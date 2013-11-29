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

//получение списка специализаций
$obQuery = $obClinics->createQuery();
$obQuery->builder()->from('estelife_specializations');
$arResult['specializations'] = $obQuery->select()->all();


$obGet=new VArray($_GET);

if (!$obGet->blank('spec')){
	//Получение вида услуги
	$obQuery = $obClinics->createQuery();
	$obQuery->builder()->from('estelife_services')->filter()
		->_eq('specialization_id', intval($obGet->one('spec')));
	$arResult['service'] = $obQuery->select()->all();

}

if (!$obGet->blank('spec') && !$obGet->blank('service')){
	//Получение типов услуг
	$obQuery = $obClinics->createQuery();
	$obQuery->builder()->from('estelife_service_concreate')->filter()
		->_eq('specialization_id',  intval($obGet->one('spec')))
		->_eq('service_id',  intval($obGet->one('service')));
	$arResult['concreate'] = $obQuery->select()->all();
}

$arResult['filter']=array(
	'city'=>intval($obGet->one('city',0)),
	'direction'=>intval($obGet->one('direction',0)),
	'date_from'=>$obGet->one('date_from',mb_strtolower(\core\types\VDate::date(),'utf-8')),
	'date_to'=>$obGet->one('date_to','')
);

$this->IncludeComponentTemplate();