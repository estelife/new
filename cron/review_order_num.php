<?php
$_SERVER["DOCUMENT_ROOT"] = realpath(__DIR__.'/../');
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
@set_time_limit(0);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('estelife');

$obQuery = \core\database\VDatabase::driver()->createQuery();
$obQuery->builder()
	->from('estelife_clinic_reviews')
	->field('date_add')
	->field('clinic_id')
	->field('id')
	->sort('date_add', 'asc');
$arReviews = $obQuery->select()->all();

if (!empty($arReviews)) {
	$arNums = array();

	foreach ($arReviews as $arReview) {
		if (!isset($arNums[$arReview['clinic_id']]))
			$arNums[$arReview['clinic_id']] = 0;

		$arNums[$arReview['clinic_id']] ++;
		$obQuery->builder()
			->from('estelife_clinic_reviews')
			->value('order_num', $arNums[$arReview['clinic_id']])
			->filter()
			->_eq('id', $arReview['id']);
		$obQuery->update();
	}
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");