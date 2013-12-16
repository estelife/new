<?php
use core\database\VDatabase;
use core\types\VArray;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");
CModule::IncludeModule("estelife");


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

$this->IncludeComponentTemplate();