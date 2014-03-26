<?php
use core\database\mysql\VFilter;
use core\database\VDatabase;
use core\exceptions\VFormException;
use core\types\VString;
use core\utils\forms\VForm;
use geo\VGeo;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

CModule::IncludeModule("estelife");

if (isset($arParams['clinic_id']))
	$nClinicId=intval($arParams['clinic_id']);
else
	$nClinicId = 0;

$arResult = array();
$obDriver = VDatabase::driver();
//Получение списка отзывов
$obQuery = $obDriver->createQuery();
$obQuery->builder()
	->from('estelife_clinic_reviews', 'ecr');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ecr', 'problem_id')
	->_to('estelife_clinic_problems', 'id', 'ecp');
$obJoin->_left()
	->_from('ecr', 'specialist_id')
	->_to('estelife_professionals', 'id', 'ep');
$obJoin->_left()
	->_from('ep', 'user_id')
	->_to('user', 'ID', 'u');
$obJoin->_left()
	->_from('ecr', 'clinic_id')
	->_to('estelife_clinics', 'id', 'ec');
$obJoin->_left()
	->_from('ecr', 'user_id')
	->_to('user', 'ID', 'uu');
$obJoin->_left()
	->_from('ecr', 'id')
	->_to('estelife_clinic_user_rating', 'review_id', 'ecur');
$obQuery->builder()
	->field('ecr.id')
	->field('ecr.date_add')
	->field('ecr.active')
	->field('ecr.date_visit')
	->field('ecr.specialist_name')
	->field('ecr.problem_name')
	->field('ecr.is_recomended')
	->field('ecr.date_moderate')
	->field('ecr.positive_description')
	->field('ecr.negative_description')
	->field('ecr.date_moderate')
	->field('ep.id', 'professional_id')
	->field('ec.name', 'clinic_name')
	->field('u.NAME', 'name')
	->field('u.LAST_NAME', 'last_name')
	->field('u.SECOND_NAME', 'second_name')
	->field('u.LOGIN', 'login')
	->field('ec.title', 'problem')
	->field('ecur.rating_doctor')
	->field('ecur.rating_stuff')
	->field('ecur.rating_service')
	->field('ecur.rating_quality')
	->sort('ecr.date_add','desc')
	->filter()
	->_eq('ecr.clinic_id', $nClinicId)
	->_eq('ecr.active', 1);
$arResult['reviews'] = $obQuery->select()->all();
$arResult['count_good'] = 0;

