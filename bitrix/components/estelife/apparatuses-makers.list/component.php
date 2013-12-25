<?php
use core\database\mysql\VFilter;
use core\database\VDatabase;
use core\types\VArray;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");
CModule::IncludeModule("estelife");

$obClinics = VDatabase::driver();
$obGet=new VArray($_GET);


if (isset($arParams['PAGE_COUNT']) && $arParams['PAGE_COUNT']>0)
	$arPageCount = $arParams['PAGE_COUNT'];
else
	$arPageCount = 10;

//Получение списка препаратов
$obQuery = $obClinics->createQuery();
$obQuery->builder()->from('estelife_companies', 'ec');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ec', 'id')
	->_to('estelife_company_geo', 'company_id', 'ecg');
$obJoin->_left()
	->_from('ecg','country_id')
	->_to('iblock_element','ID','ct')
	->_cond()->_eq('ct.IBLOCK_ID',15);
$obJoin->_right()
	->_from('ec', 'id')
	->_to('estelife_apparatus', 'company_id', 'ep');
$obJoin->_left()
	->_from('ec', 'id')
	->_to('estelife_company_contacts', 'company_id', 'ecc')
	->_cond()->_eq('ecc.type', 'web');
$obJoin->_left()
	->_from('ec', 'id')
	->_to('estelife_company_types', 'company_id', 'ect')
	->_cond()->_or()
		->_eq('ect.type', 3)
		->_isNull('ect.type');
$obJoin->_left()
	->_from('ect', 'id')
	->_to('estelife_company_type_geo', 'company_id', 'ectg');
$obJoin->_left()
	->_from('ectg','country_id')
	->_to('iblock_element','ID','cttype')
	->_cond()->_eq('cttype.IBLOCK_ID',15);
$obJoin->_left()
	->_from('ect', 'id')
	->_to('estelife_company_type_contacts', 'company_id', 'ectc')
	->_cond()->_eq('ectc.type', 'web');
$obQuery->builder()
	->field('ct.ID','country_id')
	->field('ct.NAME','country_name')
	->field('cttype.ID','type_country_id')
	->field('cttype.NAME','type_country_name')
	->field('ec.id','id')
	->field('ec.translit','translit')
	->field('ec.preview_text','preview_text')
	->field('ec.name','name')
	->field('ec.logo_id','logo_id')
	->field('ecg.country_id','company_country_id')
	->field('ecc.value', 'web')
	->field('ect.id','type_id')
	->field('ect.name','type_name')
	->field('ect.preview_text','type_preview_text')
	->field('ectg.country_id','company_type_country_id')
	->field('ect.logo_id','type_logo_id')
	->field('ectc.value', 'type_web');
$obFilter = $obQuery->builder()->filter();
	if (!$obGet->blank('country')){
		$obFilter->_eq('ecg.country_id', intval($obGet->one('country')));
	}
	if (!$obGet->blank('name')){
		$obFilter->_like('ec.name', $obGet->one('name'),VFilter::LIKE_BEFORE|VFilter::LIKE_AFTER);
	}
$obQuery->builder()->group('ec.id');
$obQuery->builder()->sort('ec.name', 'asc');
$obResult = $obQuery->select();

$obResult = $obResult->bxResult();
$obResult->NavStart($arPageCount);
$arResult['apparatus'] = array();

$i=0;
$arResult['apparatus'] = array();
while($arData=$obResult->Fetch()){
	if (!empty($arData['type_id'])){
		$arData['name'] = $arData['type_name'];
	}

	$arData['link'] = '/am'.$arData['id'].'/';

	if (!empty($arData['type_logo_id'])){
		$arData["logo_id"] = $arData["type_logo_id"];
	}
	$arData['img'] = CFile::ShowImage($arData["logo_id"], 110, 90, 'alt='.$arData["name"]);

	if (!empty($arData['type_country_name'])){
		$arData["country_name"] = $arData["type_country_name"];
	}
	if (!empty($arData['type_country_id'])){
		$arData["country_id"] = $arData["type_country_id"];
	}
	if (!empty($arData['type_web'])){
		$arData["web"] = $arData["type_web"];
	}

	$arData['web_short']=\core\types\VString::checkUrl($arData['web']);

	if (!empty($arData['type_preview_text'])){
		$arData["preview_text"] = $arData["type_preview_text"];
	}

	$arData["preview_text"] = \core\types\VString::truncate(html_entity_decode($arData['preview_text'],ENT_QUOTES,'UTF-8'), 160, '...');

	$arResult['apparatus'][]=$arData;

	if ($i<=5){
		$arDescription[]= mb_strtolower(trim(preg_replace('#[^\w\d\s\.\,\-а-я]+#iu','',$arData['name'])),'utf-8');
	}
	$i++;
}
$sTemplate=$this->getTemplateName();
$obNav=new \bitrix\VNavigation($obResult,($sTemplate=='ajax'));
$arResult['nav']=$obNav->getNav();
//$arResult['nav']=$obResult->GetNavPrint('', true,'text','/bitrix/templates/estelife/system/pagenav.php');


$APPLICATION->SetPageProperty("title", "Производители аппаратов");
$APPLICATION->SetPageProperty("description", implode(", ", $arDescription));
$APPLICATION->SetPageProperty("keywords", "Estelife, Производители аппаратов, ". implode(" ,", $arDescription));
$this->IncludeComponentTemplate();