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


$obGet=new VArray($_GET);

//получаем метро по городу
if (!$obGet->blank('city')){
	$arSelect=Array("ID", "NAME");
	$arFilter=Array(
		"IBLOCK_ID"=>17,
		"ACTIVE_DATE"=>"Y",
		"ACTIVE"=>"Y",
		"PROPERTY_CITY" => intval($obGet->one('city'))
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
$nService=intval($obGet->one('service',0));
$nSpec=intval($obGet->one('spec',0));

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

$arResult['filter']=$obGet->all();
$this->IncludeComponentTemplate();