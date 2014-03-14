<?php
use core\database\VDatabase;
use core\types\VArray;
use core\types\VString;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");

$obEvent=VDatabase::driver();
$sEventName=null;
$nEventId=null;
$sHall=null;
$sDate=null;
$arResult=array();

$nEventId =  (isset($arParams['EVENT_ID'])) ?
	intval($arParams['EVENT_ID']) : 0;

$sHall =  (isset($arParams['HALL'])) ?
	trim(strip_tags($arParams['HALL'])) : '';

$nDate =  (isset($arParams['DATE'])) ?
	strtotime($arParams['DATE']) : '';

$arResult['event_id']=$nEventId;

//Получение секций по холу о событию
$obQuery=$obEvent->createQuery();
$obQuery->builder()
	->from('estelife_event_section_halls', 'esh');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('esh','section_id')
	->_to('estelife_event_sections','id','es');
$obJoin->_left()
	->_from('esh','hall_id')
	->_to('estelife_event_halls','id','eh');
$obJoin->_left()
	->_from('es','id')
	->_to('estelife_event_sections_dates','section_id','esd');
$obJoin->_left()
	->_from('es','event_id')
	->_to('estelife_events','id','ee');
$obQuery->builder()
	->field('es.name', 'section_name')
	->field('ee.full_name', 'event_name')
	->field('es.id', 'section_id')
	->field('es.number', 'number')
	->field('eh.id', 'hall_id')
	->field('eh.name', 'hall_name')
	->field('esd.time_from', 'time_from')
	->field('esd.time_to', 'time_to')
	->sort('esd.time_from','asc')
	->filter()
	->_eq('eh.translit',$sHall)
	->_eq('esd.date', date('Y-m-d', $nDate));
$arSections=$obQuery->select()->all();

$arIds=array();
if (!empty($arSections)){
	foreach ($arSections as $val){
		$nHallId=$val['hall_id'];
		$arIds[]=$val['section_id'];
		$arResult['event']=$val['event_name'];
		$arResult['hall_id']=$val['hall_id'];
		$arResult['hall']=$val['hall_name'];
		$arResult['date']=\core\types\VDate::date($nDate, 'j F');
		$val['time_to']=preg_replace('/(.*)\:[0-9]{2}$/','$1',$val['time_to']);
		$val['time_from']=preg_replace('/(.*)\:[0-9]{2}$/','$1',$val['time_from']);
		$arResult['sections'][$val['section_id']]=$val;
	}
}

//получение событий для секции
if (!empty($arIds)){
	$obQuery=$obEvent->createQuery();
	$obQuery->builder()
		->from('estelife_event_activities', 'ea');
	$obFilter=$obQuery->builder()
		->field('ea.section_id', 'section_id')
		->field('ea.name', 'activity_name')
		->field('ea.id', 'id')
		->filter()
		->_eq('ea.hall_id',$nHallId)
		->_eq('ea.event_id', $nEventId);
	$obFilter->_or()->_in('ea.section_id', $arIds);
	$obFilter->_or()->_isNull('ea.section_id');
	$arActivities=$obQuery->select()->all();
}

$arIdEvents=array();
$arNewActivities=array();
if (!empty($arActivities)){
	foreach ($arActivities as $val){
		$arIdEvents[]=$val['id'];
		$arNewActivities[$val['id']]=$val;
	}
}

if (!empty($arIdEvents)){
	$obQuery=$obEvent->createQuery();
	$obQuery->builder()
		->from('estelife_professional_activity', 'epa');
	$obJoin=$obQuery->builder()->join();
	$obJoin->_left()
		->_from('epa','professional_id')
		->_to('estelife_professionals','id','ep');
	$obJoin->_left()
		->_from('ep','user_id')
		->_to('user','ID','u');
	$obJoin->_left()
		->_from('ep','country_id')
		->_to('iblock_element','ID','ct')
		->_cond()->_eq('ct.IBLOCK_ID',15);
	$obFilter=$obQuery->builder()
		->field('u.NAME', 'name')
		->field('u.LAST_NAME', 'last_name')
		->field('u.SECOND_NAME', 'second_name')
		->field('ct.NAME','country_name')
		->field('ct.ID','country_id')
		->field('ep.image_id', 'image_id')
		->field('ep.id', 'professional_id')
		->field('ep.short_description', 'description')
		->field('epa.activity_id', 'activity_id')
		->filter()
		->_in('epa.activity_id',$arIdEvents);
	$arProfessionals=$obQuery->select()->all();
}

if (!empty($arProfessionals)){
	foreach ($arProfessionals as $val){
		if(!empty($val['image_id'])){
			$file=CFile::ShowImage($val["image_id"], 84, 80,'alt="'.$val['name'].'"');
			$val['logo']=$file;
		}
		if (!empty($val['last_name']))
			$val['name']=$val['last_name'].' '.$val['name'].' '.$val['second_name'];

		$val['link']='/pf'.$val['professional_id'].'/';
		$arNewActivities[$val['activity_id']]['events'][]=$val;
	}
}

if (!empty($arNewActivities)){
	foreach ($arNewActivities as $val){
		if (empty($val['section_id']))
			$val['section_id']=0;

		$arResult['sections'][$val['section_id']]['activities']=$val;
	}
}
unset($arActivities, $arNewActivities, $arProfessionals);



$sSeoName =''.$arResult['event'].', '.$arResult['hall'].'';

$arResult['app']['name']=trim(strip_tags(html_entity_decode($sSeoName, ENT_QUOTES, 'utf-8')));
$arResult['app']['seo_name']=VString::pregStrSeo($arResult['app']['name']);

$APPLICATION->SetPageProperty("title", $arResult['app']['seo_name']);
$APPLICATION->SetPageProperty("keywords", "Estelife, Секции, ".$arResult['app']['seo_name']);


$this->IncludeComponentTemplate();