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

$obGet=new VArray($_GET);

if (!$obGet->blank('country') || intval($_COOKIE['estelife_country'])>0){
	$nCountry = intval($obGet->one('country',$_COOKIE['estelife_country']));
	//получаем города по стране
	$arSelect = Array("ID", "NAME");
	$arFilter = Array("IBLOCK_ID"=>16, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "PROPERTY_COUNTRY" => $nCountry);
	$obCity= CIBlockElement::GetList(Array("NAME"=>"ASC"), $arFilter, false, false, $arSelect);

	while($res = $obCity->Fetch()) {
		$arResult['cities'][] = $res;
	}
}

$arResult['filter']=array(
	'country'=>intval($obGet->one('country',$_COOKIE['estelife_country'])),
	'city'=>intval($obGet->one('city',$_COOKIE['estelife_city'])),
	'direction'=>intval($obGet->one('direction',0)),
	'type'=>intval($obGet->one('type',0)),
	'date_from'=>$obGet->one('date_from', date('d.m.y',time())),
	'date_to'=>$obGet->one('date_to',''),
	'name'=>strip_tags(trim($obGet->one('name',''))),
);

$arResult['empty']=false;
foreach ($arResult['filter'] as $key=>$val){
	if (($val=='' && $val==0) || $val=='all')
		continue;
	if ($key=='date_from' && $val==date('d.m.y',time()))
		continue;
	$arResult['empty']=true;
}

$this->IncludeComponentTemplate();