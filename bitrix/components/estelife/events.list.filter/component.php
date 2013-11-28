<?php
use core\database\VDatabase;
use core\types\VArray;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

CModule::IncludeModule("iblock");
CModule::IncludeModule("estelife");

$obClinics = VDatabase::driver();

//Получение списка стран
$arSelect = Array("ID", "NAME");
$arFilter = Array("IBLOCK_ID"=>15, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
$obCountries = CIBlockElement::GetList(Array("NAME"=>"ASC"), $arFilter, false, false, $arSelect);

while($res = $obCountries->Fetch()) {
	$arResult['countries'][] = $res;
}

//получение списка специализаций
$obQuery = $obClinics->createQuery();
$obQuery->builder()->from('estelife_specializations');
$arResult['specializations'] = $obQuery->select()->all();

$obGet=new VArray($_GET);

if (!$obGet->blank('country')){
	//получаем города по стране
	$arSelect = Array("ID", "NAME");
	$arFilter = Array("IBLOCK_ID"=>16, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "PROPERTY_COUNTRY" => intval($obGet->one('country')));
	$obCity= CIBlockElement::GetList(Array("NAME"=>"ASC"), $arFilter, false, false, $arSelect);

	while($res = $obCity->Fetch()) {
		$arResult['cities'][] = $res;
	}
}

$arResult['filter']=array(
	'country'=>intval($obGet->one('country',0)),
	'city'=>intval($obGet->one('city',0)),
	'direction'=>intval($obGet->one('direction',0)),
	'type'=>intval($obGet->one('type',0)),
	'date_from'=>$obGet->one('date_from',mb_strtolower(\core\types\VDate::date(),'utf-8')),
	'date_to'=>$obGet->one('date_to','')
);

$this->IncludeComponentTemplate();