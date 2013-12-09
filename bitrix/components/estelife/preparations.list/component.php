<?php
use core\database\mysql\VFilter;
use core\database\VDatabase;
use core\types\VArray;
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

CModule::IncludeModule("iblock");
CModule::IncludeModule("estelife");

$obPills = VDatabase::driver();
$obGet=new VArray($_GET);

if (isset($arParams['PAGE_COUNT']) && $arParams['PAGE_COUNT']>0)
	$arPageCount = $arParams['PAGE_COUNT'];
else
	$arPageCount = 10;

//Получение списка клиник
$obQuery = $obPills->createQuery();
$obQuery->builder()->from('estelife_pills', 'ep');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ep', 'id')
	->_to('estelife_pills_type', 'pill_id', 'ept');
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
$obJoin->_left()
	->_from('ec', 'id')
	->_to('estelife_company_types', 'company_id', 'ect')
	->_cond()->_eq('type', 3);
$obJoin->_left()
	->_from('ect', 'id')
	->_to('estelife_company_type_geo', 'company_id', 'ectg');
$obJoin->_left()
	->_from('ectg','country_id')
	->_to('iblock_element','ID','cttype')
	->_cond()->_eq('ct.IBLOCK_ID',15);
$obQuery->builder()
	->field('ct.ID','country_id')
	->field('ct.NAME','country_name')
	->field('cttype.ID','type_country_id')
	->field('cttype.NAME','type_country_name')
	->field('ep.id','id')
	->field('ep.name','name')
	->field('ep.translit','translit')
	->field('ep.logo_id','logo_id')
	->field('ep.preview_text', 'preview_text')
	->field('ec.name','company_name')
	->field('ec.id','company_id')
	->field('ec.translit','company_translit')
	->field('ec.id','company_id')
	->field('ect.name','type_company_name')
	->field('ect.id','type_company_id')
	->field('ect.translit','type_company_translit')
	->field('ect.id','type_company_id');

$obFilter = $obQuery->builder()->filter();


if(!$obGet->blank('country')){
	$obFilter->_or()->_eq('ecg.country_id', intval($obGet->one('country')));
	$obFilter->_or()->_eq('ectg.country_id', intval($obGet->one('country')));
}

if(!$obGet->blank('name')){
	$obFilter->_like('ep.name',$obGet->one('name'),VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);
}

if(!$obGet->blank('type')){
	$obFilter->_eq('ept.type_id', intval($obGet->one('type')));
}
$obQuery->builder()->group('ep.id');
$obQuery->builder()->sort('ep.name', 'asc');
$obResult = $obQuery->select();

$obResult = $obResult->bxResult();
$obResult->NavStart($arPageCount);
$arResult['pills'] = array();
$arDescription=array();

while($arData=$obResult->Fetch()){
	$arData['link'] = '/PS'.$arData['id'].'/';
	$arData['preview_text'] = \core\types\VString::truncate(htmlspecialchars_decode($arData['preview_text'],ENT_NOQUOTES), 250, '...');

	if(!empty($arData['logo_id'])){
		$file=CFile::ShowImage($arData["logo_id"], 110, 90,'alt="'.$arData['name'].'"');
		$arData['logo']=$file;
	}

	if (!empty($arData['type_country_name'])){
		$arData['country_name'] = $arData['type_country_name'];
		$arData['country_id'] = $arData['type_country_id'];
	}
	unset($arData['type_country_name']);
	unset($arData['type_country_id']);

	if (!empty($arData['type_company_name'])){
		$arData['company_name'] = $arData['type_company_name'];
		$arData['company_translit'] = $arData['type_company_translit'];
	}
	unset($arData['type_company_name']);
	unset($arData['type_company_translit']);

	if (!empty($arData['type_company_id'])){
		$arData['company_id'] = $arData['type_company_id'];
	}
	unset($arData['type_company_id']);

	$arData['company_link'] = '/PM'.$arData['company_id'].'/';

	$arResult['pills'][]=$arData;
	$arDescription[]=mb_strtolower(trim(preg_replace('#[^\w\d\s\.\,\-а-я]+#iu','',$arData['name'])),'utf-8');
}

$arDescription=implode(', ',$arDescription);
$APPLICATION->SetPageProperty("title", 'Estelife - Препараты');
$APPLICATION->SetPageProperty("description", $arDescription);
$APPLICATION->SetPageProperty("keywords", "Estelife, препараты, ".$arDescription);

$arResult['nav']=$obResult->GetNavPrint('', true,'text','/bitrix/templates/estelife/system/pagenav.php');
$this->IncludeComponentTemplate();