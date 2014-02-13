<?php
use core\database\VDatabase;
use core\types\VArray;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");
CModule::IncludeModule("estelife");
$obGet=new VArray($_GET);

$session = new \filters\VPreparationsFilter();
$arFilterParams = $session->getParams();
$obSession = new \filters\VSession('preparations');

if (isset($arParams['TYPE']) && $arParams['TYPE']>0)
	$nType=intval($arParams['TYPE']);

if ($nType==1){
	$arResult['link']='/preparations/';
	$arResult['find_title']='Поиск препаратов';
	$arResult['find']='Найти препарат';
	$arResult['filter_access']=array(
		'name'=>true,
		'company_name'=>true,
		'type'=>true,
		'countries'=>true
	);
}elseif ($nType==2){
	$arResult['link']='/threads/';
	$arResult['find_title']='Поиск нитей';
	$arResult['find']='Найти нить';
	$arResult['filter_access']=array(
		'name'=>true,
		'company_name'=>true,
		'type'=>false,
		'countries'=>true
	);
}else{
	$arResult['link']='/implants/';
	$arResult['find_title']='Поиск имплантатов';
	$arResult['find']='Найти имплантат';
	$arResult['filter_access']=array(
		'name'=>true,
		'company_name'=>true,
		'type'=>false,
		'countries'=>true
	);
}


//Получение списка стран, которые есть только в препаратах
$obCountries = VDatabase::driver();
$obQuery = $obCountries->createQuery();
$obQuery->builder()->from('estelife_pills','ep');
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

/*$arResult['filter']=array(
	'country'=>intval($obGet->one('country',0)),
	'type'=>intval($obGet->one('type',0)),
	'name'=>strip_tags(trim($obGet->one('name',''))),
	'company_name'=>strip_tags(trim($obGet->one('company_name',''))),
);*/

$arResult['filter'] = $arFilterParams;

if(!isset($arResult['filter']['name'])){
	$obSession->setParam('name','');
	$arResult['filter']['name'] = '';
}

$arResult['count'] = \bitrix\ERESULT::$DATA['count'];
$arResult['empty']=false;

foreach ($arResult['filter'] as $val){
	if (($val=='' && $val==0) || $val=='all')
		continue;
	$arResult['empty']=true;
}

$this->IncludeComponentTemplate();