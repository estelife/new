<?php
use core\database\mysql\VFilter;
use core\database\VDatabase;
use core\types\VArray;
use core\types\VString;
use geo\VGeo;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");
CModule::IncludeModule("estelife");

$obDriver = VDatabase::driver();
$obGet=new VArray($_GET);


if (isset($arParams['PAGE_COUNT']) && $arParams['PAGE_COUNT']>0)
	$arPageCount = $arParams['PAGE_COUNT'];
else
	$arPageCount = 10;

if(!$obGet->blank('city')){
	$obQuery=$obDriver->createQuery();
	$obQuery->builder()
		->from('iblock_element')
		->field('NAME')
		->field('ID')
		->filter()
		->_eq('ID',intval($obGet->one('city')));
	$arResult['city']=$obQuery->select()->assoc();
}

if(!$obGet->blank('country')){
	$obQuery=$obDriver->createQuery();
	$obQuery->builder()
		->from('iblock_element')
		->field('NAME')
		->field('ID')
		->filter()
		->_eq('ID',intval($obGet->one('country')));
	$arResult['country']=$obQuery->select()->assoc();
}

//Получение списка организаторов
$obQuery = $obDriver->createQuery();
$obQuery->builder()->from('estelife_company_events', 'ece');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ece', 'event_id')
	->_to('estelife_events', 'id', 'ee');
$obJoin->_left()
	->_from('ee', 'id')
	->_to('estelife_event_types', 'event_id', 'eet');
$obJoin->_left()
	->_from('ece', 'company_id')
	->_to('estelife_companies', 'id', 'ec');
$obJoin->_left()
	->_from('ec', 'id')
	->_to('estelife_company_geo', 'company_id', 'ecg');
$obJoin->_left()
	->_from('ec', 'id')
	->_to('estelife_company_contacts', 'company_id', 'ecc')
	->_cond()->_eq('ecc.type', 'web');
$obJoin->_left()
	->_from('ec', 'id')
	->_to('estelife_company_types', 'company_id', 'ect')
	->_cond()->_eq('ect.type', 2);
$obJoin->_left()
	->_from('ect', 'id')
	->_to('estelife_company_type_geo', 'company_id', 'ectg');
$obJoin->_left()
	->_from('ect', 'id')
	->_to('estelife_company_type_contacts', 'company_id', 'ectc')
	->_cond()->_eq('ecc.type', 'web');
$obJoin->_left()
	->_from('ecg','country_id')
	->_to('iblock_element','ID','company_country')
	->_cond()->_eq('company_country.IBLOCK_ID',15);
$obJoin->_left()
	->_from('ectg','country_id')
	->_to('iblock_element','ID','company_type_country')
	->_cond()->_eq('company_type_country.IBLOCK_ID',15);
$obJoin->_left()
	->_from('ecg','city_id')
	->_to('iblock_element','ID','company_city')
	->_cond()->_eq('company_city.IBLOCK_ID',16);
$obJoin->_left()
	->_from('ectg','city_id')
	->_to('iblock_element','ID','company_type_city')
	->_cond()->_eq('company_type_city.IBLOCK_ID',16);
$obQuery->builder()
	->field('ec.name', 'name')
	->field('ec.logo_id', 'logo_id')
	->field('ecg.address', 'address')
	->field('ecc.value', 'web')
	->field('ect.name', 'type_name')
	->field('ecе.logo_id', 'type_logo_id')
	->field('ectg.address', 'type_address')
	->field('ectc.value', 'type_web')
	->field('ece.company_id','company_id')
	->field('ect.id', 'type_company_id')
	->field('ecg.country_id', 'country_id')
	->field('ectg.country_id', 'type_country_id')
	->field('company_country.NAME','country_name')
	->field('company_type_country.NAME','type_country_name')
	->field('company_city.NAME','city_name')
	->field('company_type_city.NAME','type_city_name');

$obFilter = $obQuery->builder()->filter()
	->_ne('eet.type', 3);

if (!empty($arResult['city']) && $obGet->one('city')!=='all')
	$obFilter->_eq('ecg.city_id', $arResult['city']['ID']);

if (!empty($arResult['country']) && $obGet->one('country')!=='all')
	$obFilter->_eq('ecg.country_id', $arResult['country']['ID']);

