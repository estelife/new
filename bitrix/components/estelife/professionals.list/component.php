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
	$arPageCount = 10;

//Получение списка специалистов
$obQuery=$obProfessionals->createQuery();
$obQuery->builder()->from('b_user', 'u');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('u', 'ID')
	->_to('estelife_professionals', 'user_id', 'ep');
$obJoin->_left()
	->_from('ep', 'country_id')
	->_to('iblock_element','ID','ct')
	->_cond()->_eq('ct.IBLOCK_ID',15);
$obQuery->builder()
	->field('u.ID','id')
	->field('u.NAME','name')
	->field('u.LAST_NAME', 'last_name')
	->field('ep.country_id', 'country_id')
	->field('ep.image_id', 'image_id')
	->field('ct.NAME', 'country_name');

$obQuery->builder()->sort('u.NAME', 'asc');
$obResult = $obQuery->select();

$obResult = $obResult->bxResult();
$nCount = $obResult->SelectedRowsCount();

$arResult['count'] = 'Найден'.VString::spellAmount($nCount, ',о,о'). ' '.$nCount.' специалист'.VString::spellAmount($nCount, ',а,ов');
\bitrix\ERESULT::$DATA['count'] = $arResult['count'];

$obResult->NavStart($arPageCount);

$arResult['prof'] = array();
while($arData=$obResult->Fetch()){
	$arData['link'] = '/pf'.$arData['id'].'/';

	if(!empty($arData['image_id'])){
		$file=CFile::ShowImage($arData["image_id"], 180, 180,'alt="'.$arData['name'].'"');
		$arData['logo']=$file;
	}
	$arResult['prof'][]=$arData;
}


$arSEOTitle = $arTypes[$_GET['type']].' - все аппараты в нашей базе данных.';
$arSEODescription = 'Вся информация по аппаратам для процедуры '.$arTypes[$_GET['type']].'. Весь список с подробным описанием в нашей базе данных.';

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