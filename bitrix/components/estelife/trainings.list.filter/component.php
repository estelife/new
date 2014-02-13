<?php
use core\database\VDatabase;
use core\types\VArray;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");
CModule::IncludeModule("estelife");

$obClinics = VDatabase::driver();

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

$obGet=new VArray($_GET);

$session = new \filters\VTrainingsFilter();
$arFilterParams = $session->getParams();

/*$arResult['filter']=array(
	'city'=>intval($obGet->one('city', $_COOKIE['estelife_city'])),
	'direction'=>intval($obGet->one('direction',0)),
	'date_from'=>$obGet->one('date_from', date('d.m.y',time())),
	'date_to'=>$obGet->one('date_to','')
);*/

$arResult['filter'] = $arFilterParams;

$arResult['count'] = \bitrix\ERESULT::$DATA['count'];

$arResult['empty']=false;
foreach ($arResult['filter'] as $key=>$val){
	if (($val=='' && $val==0) || $val=='all')
		continue;
	if ($key=='date_from' && $val==date('d.m.y',time()))
		continue;
	$arResult['empty']=true;
}

$this->IncludeComponentTemplate();