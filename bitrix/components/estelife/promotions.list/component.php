<?php
use core\database\VDatabase;
use core\types\VArray;
use geo\VGeo;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");
CModule::IncludeModule("estelife");

$obClinics = VDatabase::driver();
$obGet=new VArray($_GET);

$arNow=time();

if (isset($arParams['COUNT']) && $arParams['COUNT']>0){
	$arCount = $arParams['COUNT'];
}

if (isset($arParams['PAGE_COUNT']) && $arParams['PAGE_COUNT']>0)
	$arPageCount = $arParams['PAGE_COUNT'];
else
	$arPageCount = 10;

if (isset($arParams['CITY_ID']) && $arParams['CITY_ID']>0){
	//Получаем имя города по его ID
	$arSelect = Array("ID", "NAME");
	$arFilter = Array("IBLOCK_ID"=>16, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "ID" => $arParams['CITY_ID']);
	$obCity = CIBlockElement::GetList(Array("NAME"=>"ASC"), $arFilter, false, false, $arSelect);

	while($res = $obCity->Fetch()) {
		$arResult['city'] = $res;
	}
}else if(isset($arParams['CITY_CODE']) && !empty($arParams['CITY_CODE'])){
	//Получаем ID города по его коду
	$arSelect = Array("ID", "NAME");
	$arFilter = Array("IBLOCK_ID"=>16, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "CODE" => $arParams['CITY_CODE']);
	$obCity = CIBlockElement::GetList(Array("NAME"=>"ASC"), $arFilter, false, false, $arSelect);

	while($res = $obCity->Fetch()) {
		$arResult['city'] = $res;
	}
}elseif(isset($_COOKIE['estelife_city'])){
	$arResult['city'] = VGeo::getInstance()->getGeo();
}


//Получение списка акций
$obQuery = $obClinics->createQuery();
$obQuery->builder()->from('estelife_akzii', 'ea');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ea','id')
	->_to('estelife_clinic_akzii','akzii_id','eca');
$obJoin->_left()
	->_from('eca','clinic_id')
	->_to('estelife_clinics','id','ec');
$obJoin->_left()
	->_from('ec','city_id')
	->_to('iblock_element','ID','ct')
	->_cond()->_eq('ct.IBLOCK_ID',16);
$obJoin->_left()
	->_from('ea', 'id')
	->_to('estelife_akzii_types', 'akzii_id', 'eat');
$obQuery->builder()
	->field('ea.id','id')
	->field('ea.name','name')
	->field('ea.end_date','end_date')
	->field('ea.base_old_price','old_price')
	->field('ea.base_new_price','new_price')
	->field('ea.base_sale','sale')
	->field('ea.small_photo','logo_id')
	->field('ea.view_type','view_type')
	->field('ct.CODE', 'city_code')
	->field('ea.small_photo','s_logo_id');
$obFilter=$obQuery->builder()->filter();
$obFilter->_gte('ea.end_date', $arNow);
$obFilter->_eq('ea.active', 1);

$obQuery->builder()->sort('ea.end_date', 'desc');


if(!$obGet->blank('city')){
	$obFilter->_eq('ec.city_id', intval($obGet->one('city')));
}elseif (!empty($arResult['city'])){
	$obFilter->_eq('ec.city_id', $arResult['city']['ID']);
}

if(!$obGet->blank('metro'))
	$obFilter->_eq('ec.metro_id', intval($obGet->one('metro')));
if(!$obGet->blank('spec'))
	$obFilter->_eq('eat.specialization_id', intval($obGet->one('spec')));
if(!$obGet->blank('service'))
	$obFilter->_eq('eat.service_id', intval($obGet->one('service')));
if(!$obGet->blank('concreate'))
	$obFilter->_eq('eat.service_concreate_id', intval($obGet->one('concreate')));
if(!$obGet->blank('method'))
	$obFilter->_eq('eat.method_id', intval($obGet->one('method')));

if(!empty($arCount))
	$obQuery->builder()->slice(0,$arCount);

$obQuery->builder()->group('ea.id');
$obResult=$obQuery->select();
$arResult['akzii']=array();
$arDescription=array();

if(!empty($arCount)){
	$arActions= $obResult->all();
	foreach ($arActions as $val){
		$val['src'] = CFile::GetFileArray($val["logo_id"]);
		$val['src'] = $val['src']['SRC'];
		$val['new_price'] = number_format($val['new_price'],0,'.',' ');
		$val['old_price'] = number_format($val['old_price'],0,'.',' ');
		$val['time'] = ceil(($val['end_date']-$arNow)/(60*60*24));
		$val['day'] = \core\types\VString::spellAmount($val['time'], 'день,дня,дней');
		$val['link'] = '/pr'.$val['id'].'/';
		$arResult['akzii'][]=$val;
	}
	if (!empty($arResult['city']['ID'])){
		$arResult['link'] = '/promotions/?city='.$arResult['city']['ID'];
	}else{
		$arResult['link'] = '/promotions/';
	}
	unset($arActions);
}else{
	$obResult=$obResult->bxResult();
	$obResult->NavStart($arPageCount);

	$i = 0;
	while($arData=$obResult->Fetch()){
		$arData['time']=ceil(($arData['end_date']-$arNow)/(60*60*24));
		$arData['day']=\core\types\VString::spellAmount($arData['time'], 'день,дня,дней');
		$arData['link'] = '/pr'.$arData['id'].'/';

		if(!empty($arData['logo_id'])){
			$arData['img'] = CFile::GetFileArray($arData["logo_id"]);
			$arData['src']=$arData['img']['SRC'];
		}

		$arData['new_price'] = number_format(intval($arData['new_price']),0,'.',' ');
		$arData['old_price'] = number_format(intval($arData['old_price']),0,'.',' ');

		$arResult['akzii'][]=$arData;

		if ($i<=5){
			$arDescription[]= mb_strtolower(trim(preg_replace('#[^\w\d\s\.\,\-а-я]+#iu','',$arData['name'])),'utf-8');
		}
		$i++;
	}

	$sTemplate=$this->getTemplateName();
	$obNav=new \bitrix\VNavigation($obResult,($sTemplate=='ajax'));
	$arResult['nav']=$obNav->getNav();
//	$arResult['nav']=$obResult->GetNavPrint('', true,'akzii','/bitrix/templates/estelife/system/pagenav.php');
}

$arDescription=implode(", ", $arDescription);
$arResult['SEO']=array(
	'title'=>'Акции',
	'description'=>$arDescription,
	'keywords'=>"Estelife, Акции, Клиники, ".$arDescription
);

$this->IncludeComponentTemplate();