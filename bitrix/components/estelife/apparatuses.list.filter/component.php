<?php
use core\database\VDatabase;
use core\types\VArray;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");
CModule::IncludeModule("estelife");
$obGet=new VArray($_GET);

$session = new \filters\decorators\VApparatuses();
$arFilterParams = $session->getParams();

//получение стран, которые есть только в аппаратах
$obApp = VDatabase::driver();
$obQuery = $obApp->createQuery();
$obQuery->builder()->from('estelife_apparatus','ap');
$obJoin = $obQuery->builder()->join();
$obJoin->_left()
	->_from('ap', 'company_id')
	->_to('estelife_companies', 'id', 'ec');

$obJoin->_left()
	->_from('ec', 'id')
	->_to('estelife_company_geo', 'company_id', 'ecg');

$obJoin->_left()
	->_from('ecg','country_id')
	->_to('iblock_element','ID','ct')
	->_cond()->_eq('ct.IBLOCK_ID',15);

$obQuery->builder()
	->field('ct.ID','ID')
	->field('ct.NAME','NAME');

$obQuery->builder()->group('ct.ID');
$obQuery->builder()->sort('ct.NAME', 'asc');
$arResult['countries']=$obQuery->select()->all();

//Получение типов аппаратов
$obQuery = $obApp->createQuery();
$obQuery
	->builder()
	->from('estelife_apparatus_typename');
$arResult['types'] = $obQuery->select()->all();

$arResult['filter'] = $arFilterParams;
$arResult['count'] = \bitrix\ERESULT::$DATA['count'];

$arResult['empty']=false;
foreach ($arResult['filter'] as $val){
	if (($val=='' && $val==0) || $val=='all')
		continue;
	$arResult['empty']=true;
}

$this->IncludeComponentTemplate();