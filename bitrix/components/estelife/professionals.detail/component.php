<?php
use core\database\VDatabase;
use core\types\VArray;
use core\types\VString;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");
CModule::IncludeModule("estelife");

$obProffesional=VDatabase::driver();
$sProfessionalName=null;
$nProfessionalId=null;

$nProfessionalId =  (isset($arParams['ID'])) ?
	intval($arParams['ID']) : 0;

//Получаем данные по мероприятию
$obQuery = $obProffesional->createQuery();
$obQuery->builder()->from('estelife_professionals', 'ep');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ep','country_id')
	->_to('iblock_element','ID','ct')
	->_cond()->_eq('ct.IBLOCK_ID',15);
$obJoin->_left()
	->_from('ep','city_id')
	->_to('iblock_element','ID','cty')
	->_cond()->_eq('cty.IBLOCK_ID',16);
$obQuery->builder()
	->field('ep.short_description','short_description')
	->field('ep.full_description','full_description')
	->field('ep.image_id','image_id')
	->field('ct.NAME','country_name')
	->field('cty.NAME','city_name');

$obFilter = $obQuery->builder()->filter();

if(!is_null($nProfessionalId))
	$obFilter->_eq('ee.id', $nProfessionalId);
else
	$obFilter->_eq('ee.id', 0);

$arResult['professional'] = $obQuery->select()->assoc();
$arResult['professional']['img'] = CFile::ShowImage($arResult['professional']['image_id'],280, 120, 'alt='.$arResult['professional']['name']);
$arResult['professional']['short_text'] = htmlspecialchars_decode($arResult['professional']['short_description'],ENT_NOQUOTES);
$arResult['professional']['detail_text'] = htmlspecialchars_decode($arResult['professional']['full_description'],ENT_NOQUOTES);

//Получение клиник
$obQuery = $obProffesional->createQuery();
$obQuery->builder()->from('estelife_professionals_clinics', 'epc');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('epc','clinic_id')
	->_to('estelife_clinics','id','ec');
$obQuery->builder()
	->field('ec.id','clinic_id')
	->field('ec.name','clinic_name');

$obFilter = $obQuery->builder()->filter();


$obFilter->_eq('epc.professional_id', $nProfessionalId);

$arResult['clinics'] = $obQuery->select()->all();


//Получение мероприятий
$obQuery = $obProffesional->createQuery();
$obQuery->builder()->from('estelife_professional_activity', 'epa');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('epa','activity_id')
	->_to('estelife_event_activities','id','eea');
$obQuery->builder()
	->field('eea.id','activity_id')
	->field('eea.short_description','activity_short_description')
	->field('eea.short_description','activity_short_description')
	->field('eea.date','activity_date');

$obFilter = $obQuery->builder()->filter();

$obFilter->_eq('epa.professional_id', $nProfessionalId);

$arResult['activities'] = $obQuery->select()->all();


/*$APPLICATION->SetPageProperty("title", $arResult['event']['seo_title']);
$APPLICATION->SetPageProperty("description", $arResult['event']['seo_description']);*/

$this->IncludeComponentTemplate();


