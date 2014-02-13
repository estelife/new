<?php
use core\database\mysql\VFilter;
use core\database\VDatabase;
use core\types\VArray;
use core\types\VString;

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

//Получение списка аппаратов
$obQuery = $obPills->createQuery();
$obQuery->builder()->from('estelife_apparatus', 'ap');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ap', 'id')
	->_to('estelife_apparatus_type', 'apparatus_id', 'apt');
$obJoin->_left()
	->_from('ap', 'company_id')
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
	->field('ap.id','id')
	->field('ap.name','name')
	->field('ap.translit','translit')
	->field('ap.logo_id','logo_id')
	->field('ap.preview_text', 'preview_text')
	->field('ec.name','company_name')
	->field('ec.id','company_id')
	->field('ec.translit','company_translit')
	->field('ec.id','company_id')
	->field('ect.name','type_company_name')
	->field('ect.id','type_company_id')
	->field('ect.translit','type_company_translit')
	->field('ect.id','type_company_id');
$obFilter = $obQuery->builder()->filter();

$session = new \filters\VApparatusesFilter();
$arFilterParams = $session->getParams();


if(!empty($arFilterParams['country']) && $arFilterParams['country'] !='all'){
	$obFilter->_or()->_eq('ecg.country_id', intval($arFilterParams['country']));
	$obFilter->_or()->_eq('ectg.country_id', intval($arFilterParams['country']));
}else if(!$obGet->blank('country') && $obGet->one('country')!=='all'){
	$obFilter->_or()->_eq('ecg.country_id', intval($obGet->one('country')));
	$obFilter->_or()->_eq('ectg.country_id', intval($obGet->one('country')));
}

if(!$obGet->blank('name')){
	$obFilter->_like('ap.name',$obGet->one('name'),VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);
}

if(!empty($arFilterParams['type'])){
	$obFilter->_eq('apt.type_id', intval($arFilterParams['type']));
}else if(!$obGet->blank('type')){
	$obFilter->_eq('apt.type_id', intval($obGet->one('type')));
}
$obQuery->builder()->group('ap.id');
$obQuery->builder()->sort('ap.name', 'asc');
$obResult = $obQuery->select();

$obResult = $obResult->bxResult();
$nCount = $obResult->SelectedRowsCount();
$arResult['count'] = 'Найден'.VString::spellAmount($nCount, ',о,о'). ' '.$nCount.' аппарат'.VString::spellAmount($nCount, ',а,ов');
\bitrix\ERESULT::$DATA['count'] = $arResult['count'];

$obResult->NavStart($arPageCount);
$arResult['apps'] = array();

$i = 0;
$arResult['apps'] = array();
while($arData=$obResult->Fetch()){
	$arData['link'] = '/ap'.$arData['id'].'/';
	$arData['preview_text'] = \core\types\VString::truncate(nl2br(htmlspecialchars_decode($arData['preview_text'],ENT_NOQUOTES)), 250, '...');

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

	$arData['company_link'] = '/am'.$arData['company_id'].'/';

	$arResult['apps'][]=$arData;

	if ($i<=5){
		$arDescription[]= mb_strtolower($arData['name']);
	}
	$i++;
}

$arTypes = array(
"1"=>'Anti-Age терапия',
"7"=>'Диагностика',
"2"=>'Коррекция фигуры',
"9"=>'Микропигментация',
"5"=>'Микротоки',
"4"=>'Миостимуляция',
"6"=>'Лазеры',
"8"=>'Реабилитация',
"3"=>'Эпиляция'
);

if (empty($_GET['type'])){
	$arSEOTitle = 'Список и база данных аппартов в эстетической медицине.';
	$arSEODescription = 'Огромная база данных по аппаратам для всех процедур и видов терапий в эстетической медицине. Подробная информация только у нас.';
}else{
	$arSEOTitle = $arTypes[$_GET['type']].' - все аппараты в нашей базе данных.';
	$arSEODescription = 'Вся информация по аппаратам для процедуры '.$arTypes[$_GET['type']].'. Весь список с подробным описанием в нашей базе данных.';
}

if (isset($_GET['PAGEN_1']) && intval($_GET['PAGEN_1'])>0){
	$_GET['PAGEN_1'] = intval($_GET['PAGEN_1']);
	$arSEOTitle.=' - '.$_GET['PAGEN_1'].' страница';
	$arSEODescription.=' - '.$_GET['PAGEN_1'].' страница';
}


$APPLICATION->SetPageProperty("title", $arSEOTitle);
$APPLICATION->SetPageProperty("description", VString::truncate($arSEODescription, 160, ''));
$APPLICATION->SetPageProperty("keywords", "Estelife, Аппараты, ". $arSEODescription);

$sTemplate=$this->getTemplateName();
$obNav=new \bitrix\VNavigation($obResult,($sTemplate=='ajax'));
$arResult['nav']=$obNav->getNav();
//$arResult['nav']=$obResult->GetNavPrint('', true,'text','/bitrix/templates/estelife/system/pagenav.php');
$this->IncludeComponentTemplate();