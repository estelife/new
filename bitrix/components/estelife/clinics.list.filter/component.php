<?php
use core\database\VDatabase;
use core\types\VArray;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");
CModule::IncludeModule("estelife");

$obDriver = VDatabase::driver();

//Получение списка городов
$arSelect = Array("ID", "NAME");
$arFilter = Array("IBLOCK_ID"=>16, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
$obCities = CIBlockElement::GetList(Array("NAME"=>"ASC"), $arFilter, false, false, $arSelect);
while($res = $obCities->Fetch()) {
	$arResult['cities'][] = $res;
}

//получение списка специализаций
$obQuery = $obDriver->createQuery();
$obQuery->builder()->from('estelife_specializations');
$arResult['specializations'] = $obQuery->select()->all();

$session = new \filters\decorators\VClinic();
$arFilterParams = $session->getParams();


//получаем метро по городу
if (!$obFilter->blank('city') || isset($_COOKIE['estelife_city'])){

	if(!empty($arFilterParams['city']) && $arFilterParams['city']!= 'all'){
		$nCity = $arFilterParams['city'];
	}else{
		$nCity=intval($obFilter->one('city',$_COOKIE['estelife_city']));
		$arFilterParams['city'] = $nCity;
	}

	$obFilter->set('city',$nCity);

	$arSelect=Array("ID", "NAME");
	$arFilter=Array(
		"IBLOCK_ID"=>17,
		"ACTIVE_DATE"=>"Y",
		"ACTIVE"=>"Y",
		"PROPERTY_CITY" =>$nCity
	);
	$obMetro=CIBlockElement::GetList(
		Array("NAME"=>"ASC"),
		$arFilter,
		false,
		false,
		$arSelect
	);
	while($res=$obMetro->Fetch()){
		$arResult['metro'][] = $res;
	}
}

$obMethod=null;
$nService=intval($arFilterParams['service']);
$nSpec=intval($arFilterParams['spec']);

//Получение вида услуги
if($nSpec>0){
	$obQuery=$obDriver->createQuery();
	$obQuery->builder()->from('estelife_services')->filter()
		->_eq('specialization_id',$nSpec);

	$arResult['service']=$obQuery->select()->all();

	$obMethod=$obDriver->createQuery();
	$obMethod->builder()->filter()->_eq(
		'specialization_id',
		$nSpec
	);
}

//Получение типов услуг
if ($nSpec>0 && $nService>0){
	$obQuery=$obDriver->createQuery();
	$obQuery->builder()->from('estelife_service_concreate')->filter()
		->_eq('specialization_id',  $nSpec)
		->_eq('service_id',  $nService);

	$arResult['concreate']=$obQuery->select()->all();

	$obMethod=(!$obMethod) ?
		$obDriver->createQuery() :
		$obMethod;
	$obMethod->builder()->filter()->_eq(
		'service_id',
		$nService
	);
}

if($obMethod){
	$obMethod->builder()->from('estelife_methods');
	$arResult['methods']=$obMethod->select()->all();
}

$arResult['filter'] = $arFilterParams;

$arResult['empty']=false;
foreach ($arResult['filter'] as $val){
	if (($val=='' && $val==0) || $val=='all')
		continue;
	$arResult['empty']=true;
}

$this->IncludeComponentTemplate();