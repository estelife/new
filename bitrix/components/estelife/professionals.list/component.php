<?php
use core\database\mysql\VFilter;
use core\database\VDatabase;
use core\types\VArray;
use core\types\VString;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

CModule::IncludeModule("iblock");
CModule::IncludeModule("estelife");

$obProfessionals = VDatabase::driver();
$obGet=new VArray($_GET);

if (isset($arParams['PAGE_COUNT']) && $arParams['PAGE_COUNT']>0)
	$arPageCount = $arParams['PAGE_COUNT'];
else
	$arPageCount = 12;

//Получение списка специалистов
$obQuery=$obProfessionals->createQuery();
$obQuery->builder()->from('estelife_professionals', 'ep');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ep', 'user_id')
	->_to('user', 'ID', 'u');
$obJoin->_left()
	->_from('ep', 'country_id')
	->_to('iblock_element','ID','ct')
	->_cond()->_eq('ct.IBLOCK_ID',15);
$obQuery->builder()
	->field('ep.id','id')
	->field('u.NAME','name')
	->field('u.LAST_NAME', 'last_name')
	->field('u.SECOND_NAME', 'second_name')
	->field('ep.country_id', 'country_id')
	->field('ep.image_id', 'image_id')
	->field('ct.NAME', 'country_name');

$obFilter = $obQuery->builder()->filter();

$session = new \filters\decorators\VProfessionals();
$arFilterParams = $session->getParams();

if(!empty($arFilterParams['country']) && $arFilterParams['country'] !='all'){
	$obFilter->_eq('ep.country_id', intval($arFilterParams['country']));
}

if(!empty($arFilterParams['name'])){
	$obFilter->_or()->_like('u.LAST_NAME',$arFilterParams['name'],VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);
	$obFilter->_or()->_like('u.NAME',$arFilterParams['name'],VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);
	$obFilter->_or()->_like('u.SECOND_NAME',$arFilterParams['name'],VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);
}

$obQuery->builder()->sort('u.NAME', 'asc');
$obResult = $obQuery->select();

$obResult = $obResult->bxResult();
$nCount = $obResult->SelectedRowsCount();

$arResult['count'] = 'Найден'.VString::spellAmount($nCount, ',о,о'). ' '.$nCount.' специалист'.VString::spellAmount($nCount, ',а,ов');
\bitrix\ERESULT::$DATA['count'] = $arResult['count'];

$obResult->NavStart($arPageCount);

$arResult['prof'] = array();
while($arData=$obResult->Fetch()){
	$arData['link']='/pf'.$arData['id'].'/';
	if (empty($arData['last_name']))
		$arData['name']=VString::brForName($arData['name']);
	else
		$arData['name']=VString::brForName($arData['last_name'].' '.$arData['name'].' '.$arData['second_name']);

	if (empty($val['country_name']))
		$val['country_name']='';

	if(!empty($arData['image_id'])){
		$file=CFile::ShowImage($arData["image_id"], 227, 158,'alt="'.$arData['name'].'"');
		$arData['logo']=$file;
	}
	$arResult['prof'][]=$arData;
}


$arSEOTitle = 'Специалисты';
$arSEODescription = 'Инфомарция о специалистах';

if (isset($_GET['PAGEN_1']) && intval($_GET['PAGEN_1'])>0){
	$_GET['PAGEN_1'] = intval($_GET['PAGEN_1']);
	$arSEOTitle.=' - '.$_GET['PAGEN_1'].' страница';
	$arSEODescription.=' - '.$_GET['PAGEN_1'].' страница';
}

$APPLICATION->SetPageProperty("title", $arSEOTitle);
$APPLICATION->SetPageProperty("description", VString::truncate($arSEODescription, 160, ''));
$APPLICATION->SetPageProperty("keywords", "Estelife, Специалисты, ". $arSEODescription);

$sTemplate=$this->getTemplateName();
$obNav=new \bitrix\VNavigation($obResult,($sTemplate=='ajax'));
$arResult['nav']=$obNav->getNav();
$this->IncludeComponentTemplate();