<?php
use core\database\VDatabase;
use core\types\VString;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");
CModule::IncludeModule("estelife");

$obProfessional=VDatabase::driver();
$sProfessionalName=null;
$nProfessionalId=null;

$nProfessionalId =  (isset($arParams['ID'])) ?
	intval($arParams['ID']) : 0;


//Получаем данные по мероприятию
$obQuery = $obProfessional->createQuery();
$obQuery->builder()->from('estelife_professionals', 'ep');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ep','user_id')
	->_to('user','ID','u');
$obJoin->_left()
	->_from('ep','country_id')
	->_to('iblock_element','ID','ct')
	->_cond()->_eq('ct.IBLOCK_ID',15);
$obQuery->builder()
	->field('ep.short_description','short_description')
	->field('ep.full_description','full_description')
	->field('ep.image_id','image_id')
	->field('ep.country_id','country_id')
	->field('ct.NAME','country_name')
	->field('u.NAME', 'name')
	->field('u.LAST_NAME', 'last_name')
	->field('u.SECOND_NAME', 'second_name');

$obFilter = $obQuery->builder()->filter();
if(!empty($nProfessionalId))
	$obFilter->_eq('ep.id', $nProfessionalId);
else
	$obFilter->_eq('ep.id', 0);

$arResult['professional'] = $obQuery->select()->assoc();
$arResult['professional']['img'] = CFile::ShowImage($arResult['professional']['image_id'],227, 158, 'alt='.$arResult['professional']['name']);

$arResult['professional']['short_description'] = html_entity_decode($arResult['professional']['short_description'],ENT_QUOTES);
$arResult['professional']['full_description'] = html_entity_decode($arResult['professional']['full_description'],ENT_QUOTES);

if (!empty($arResult['professional']['last_name']))
	$arResult['professional']['name']=$arResult['professional']['last_name'].' '.$arResult['professional']['name'].' '.$arResult['professional']['second_name'];

//Получение клиник
$obQuery = $obProfessional->createQuery();
$obQuery->builder()->from('estelife_professionals_clinics', 'epc');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('epc','clinic_id')
	->_to('estelife_clinics','id','ec');
$obQuery->builder()
	->field('ec.id','id')
	->field('ec.name','name');
$obFilter->_eq('epc.professional_id', $nProfessionalId);

$arClinics=$obQuery->select()->all();
if (!empty($arClinics)){
	foreach ($arClinics as $val){
		$val['link']='/cl'.$val['id'].'/';
		$arResult['professional']['clinics'][]=$val;
	}
}
unset($arClinics);

//Получение мероприятий
$obQuery = $obProfessional->createQuery();
$obQuery->builder()->from('estelife_professional_activity', 'epa');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('epa','activity_id')
	->_to('estelife_event_activities','id','eea');
$obJoin->_left()
	->_from('eea','type_id')
	->_to('estelife_activity_types','id','eat');
$obJoin->_left()
	->_from('eea','event_id')
	->_to('estelife_events','id','ee');
$obQuery->builder()
	->field('eea.id','id')
	->field('eea.name','name')
	->field('eat.name', 'type_name')
	->field('eea.full_description','description')
	->field('eea.date','date')
	->field('ee.id', 'event_id')
	->field('ee.full_name', 'event_name');

$obFilter = $obQuery->builder()->filter();
$obFilter->_eq('epa.professional_id', $nProfessionalId);
$arActivities=$obQuery->select()->all();

if (!empty($arActivities)){
	foreach ($arActivities as $val){
		$val['date'] = ($sDate = \core\types\VDate::getDbDate($val['date'])) ?
			$sDate :
			'Уточняется';

		$val['description']=htmlspecialchars_decode($val['name'],ENT_NOQUOTES);
		$val['link_event']='/ev'.$val['event_id'].'/';
		$arResult['professional']['activities'][]=$val;
	}
}
unset($arActivities);

$APPLICATION->SetPageProperty("title", $arResult['professional']['name']);
$APPLICATION->SetPageProperty("description", 'Специалист '.$arResult['professional']['name'].'. Подробная информация.');

$this->IncludeComponentTemplate();