$i=1;
if (!empty($arResult['reviews'])){
	foreach ($arResult['reviews'] as &$val){

		$val['rating'] = round(($val['rating_doctor']+$val['rating_stuff']+$val['rating_service']+$val['rating_quality'])/4, 1);
		$val['temp_rating'] = round($val['rating']);
		$val['stars'] = '';
		for ($j=1; $j<=5; $j++){
			if ($j<=$val['temp_rating'])
				$val['stars'] .= '<span class="active"></span>';
			else
				$val['stars'] .= '<span></span>';
		}

		if (empty($val['date_moderate']))
			$val['moderate'] = 1;

		if (!empty($val['specialist_name'])){
			$val['professional_name']=$val['specialist_name'];
			$val['professional_link'] = '#';
		}else{
			if (empty($val['name']))
				$val['professional_name']=$val['login'];
			elseif (empty($val['last_name']))
				$val['professional_name']=$val['name'];
			else
				$val['professional_name']=$val['last_name'].' '.$val['name'].' '.$val['second_name'];
			$val['professional_link'] = '/pf'.$val['professional_id'].'/';
		}


		$val['date_visit'] = date('d.m.Y', strtotime($val['date_visit']));
		$val['date_add'] = date('d.m.Y', strtotime($val['date_add']));

		if (!empty($val['problem_name']))
			$val['problem'] = $val['problem_name'];

		if (empty($val['user_name']))
			$val['user_name']=$val['user_login'];
		elseif (empty($val['user_last_name']))
			$val['user_name']=$val['user_name'];
		else
			$val['user_name']=$val['user_last_name'].' '.$val['user_name'].' '.$val['user_second_name'];

		if ($val['is_recomended'] == 1)
			$arResult['count_good']++;
		$val['number'] = $i;
		$i++;
	}

	$arResult['count_reviews'] = count($arResult['reviews']);

	//Получение лучшего врача
	$obQuery = $obDriver->createQuery();
	$obQuery->builder()
		->from('estelife_professionals', 'ep');
	$obJoin=$obQuery->builder()->join();
	$obJoin->_left()
		->_from('ep', 'user_id')
		->_to('user', 'ID', 'u');
	$obJoin->_left()
		->_from('ep', 'id')
		->_to('estelife_professionals_clinics', 'professional_id', 'epc');
	$obQuery->builder()
		->sort('ep.rating', 'desc')
		->field('ep.id')
		->field('ep.image_id')
		->field('ep.rating')
		->field('u.NAME', 'name')
		->field('u.LAST_NAME', 'last_name')
		->field('u.SECOND_NAME', 'second_name')
		->slice(0,1)
		->filter()
		->_eq('epc.clinic_id', $nClinicId);
	$arSpecialist = $obQuery->select()->all();
	if (!empty($arSpecialist)){
		foreach ($arSpecialist as &$val){
			if (empty($val['name']))
				$val['name'] = $val['login'];
			elseif (empty($val['last_name']))
				$val['name'] = $val['name'];
			else
				$val['name'] = $val['last_name'].' '.$val['name'].' '.$val['second_name'];
			$val['professional_link'] = '/pf'.$val['id'].'/';
			if(!empty($val['image_id'])){
				$file=CFile::ShowImage($val['image_id'], 93, 127,'alt="'.$val['name'].'"');
				$val['logo']=$file;
			}

			$val['name'] = str_replace(' ', '<br />', $val['name']);
			$nStar = round($val['rating']);
			$val['stars'] = '';
			for ($j=1; $j<=5; $j++){
				if ($j<=$nStar)
					$val['stars'] .= '<span class="active"></span>';
				else
					$val['stars'] .= '<span></span>';
			}
			$arResult['specialist'] = $val;
		}
	}
}

	//Получение общего рейтинга
	$obQuery = $obDriver->createQuery();
	$obQuery->builder()
		->from('estelife_clinic_rating', 'ep')
		->filter()
		->_eq('clinic_id', $nClinicId);
	$arResult['clinic_rating'] = $obQuery->select()->assoc();

	$arResult['clinic_rating']['temp_rating_doctor'] = round($arResult['clinic_rating']['rating_doctor']);
	$arResult['clinic_rating']['stars_doctor'] = '';
	for ($j=1; $j<=5; $j++){
		if ($j<=$arResult['clinic_rating']['temp_rating_doctor'])
			$arResult['clinic_rating']['stars_doctor'] .= '<spanclass="active"></span>';
		else
			$arResult['clinic_rating']['stars_doctor'] .= '<span></span>';
	}

	$arResult['clinic_rating']['temp_rating_quality'] = round($arResult['clinic_rating']['rating_quality']);
	$arResult['clinic_rating']['stars_quality'] = '';
	for ($j=1; $j<=5; $j++){
		if ($j<=$arResult['clinic_rating']['temp_rating_quality'])
			$arResult['clinic_rating']['stars_quality'] .= '<span class="active"></span>';
		else
			$arResult['clinic_rating']['stars_quality'] .= '<span href="#"></span>';
	}

	$arResult['clinic_rating']['temp_rating_stuff'] = round($arResult['clinic_rating']['rating_stuff']);
	$arResult['clinic_rating']['stars_stuff'] = '';
	for ($j=1; $j<=5; $j++){
		if ($j<=$arResult['clinic_rating']['temp_rating_stuff'])
			$arResult['clinic_rating']['stars_stuff'] .= '<span class="active"></span>';
		else
			$arResult['clinic_rating']['stars_stuff'] .= '<span href="#"></span>';
	}

	$arResult['clinic_rating']['temp_rating_service'] = round($arResult['clinic_rating']['rating_service']);
	$arResult['clinic_rating']['stars_service'] = '';
	for ($j=1; $j<=5; $j++){
		if ($j<=$arResult['clinic_rating']['temp_rating_service'])
			$arResult['clinic_rating']['stars_service'] .= '<span class="active"></span>';
		else
			$arResult['clinic_rating']['stars_service'] .= '<span></span>';
	}

	$arResult['clinic_rating']['temp_rating_full'] = round($arResult['clinic_rating']['rating_full']);
	$arResult['clinic_rating']['stars_full'] = '';
	for ($j=1; $j<=5; $j++){
		if ($j<=$arResult['clinic_rating']['temp_rating_full'])
			$arResult['clinic_rating']['stars_full'] .= '<span class="active"></span>';
		else
			$arResult['clinic_rating']['stars_full'] .= '<span></span>';
	}



$this->IncludeComponentTemplate();
