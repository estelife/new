<?php
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

//Получение списка организаторов
$obQuery = $obClinics->createQuery();
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
	->_from('ecg','metro_id')
	->_to('iblock_element','ID','mt')
	->_cond()->_eq('mt.IBLOCK_ID',17);
$obJoin->_left()
	->_from('ecg','city_id')
	->_to('iblock_element','ID','ct')
	->_cond()->_eq('ct.IBLOCK_ID',16);
$obJoin->_left()
	->_from('ec', 'id')
	->_to('estelife_company_contacts', 'company_id', 'ecc')
	->_cond()->_eq('ecc.type', 'web');
$obJoin->_left()
	->_from('ec', 'id')
	->_to('estelife_company_contacts', 'company_id', 'eccp')
	->_cond()->_eq('eccp.type', 'phone');
$obJoin->_left()
	->_from('ec', 'id')
	->_to('estelife_company_types', 'company_id', 'ect')
	->_cond()->_eq('ect.type', 4);
$obJoin->_left()
	->_from('ect', 'id')
	->_to('estelife_company_type_geo', 'company_id', 'ectg');
$obJoin->_left()
	->_from('ectg','metro_id')
	->_to('iblock_element','ID','mtt')
	->_cond()->_eq('mtt.IBLOCK_ID',17);
$obJoin->_left()
	->_from('ectg','city_id')
	->_to('iblock_element','ID','ctt')
	->_cond()->_eq('ctt.IBLOCK_ID',16);
$obJoin->_left()
	->_from('ect', 'id')
	->_to('estelife_company_type_contacts', 'company_id', 'ectc')
	->_cond()->_eq('ecc.type', 'web');
$obJoin->_left()
	->_from('ect', 'id')
	->_to('estelife_company_type_contacts', 'company_id', 'ectcp')
	->_cond()->_eq('eccp.type', 'phone');
$obQuery->builder()
	->field('ec.name', 'name')
	->field('ec.logo_id', 'logo_id')
	->field('ecg.address', 'address')
	->field('ecc.value', 'web')
	->field('eccp.value', 'phone')
	->field('ect.name', 'type_name')
	->field('ecе.logo_id', 'type_logo_id')
	->field('ectg.address', 'type_address')
	->field('ectc.value', 'type_web')
	->field('ectcp.value', 'type_phone')
	->field('ece.company_id','company_id')
	->field('ect.id', 'type_company_id')
	->field('mt.NAME', 'metro')
	->field('mtt.NAME', 'type_metro')
	->field('ct.NAME', 'city')
	->field('ctt.NAME', 'type_city')
	->field('ecg.city_id', 'city_id')
	->field('ectg.city_id', 'type_city_id');

$obFilter = $obQuery->builder()->filter()
	->_eq('eet.type', 3)
	->_eq('ece.is_owner', 1);


if (!$obGet->blank('city')){
	$obFilter->_or()
		->_eq('ecg.city_id', intval($obGet->one('city')));
	$obFilter->_or()
		->_eq('ectd.city_id', intval($obGet->one('city')));
}


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
$obResult->NavStart($arPageCount);
$arResult['org']=array();

$arDescription=array();

while($arData=$obResult->Fetch()){
	if (!empty($arData['type_company_id'])){
		$arData['name'] = $arData['type_name'];
		$arData['company_id'] = $arData['type_company_id'];
	}
	$arData['link'] = '/tc'.$arData['company_id'].'/';

	if (!empty($arData['type_logo_id'])){
		$arData["logo_id"] = $arData["type_logo_id"];
	}
	$arData['img'] = CFile::ShowImage($arData["logo_id"], 190, 100, 'alt='.$arData["name"]);

	if (!empty($arData['type_address'])){
		$arData["address"] = $arData["type_address"];
	}
	unset($arData["type_address"]);

	if (!empty($arData['type_metro'])){
		$arData["metro"] = $arData["type_metro"];
	}
	unset($arData["type_metro"]);

	if (!empty($arData['type_city'])){
		$arData["city"] = $arData["type_city"];
	}
	unset($arData["type_city"]);

	$arData["address"] = 'г. '.$arData["city"].', '.$arData["address"];

	if (!empty($arData['type_city_id'])){
		$arData["city_id"] = $arData["type_city_id"];
	}
	unset($arData["type_city_id"]);

	if (!empty($arData['type_web'])){
		$arData["web"] = $arData["type_web"];
	}
	unset($arData["type_web"]);

	if (!empty($arData['type_phone'])){
		$arData["phone"] = $arData["type_phone"];
	}
	unset($arData["type_phone"]);
	$arData['phone']=\core\types\VString::formatPhone($arData['phone']);
	
	if(!empty($arData['web']))
		$arData['short_web']=\core\types\VString::checkUrl($arData['web']);

	$arDescription[]=mb_strtolower(trim(preg_replace('#[^\w\d\s\.\,\-а-я]+#iu','',$arData['name'])),'utf-8');
	$arResult['org'][]=$arData;
}

$arDescription=implode(', ',$arDescription);
$APPLICATION->SetPageProperty("title", 'Учебные центры');
$APPLICATION->SetPageProperty("description", $arDescription);
$APPLICATION->SetPageProperty("keywords", "Estelife, учебные центры, ".$arDescription);

$arResult['nav']=$obResult->GetNavPrint('', true,'text','/bitrix/templates/estelife/system/pagenav.php');
$this->IncludeComponentTemplate();