<?php
use core\database\VDatabase;
use core\types\VArray;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");
CModule::IncludeModule("estelife");
$obGet=new VArray($_GET);

//Получение списка городов
$obCities = VDatabase::driver();
$obQuery = $obCities->createQuery();
$obQuery->builder()->from('estelife_events','ee');
$obJoin = $obQuery->builder()->join();
$obJoin->_left()
	->_from('ee', 'id')
	->_to('estelife_company_events', 'event_id', 'ece');
$obJoin->_left()
	->_from('ee', 'id')
	->_to('estelife_event_types', 'event_id', 'eet');
$obJoin->_left()
	->_from('ece', 'company_id')
	->_to('estelife_company_geo', 'company_id', 'ecg');
$obJoin->_left()
	->_from('ecg','city_id')
	->_to('iblock_element','ID','ct')
	->_cond()->_eq('ct.IBLOCK_ID',16);
$obQuery->builder()
	->field('ct.ID','ID')
	->field('ct.NAME','NAME');

$obFilter=$obQuery->builder()->filter();
$obFilter->_eq('eet.type', 3);
$obQuery->builder()->group('ct.ID');
$obQuery->builder()->sort('ct.NAME', 'asc');
$arResult['cities'] = $obQuery->select()->all();


$arResult['filter']=array(
	'city'=>intval($obGet->one('city', $_COOKIE['estelife_city'])),
	'name'=>strip_tags(trim($obGet->one('name',''))),
);

$arResult['count'] = \bitrix\ERESULT::$DATA['count'];

$arResult['empty']=false;
foreach ($arResult['filter'] as $val){
	if (($val=='' && $val==0) || $val=='all')
		continue;
	$arResult['empty']=true;
}

$this->IncludeComponentTemplate();