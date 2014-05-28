<?php
use core\database\VDatabase;
use core\types\VArray;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");
CModule::IncludeModule("estelife");
$obGet=new VArray($_GET);

$arResult['link']='/implants/';
$arResult['find_title']='Поиск имплантатов';
$arResult['find']='Найти имплантат';
$arResult['filter_access']=array(
	'name'=>true,
	'company_name'=>true,
	'type'=>true,
	'countries'=>true
);
$session = new \filters\decorators\VThreads();
$arFilterParams = $session->getParams();


//Получение списка стран, которые есть только в препаратах
$obCountries = VDatabase::driver();
$obQuery = $obCountries->createQuery();
$obQuery->builder()->from('estelife_implants','ep');
$obJoin = $obQuery->builder()->join();
$obJoin->_left()
	->_from('ep', 'company_id')
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
$obQuery->builder()
	->sort('ct.NAME', 'asc')
	->group('ct.ID')
	->filter()
		->_eq('type_id',$nType);
$arResult['countries'] = $obQuery->select()->all();

//Получение типов аппаратов
$obQuery = $obCountries->createQuery();
$obQuery
	->builder()
	->from('estelife_implants_typename');
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