<?php
$_SERVER["DOCUMENT_ROOT"] = realpath(__DIR__.'/../');
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
@set_time_limit(0);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('estelife');

$obQuery = \core\database\VDatabase::driver()->createQuery();
$obJoin = $obQuery->builder()
	->from('estelife_clinic_reviews', 'review')
	->field('review.id')
	->field('review.specialist_id')
	->field('clinic.city_id')
	->field('clinic.id', 'clinic_id')
	->field('rt.rating_doctor')
	->field('rt.rating_stuff')
	->field('rt.rating_service')
	->field('rt.rating_quality')
	->join();
$obJoin->_left()
	->_from('review', 'clinic_id')
	->_to('estelife_clinics', 'id', 'clinic');
$obJoin->_left()
	->_from('review', 'id')
	->_to('estelife_clinic_user_rating', 'review_id', 'rt');

$arReviews = $obQuery->select()->all();

if (!empty($arReviews)) {
	$arCities = array();
	$arCounts = array();

	$arRatingDoctor = array();
	$arRatingStuff = array();
	$arRatingService = array();
	$arRatingQuality = array();

	$arSpecialistRatings = array();
	$arSpecialistCounts = array();

	foreach($arReviews as $arReview) {
		if (!isset($arCities[$arReview['city_id']]) && !in_array($arReview['clinic_id'], $arCities[$arReview['city_id']]))
			$arCities[$arReview['city_id']][] = $arReview['clinic_id'];

		$arRatingDoctor[$arReview['clinic_id']][] = floatval($arReview['rating_doctor']);
		$arRatingStuff[$arReview['clinic_id']][] = floatval($arReview['rating_stuff']);
		$arRatingService[$arReview['clinic_id']][] = floatval($arReview['rating_service']);
		$arRatingQuality[$arReview['clinic_id']][] = floatval($arReview['rating_quality']);

		if (!isset($arCounts[$arReview['city_id']][$arReview['clinic_id']]))
			$arCounts[$arReview['city_id']][$arReview['clinic_id']] = 0;

		$arCounts[$arReview['city_id']][$arReview['clinic_id']] ++;

		if (empty($arReview['specialist_id']))
			continue;

		$arSpecialistRatings[$arReview['specialist_id']][] = floatval($arReview['rating_doctor']);

		if (!isset($arSpecialistCounts[$arReview['specialist_id']]))
			$arSpecialistCounts[$arReview['specialist_id']] = 0;

		$arSpecialistCounts[$arReview['specialist_id']] ++;
	}

	$obQuery->builder()
		->from('estelife_clinic_rating')
		->filter()
		->_ne('id', 0);
	$obQuery->delete();

	foreach($arCities as $nCityId => $arClinics) {
		$nMaxScore = pow(max(array_values($arCounts[$nCityId])), 1/5);

		foreach ($arClinics as $nClinicId) {
			$nCount = count($arRatingDoctor[$nClinicId]);

			$nScoreDoctor = array_sum($arRatingDoctor[$nClinicId]) / $nCount;
			$nScoreStuff = array_sum($arRatingStuff[$nClinicId]) / $nCount;
			$nScoreService = array_sum($arRatingService[$nClinicId]) / $nCount;
			$nScoreQuality = array_sum($arRatingQuality[$nClinicId]) / $nCount;

			$nRatingDoctor = (log($nMaxScore, $nCount) + $nScoreDoctor) / 2;
			$nRatingStuff = (log($nMaxScore, $nCount) + $nScoreStuff) / 2;
			$nRatingService = (log($nMaxScore, $nCount) + $nScoreService) / 2;
			$nRatingQuality = (log($nMaxScore, $nCount) + $nScoreQuality) / 2;

			if(is_nan($nRatingDoctor))
				$nRatingDoctor = 0;

			if(is_nan($nRatingStuff))
				$nRatingStuff = 0;

			if(is_nan($nRatingService))
				$nRatingService = 0;

			if(is_nan($nRatingQuality))
				$nRatingQuality = 0;

			$obQuery->builder()
				->from('estelife_clinic_rating')
				->value('clinic_id', $nClinicId)
				->value('rating_doctor', $nRatingDoctor)
				->value('rating_stuff', $nRatingStuff)
				->value('rating_service', $nRatingService)
				->value('rating_quality', $nRatingQuality)
				->value('rating_full', ($nRatingDoctor + $nRatingStuff + $nRatingService + $nRatingQuality) / 4);
			$obQuery->insert();
		}
	}

	foreach($arSpecialistRatings as $nSpecialistId => $arRatings) {
		$nMaxScore = pow(max(array_values($arSpecialistCounts)), 1/5);
		$nCount = count($arRatings);
		$nScore = array_sum($arRatings) / $nCount;
		$nRating = (log($nMaxScore, $nCount) + $nScore) / 2;

		if(is_nan($nRating))
			$nRating = 0;

		$obQuery->builder()
			->from('estelife_professionals')
			->value('rating', $nRating)
			->filter()
			->_eq('id', $nSpecialistId);

		$obQuery->update();
	}
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");