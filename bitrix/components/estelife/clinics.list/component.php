<?php
use core\database\mysql\VFilter;
use core\database\VDatabase;
use core\types\VArray;
use core\types\VString;
use geo\VGeo;

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


$session = new \filters\decorators\VClinic();
$arFilterParams = $session->getParams();


if(!empty($arFilterParams['city'])){
	$arResult['city']['ID'] = $arFilterParams['city'];
}else if(!$obGet->blank('city')){
	//Получаем имя города по его ID
	$arSelect = Array("ID", "NAME", "PROPERTY_CITY");
	$arFilter = Array("IBLOCK_ID"=>16, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "ID" => intval($obGet->one('city')));
	$obCity = CIBlockElement::GetList(Array("NAME"=>"ASC"), $arFilter, false, false, $arSelect);
	$arResult['city'] = $obCity->Fetch();
}elseif (isset($arParams['CITY_CODE']) && !empty($arParams['CITY_CODE'])){
	//Получаем ID города по его коду
	$arSelect = Array("ID", "NAME","PROPERTY_CITY");
	$arFilter = Array("IBLOCK_ID"=>16, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "CODE" => $arParams['CITY_CODE']);
	$obCity = CIBlockElement::GetList(Array("NAME"=>"ASC"), $arFilter, false, false, $arSelect);
	$arResult['city'] = $obCity->Fetch();
}elseif(isset($_COOKIE['estelife_city'])){
	$arResult['city'] = VGeo::getInstance()->getGeo();
}

if (empty($arResult['city']['NAME'])){
	$arSelect = Array("ID", "NAME","PROPERTY_CITY");
	$arFilter = Array("IBLOCK_ID"=>16, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "ID" => $arResult['city']['ID']);
	$obCity = CIBlockElement::GetList(Array("NAME"=>"ASC"), $arFilter, false, false, $arSelect);
	$arResult['city'] = $obCity->Fetch();
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
$bFilterByCity=false;

if($bFilterByCity=(!empty($arFilterParams['city']) && $arFilterParams['city'] !='all')){
	$obFilter->_eq('ec.city_id', $arFilterParams['city']);
}

if(!empty($arFilterParams['name'])){
	$obSearch=new \search\VSearch();
	$obSearch->setIndex('clinics');

	if($bFilterByCity)
		$obSearch->setFilter('city',array($arFilterParams['city']));

	$arSearch=$obSearch->search($arFilterParams['name']);

	if(!empty($arSearch)){
		$arTemp=array();

		foreach($arSearch as $arValue)
			$arTemp[]=$arValue['id'];

		$obFilter->_in('ec.id',$arTemp);
	}else
		$obFilter->_eq('ec.id',0);
}

if(!empty($arFilterParams['metro'])){
	$obFilter->_eq('ec.metro_id', intval($arFilterParams['metro']));
}

if(!empty($arFilterParams['spec'])){
	$obFilter->_eq('ecs.specialization_id', intval($arFilterParams['spec']));
}

if(!empty($arFilterParams['service'])){
	$obFilter->_eq('ecs.service_id', intval($arFilterParams['service']));
}

if(!empty($arFilterParams['concreate'])){
	$obFilter->_eq('ecs.service_concreate_id', intval($arFilterParams['concreate']));
}

if(!empty($arFilterParams['method'])){
	$obFilter->_eq('ecs.method_id', intval($arFilterParams['method']));
}

$obQuery->builder()->group('ec.id');
$obQuery->builder()->sort('ec.name', 'asc');
$obResult = $obQuery->select();

$obResult = $obResult->bxResult();
$nCount = $obResult->SelectedRowsCount();
$arResult['count'] = 'Найден'.VString::spellAmount($nCount, 'а,о,о'). ' '.$nCount.' клиник'.VString::spellAmount($nCount, 'а,и,');
\bitrix\ERESULT::$DATA['count'] = $arResult['count'];

$obResult->NavStart($arPageCount);
$arResult['clinics']=array();

while($arData=$obResult->Fetch()){
	$arClinics[]=$arData['id'];
	$arData['name']=trim($arData['name']);
	$arData['link'] = '/cl'.$arData['id'].'/';

	if(!empty($arData['logo_id'])){
		$file=CFile::ShowImage($arData["logo_id"], 160, 80,'alt="'.$arData['name'].'"');
		$arData['logo']=$file;
	}

	if(!empty($arData['phone']))
		$arData['phone']=\core\types\VString::formatPhone($arData['phone']);

	if(!empty($arData['web']))
		$arData['web_short']=\core\types\VString::checkUrl($arData['web']);

	$arResult['clinics'][$arData['id']]=$arData;
}

if (!empty($arClinics)){
	//получаем услуги
	$obQuery=$obClinics->createQuery();
	$obQuery->builder()
		->from('estelife_clinic_services', 'ecs');
	$obJoin=$obQuery->builder()
		->join();

	$obJoin->_left()
		->_from('ecs','specialization_id')
		->_to('estelife_specializations','id','es');
	$obQuery->builder()
		->field('es.name','s_name')
		->field('es.id','s_id')
		->field('ecs.clinic_id');
	$obQuery->builder()
		->filter()
		->_in('ecs.clinic_id', $arClinics);

	$arClinicSpecialization = $obQuery->select()->all();
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

$sTemplate=$this->getTemplateName();
$obNav=new \bitrix\VNavigation($obResult,($sTemplate=='ajax'));
$arResult['nav'] = $obNav->getNav();

$sTitle = "Клиники косметологии и пластической хирургии";

if (!empty($arResult['city']['NAME'])){
	$sTitle .= ' ('.$arResult['city']['NAME'].')';
}

if (!empty($arResult['city']['PROPERTY_CITY_VALUE'])){
	$sDescription = 'Список всех клиник '.$arResult['city']['PROPERTY_CITY_VALUE'].' по косметологии и пластической хирургии. Читайте здесь.';
}else{
	$sDescription = 'Список всех клиник по косметологии и пластической хирургии. Читайте здесь.';
}

if (isset($_GET['PAGEN_1']) && intval($_GET['PAGEN_1'])>0){
	$_GET['PAGEN_1'] = intval($_GET['PAGEN_1']);
	$sTitle.=' - '.$_GET['PAGEN_1'].' страница';
	$sDescription.=' - '.$_GET['PAGEN_1'].' страница';
}

$APPLICATION->SetPageProperty("title", $sTitle);
$APPLICATION->SetPageProperty("description", $sDescription);

$this->IncludeComponentTemplate();