if(!$obGet->blank('name'))
	$obFilter->_like('ec.name',$obGet->one('name'),VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);

$obIf=$obQuery->builder()->_if();
$obIf->when(
	$obQuery->builder()->_substr('ec.name',2),
	'ec.name'
)->_gt(
	$obQuery->builder()->_regexp('ec.name','^[\\\'\"\«]+'),
	0
);

$obQuery->builder()->sort($obIf,'asc');
$obQuery->builder()->group('ece.company_id');
$obResult = $obQuery->select();

$obResult = $obResult->bxResult();
$nCount = $obResult->SelectedRowsCount();
$arResult['count'] = 'Найден'.VString::spellAmount($nCount, ',о,о'). ' '.$nCount.' организатор'.VString::spellAmount($nCount, ',а,ов');
\bitrix\ERESULT::$DATA['count'] = $arResult['count'];

$obResult->NavStart($arPageCount);
$arResult['org'] = array();
$arDescription=array();

while($arData=$obResult->Fetch()){
	if (!empty($arData['type_company_id'])){
		$arData['name'] = $arData['type_name'];
		$arData['company_id'] = $arData['type_company_id'];
	}
	$arData['link'] = '/sp'.$arData['company_id'].'/';

	if (!empty($arData['type_logo_id']))
		$arData["logo_id"] = $arData["type_logo_id"];

	$arData['img']=CFile::ShowImage($arData["logo_id"], 110, 90, 'alt='.$arData["name"]);
	$arData['short_web']=\core\types\VString::checkUrl($arData['web']);

	if (!empty($arData['type_address']))
		$arData["address"] = $arData["type_address"];

	if (!empty($arData['type_web']))
		$arData["web"] = $arData["type_web"];

	if (!empty($arData['type_country_id']))
		$arData['country_id'] = $arData['type_country_id'];

	if(!empty($arData['type_country_name']))
		$arData['country_name']=$arData['type_country_name'];

	if(!empty($arData['type_city_name']))
		$arData['city_name']=$arData['type_city_name'];

	$arAddress=array();

	if(!empty($arData['country_name']))
		$arAddress[]=$arData['country_name'];

	if(!empty($arData['city_name']))
		$arAddress[]='г. '.$arData['city_name'];

	if(!empty($arData['address']))
		$arAddress[]=$arData['address'];

	$arData['address']=implode(', ',$arAddress);

	unset(
		$arData['type_address'],
		$arData['type_web'],
		$arData['type_country_id'],
		$arData['type_country_name'],
		$arData['type_city_name'],
		$arData['country_name'],
		$arData['city_name'],
		$arData['type_logo_id'],
		$arData['logo_id']
	);

	$arResult['org'][]=$arData;
	$arDescription[]=mb_strtolower($arData['name']);
}

$arDescription=implode(', ',$arDescription);
$arDescription = strip_tags(html_entity_decode(implode(", ", $arDescription), ENT_QUOTES, 'utf-8'));
$arDescription = VString::pregStrSeo($arDescription);

$sSeoTitle='Организаторы мероприятий в сфере красоты';
$sSeoDescription='Подробная информация об организаторах выставок, форумов, семинаров и конгрессов в сфере эстетической медицины';
$arSeoGeo=array();

if(!empty($arResult['city']['NAME']))
	$arSeoGeo[]=$arResult['city']['NAME'];

if(!empty($arResult['country']['NAME']))
	$arSeoGeo[]=$arResult['country']['NAME'];

if(!empty($arSeoGeo))
	$arSeoGeo=' - '.implode(', ',$arSeoGeo);

$sSeoTitle.=$arSeoGeo;
$sSeoDescription.=$arSeoGeo;

$APPLICATION->SetPageProperty("title", $sSeoTitle);
$APPLICATION->SetPageProperty("description", VString::truncate($arDescription, 160, ''));
$APPLICATION->SetPageProperty("keywords", "Estelife, организаторы, ".$arDescription);

//$arResult['nav']=$obResult->GetNavPrint('', true,'text','/bitrix/templates/estelife/system/pagenav.php');
$sTemplate=$this->getTemplateName();
$obNav=new \bitrix\VNavigation($obResult,($sTemplate=='ajax'));
$arResult['nav']=$obNav->getNav();

$this->IncludeComponentTemplate();