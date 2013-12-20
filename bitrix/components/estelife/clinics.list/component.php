<?php
use core\database\mysql\VFilter;
use core\database\VDatabase;
use core\types\VArray;
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

CModule::IncludeModule("iblock");
CModule::IncludeModule("estelife");

$obClinics = VDatabase::driver();
$obGet=new VArray($_GET);

if (isset($arParams['PAGE_COUNT']) && $arParams['PAGE_COUNT']>0)
	$arPageCount = $arParams['PAGE_COUNT'];
else
	$arPageCount = 10;

if (isset($arParams['CITY_CODE']) && !empty($arParams['CITY_CODE'])){
	//Получаем ID города по его коду
	$arSelect = Array("ID", "NAME");
	$arFilter = Array("IBLOCK_ID"=>16, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "CODE" => $arParams['CITY_CODE']);
	$obCity = CIBlockElement::GetList(Array("NAME"=>"ASC"), $arFilter, false, false, $arSelect);

	while($res = $obCity->Fetch()) {
		$arResult['city'] = $res;
	}
}

//Получение списка клиник
$obQuery = $obClinics->createQuery();
$obQuery->builder()->from('estelife_clinics', 'ec');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ec','city_id')
	->_to('iblock_element','ID','ct')
	->_cond()->_eq('ct.IBLOCK_ID',16);
$obJoin->_left()
	->_from('ec','metro_id')
	->_to('iblock_element','ID','mt')
	->_cond()->_eq('mt.IBLOCK_ID',17);
$obJoin->_left()
	->_from('ec', 'id')
	->_to('estelife_clinic_contacts', 'clinic_id', 'eccp')
	->_cond()->_eq('eccp.type', 'phone');
$obJoin->_left()
	->_from('ec', 'id')
	->_to('estelife_clinic_contacts', 'clinic_id', 'eccw')
	->_cond()->_eq('eccw.type', 'web');
$obJoin->_left()
	->_from('ec', 'id')
	->_to('estelife_clinic_services', 'clinic_id', 'ecs');
$obQuery->builder()
	->field('mt.ID','metro_id')
	->field('mt.NAME','metro_name')
	->field('ct.ID','city_id')
	->field('ct.NAME','city_name')
	->field('ct.CODE','city_code')
	->field('ec.id','id')
	->field('ec.dop_text','dop_text')
	->field('ec.recomended', 'recomended')
	->field('ec.address','address')
	->field('ec.name','name')
	->field('ec.logo_id','logo_id')
	->field('eccp.value', 'phone')
	->field('eccw.value', 'web');

$obFilter = $obQuery->builder()->filter();
$obFilter->_eq('ec.active', 1);
$obFilter->_eq('ec.clinic_id', 0);

if(!empty($arResult['city'])){
	$obFilter->_eq('ec.city_id', $arResult['city']['ID']);
}else if(!$obGet->blank('city')){
	$obFilter->_eq('ec.city_id', intval($obGet->one('city')));
}

if(!$obGet->blank('name')){
	$obFilter->_like('ec.name',$obGet->one('name'),VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);
}

if(!$obGet->blank('metro'))
	$obFilter->_eq('ec.metro_id', intval($obGet->one('metro')));

if(!$obGet->blank('spec'))
	$obFilter->_eq('ecs.specialization_id', intval($obGet->one('spec')));

if(!$obGet->blank('service'))
	$obFilter->_eq('ecs.service_id', intval($obGet->one('service')));

if(!$obGet->blank('concreate'))
	$obFilter->_eq('ecs.service_concreate_id', intval($obGet->one('concreate')));

if(!$obGet->blank('method'))
	$obFilter->_eq('ecs.method_id', intval($obGet->one('method')));

$obQuery->builder()->group('ec.id');
$obQuery->builder()->sort('ec.name', 'asc');
$obResult = $obQuery->select();

$obResult = $obResult->bxResult();
$obResult->NavStart($arPageCount);
$arResult['clinics']=array();

$i=0;
while($arData=$obResult->Fetch()){
	$arClinics[]=$arData['id'];
	$arData['name']=trim($arData['name']);
	$arData['link'] = '/cl'.$arData['id'].'/';

	if(!empty($arData['logo_id'])){
		$file=CFile::ShowImage($arData["logo_id"], 110, 90,'alt="'.$arData['name'].'"');
		$arData['logo']=$file;
	}

	if(!empty($arData['phone']))
		$arData['phone']=\core\types\VString::formatPhone($arData['phone']);

	if(!empty($arData['web']))
		$arData['web_short']=\core\types\VString::checkUrl($arData['web']);

	$arResult['clinics'][$arData['id']]=$arData;

	if ($i<=5){
		$arDescription[]= mb_strtolower(trim(preg_replace('#[^\w\d\s\.\,\-а-я]+#iu','',$arData['name'])),'utf-8');
	}
	$i++;
}

if (!empty($arClinics)){
	//получаем услуги
	$obQuery = $obClinics->createQuery();
	$obQuery->builder()->from('estelife_clinic_services', 'ecs');
	$obJoin=$obQuery->builder()->join();
	$obJoin->_left()
		->_from('ecs','specialization_id')
		->_to('estelife_specializations','id','es');
	$obQuery->builder()
		->field('es.name','s_name')
		->field('es.id','s_id')
		->field('ecs.clinic_id');
	$obQuery->builder()->filter()
		->_in('ecs.clinic_id', $arClinics);
	$arClinicSpecialization = $obQuery->select()->all();

//	VArray::prePrint($arClinicSpecialization);

	$arSpecialization = array();
	if (!empty($arClinicSpecialization)){
		foreach ($arClinicSpecialization as $val){
			$arSpecialization[$val['clinic_id']][] = $val['s_name'];
		}
		foreach ($arSpecialization as $key=>$val){
			$val = array_unique($val);
			$arResult['clinics'][$key]['specialization'] =  mb_strtolower(implode(', ', $val), 'utf-8');
		}
	}
}

$arResult['nav']=$obResult->GetNavPrint('', true,'text','/bitrix/templates/estelife/system/pagenav.php');

$APPLICATION->SetPageProperty("title", "Клиники");
$APPLICATION->SetPageProperty("description", implode(", ", $arDescription));
$APPLICATION->SetPageProperty("keywords", "Estelife, Акции, Клиники, ". implode(" ,", $arDescription));
$this->IncludeComponentTemplate();