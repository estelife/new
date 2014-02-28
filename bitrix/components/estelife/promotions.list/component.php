<?php
use core\database\VDatabase;
use core\types\VArray;
use core\types\VString;
use core\database\mysql\VFilter;
use geo\VGeo;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");
CModule::IncludeModule("estelife");

$obClinics = VDatabase::driver();
$obGet=new VArray($_GET);
$arNow=time();
$nCityId=0;

$session = new \filters\decorators\VPromotions();
$arFilterParams = $session->getParams();

if (isset($arParams['COUNT']) && $arParams['COUNT']>0)
	$arCount = $arParams['COUNT'];

if (isset($arParams['PAGE_COUNT']) && $arParams['PAGE_COUNT']>0)
	$arPageCount = $arParams['PAGE_COUNT'];
else
	$arPageCount = 12;

if(!empty($arFilterParams['city']) && $arFilterParams['city'] !='all'){
	$nCityId=$arResult['city']['ID'] = $arFilterParams['city'];
}else if(!$obGet->blank('city'))
	$nCityId=intval($obGet->one('city'));
elseif (isset($arParams['CITY_ID']) && $arParams['CITY_ID']>0)
	$nCityId=intval($arParams['CITY_ID']);
elseif(isset($_COOKIE['estelife_city']))
	$nCityId=intval($_COOKIE['estelife_city']);

if($nCityId>0){
	//Получаем имя города по его ID
	$obCity=CIBlockElement::GetList(
		array("NAME"=>"ASC"),
		array("IBLOCK_ID"=>16, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "ID" => $nCityId),
		false,
		false,
		array("ID", "NAME", 'PROPERTY_CITY')
	);
	$arResult['city']=$obCity->Fetch();
	$arResult['city']['R_NAME']=(!empty($arResult['city']['PROPERTY_CITY_VALUE'])) ?
		$arResult['city']['PROPERTY_CITY_VALUE'] :
		$arResult['city']['NAME'];
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
	->field('ea.small_photo','s_logo_id')
	->field('ec.name','clinic_name')
	->field('ec.id','clinic_id');
$obFilter=$obQuery->builder()->filter();
$obFilter->_gte('ea.end_date', $arNow);
$obFilter->_eq('ea.active', 1);

$obQuery->builder()
	->sort('ea.end_date', 'desc');
$bFilterByCity=false;

if($bFilterByCity=(!empty($arFilterParams['city']) && $arFilterParams['city'] !=='all')){
	$obFilter->_eq('ec.city_id', $arFilterParams['city']);
}

if(!empty($arFilterParams['name'])){
	$obSearch=new \search\VSearch();
	$obSearch->setIndex('promotions');

	if($bFilterByCity)
		$obSearch->setFilter('city',array($arFilterParams['city']));

	$arSearch=$obSearch->search($arFilterParams['name']);

	if(!empty($arSearch)){
		$arTemp=array();

		foreach($arSearch as $arValue)
			$arTemp[]=$arValue['id'];

		$obFilter->_in('ea.id',$arTemp);
	}else
		$obFilter->_eq('ea.id',0);
}else if(!$obGet->blank('name')){
	$obFilter->_like('ea.name',$obGet->one('name'),VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);
}

if(!empty($arFilterParams['metro'])){
	$obFilter->_eq('ec.metro_id', intval($arFilterParams['metro']));
}
if(!empty($arFilterParams['spec'])){
	$obFilter->_eq('eat.specialization_id', intval($arFilterParams['spec']));
}
if(!empty($arFilterParams['sevice'])){
	$obFilter->_eq('eat.service_id', intval($arFilterParams['sevice']));
}
if(!empty($arFilterParams['concreate'])){
	$obFilter->_eq('eat.service_concreate_id', intval($arFilterParams['concreate']));
}
if(!empty($arFilterParams['method'])){
	$obFilter->_eq('eat.method_id', intval($arFilterParams['method']));
}


if(!empty($arCount))
	$obQuery->builder()->slice(0,$arCount);

$obQuery->builder()->group('ea.id');
$obResult=$obQuery->select();
$arResult['akzii']=array();

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
	$nCount = $obResult->SelectedRowsCount();
	$arResult['count'] = 'Найден'.VString::spellAmount($nCount, 'а,о,о'). ' '.$nCount.' акц'.VString::spellAmount($nCount, 'ия,ии,ий');
	\bitrix\ERESULT::$DATA['count'] = $arResult['count'];
	$obResult->NavStart($arPageCount);

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
	}

	$sTemplate=$this->getTemplateName();
	$obNav=new \bitrix\VNavigation($obResult,($sTemplate=='ajax'));
	$arResult['nav']=$obNav->getNav();

	if (empty($arResult['city']['R_NAME']))
		$arResult['city']['R_NAME'] = '';
	else
		$arResult['city']['R_NAME'] = ' '.$arResult['city']['R_NAME'];

	$sTitle='Клиники'.$arResult['city']['R_NAME'].' - акции, скидки, купоны.';
	$sDescription='Актуальные акции и скидки клиник'.$arResult['city']['R_NAME'].'.';

	if (isset($_GET['PAGEN_1']) && intval($_GET['PAGEN_1'])>0){
		$_GET['PAGEN_1'] = intval($_GET['PAGEN_1']);
		$sTitle.=' - '.$_GET['PAGEN_1'].' страница';
		$sDescription.=' - '.$_GET['PAGEN_1'].' страница';
	}

	$APPLICATION->SetPageProperty("title", $sTitle);
	$APPLICATION->SetPageProperty("description", $sDescription);
}

$this->IncludeComponentTemplate();