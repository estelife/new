<?php
use core\database\VDatabase;
use core\types\VArray;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");
CModule::IncludeModule("estelife");
$obGet = new VArray($_GET);

//Получение списка стран
$obCities = VDatabase::driver();
$obQuery = $obCities->createQuery();
$obQuery->builder()->from('estelife_pills','ep');
$obJoin = $obQuery->builder()->join();

$obJoin->_left()
	->_from('ep', 'company_id')
	->_to('estelife_company_geo', 'company_id', 'ecg');
$obJoin->_left()
	->_from('ecg','country_id')
	->_to('iblock_element','ID','ct')
	->_cond()->_eq('ct.IBLOCK_ID',15);
$obQuery->builder()
	->field('ct.ID','ID')
	->field('ct.NAME','NAME');

$obFilter=$obQuery->builder()->filter();
$obQuery->builder()->group('ct.ID');
$obQuery->builder()->sort('ct.NAME', 'asc');
$arResult['countries'] = $obQuery->select()->all();

$arResult['filter']=array(
	'country'=>intval($obGet->one('country',0)),
	'name'=>strip_tags(trim($obGet->one('name',''))),
);

$arResult['empty']=false;
foreach ($arResult['filter'] as $val){
	if (($val=='' && $val==0) || $val=='all')
		continue;
	$arResult['empty']=true;
}

$this->IncludeComponentTemplate();