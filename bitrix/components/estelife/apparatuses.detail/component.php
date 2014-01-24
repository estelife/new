<?php
use core\database\VDatabase;
use core\types\VArray;
use core\types\VString;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");
CModule::IncludeModule("estelife");

$obApps = VDatabase::driver();
$nCompanyId=null;
$sCompanyName=null;

$nCompanyId =  (isset($arParams['ID'])) ?
	intval($arParams['ID']) : 0;

//Получаем данные по аппарату
$obQuery = $obApps->createQuery();
$obQuery->builder()->from('estelife_apparatus', 'ap');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ap', 'company_id')
	->_to('estelife_companies', 'id', 'ec');
$obJoin->_left()
	->_from('ap','type_id')
	->_to('iblock_element','ID','pt')
	->_cond()->_eq('pt.IBLOCK_ID',31);
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
	->field('ap.*')
	->field('pt.NAME','type_name')
	->field('ec.name','company_name')
	->field('ec.id','company_id')
	->field('ec.translit','company_translit')
	->field('ec.id','company_id')
	->field('ect.name','type_company_name')
	->field('ect.id','type_company_id')
	->field('ect.translit','type_company_translit')
	->field('ect.id','type_company_id');

$obFilter=$obQuery->builder()->filter();

if(!is_null($nCompanyId)){
	$obFilter->_eq('ap.id', $nCompanyId);
}else{
	$obFilter->_eq('ap.id',0);
}

$arResult['app'] = $obQuery->select()->assoc();


if (!empty($arResult['app']['type_company_name'])){
	$arResult['app']['company_name'] = $arResult['app']['type_company_name'];
	$arResult['app']['company_id'] = $arResult['app']['type_company_id'];
}
unset($arResult['app']['type_company_name']);
unset($arResult['app']['type_company_id']);

if (!empty($arResult['app']['type_country_name'])){
	$arResult['app']['country_name'] = $arResult['app']['type_country_name'];
	$arResult['app']['country_id'] = $arResult['app']['type_country_id'];
}
unset($arResult['app']['type_country_name']);
unset($arResult['app']['type_country_id']);

if (!empty($arResult['app']['type_company_translit'])){
	$arResult['app']['company_translit'] = $arResult['app']['type_company_translit'];
}
unset($arResult['app']['type_company_translit']);

if (!empty($arResult['pill']['type_company_id'])){
	$arResult['pill']['company_id'] = $arResult['pill']['type_company_id'];
}
unset($arResult['pill']['type_company_id']);

$arResult['app']['company_link'] = '/am'.$arResult['app']['company_id'].'/';

$arResult['app']['img'] = CFile::ShowImage($arResult['app']['logo_id'],200, 85, 'alt='.$arResult['app']['name']);

$arResult['app']['detail_text'] = nl2br(htmlspecialchars_decode($arResult['app']['detail_text'],ENT_NOQUOTES));
$arResult['app']['registration'] = nl2br(htmlspecialchars_decode($arResult['app']['registration'],ENT_NOQUOTES));
$arResult['app']['action'] = nl2br(htmlspecialchars_decode($arResult['app']['action'],ENT_NOQUOTES));
$arResult['app']['undesired'] = nl2br(htmlspecialchars_decode($arResult['app']['undesired'],ENT_NOQUOTES));
$arResult['app']['evidence'] = nl2br(htmlspecialchars_decode($arResult['app']['evidence'],ENT_NOQUOTES));
$arResult['app']['contra'] = nl2br(htmlspecialchars_decode($arResult['app']['contra'],ENT_NOQUOTES));
$arResult['app']['advantages'] = nl2br(htmlspecialchars_decode($arResult['app']['advantages'],ENT_NOQUOTES));
$arResult['app']['func'] = nl2br(htmlspecialchars_decode($arResult['app']['func'],ENT_NOQUOTES));
$arResult['app']['security'] = nl2br(htmlspecialchars_decode($arResult['app']['security'],ENT_NOQUOTES));
$arResult['app']['procedure'] = nl2br(htmlspecialchars_decode($arResult['app']['procedure'],ENT_NOQUOTES));
$arResult['app']['protocol'] = nl2br(htmlspecialchars_decode($arResult['app']['protocol'],ENT_NOQUOTES));
$arResult['app']['specs'] = nl2br(htmlspecialchars_decode($arResult['app']['specs'],ENT_NOQUOTES));
$arResult['app']['equipment'] = nl2br(htmlspecialchars_decode($arResult['app']['equipment'],ENT_NOQUOTES));

//получение галереи
$obQuery = $obApps->createQuery();
$obQuery->builder()->from('estelife_apparatus_photos');
$obQuery->builder()->filter()->_eq('apparatus_id', $arResult['app']['id']);
$arPhotos = $obQuery->select()->all();
if (!empty($arPhotos)){
	foreach ($arPhotos as $val){
		$file =  CFile::GetFileArray($val['original']);
		$arResult['app']['gallery'][] = $file['SRC'];
	}
}

//получение типов препаратов
$obQuery = $obApps->createQuery();
$obQuery->builder()->from('estelife_apparatus_type');
$obQuery->builder()->filter()->_eq('apparatus_id', $arResult['app']['id']);
$arTypes = $obQuery->select()->all();
if (!empty($arTypes)){
	foreach ($arTypes as $val){
		if ($val['type_id'] == 1){
			$arResult['app']['types'][] = 'Anti-Age терапия';
		}elseif ($val['type_id'] == 2){
			$arResult['app']['types'][] = 'Коррекция фигуры';
		}elseif ($val['type_id'] == 3){
			$arResult['app']['types'][] = 'Эпиляция';
		}elseif ($val['type_id'] == 4){
			$arResult['app']['types'][] = 'Миостимуляция';
		}elseif ($val['type_id'] == 5){
			$arResult['app']['types'][] = 'Микротоки';
		}elseif ($val['type_id'] == 6){
			$arResult['app']['types'][] = 'Лазеры';
		}elseif ($val['type_id'] == 7){
			$arResult['app']['types'][] = 'Диагностика';
		}elseif ($val['type_id'] == 8){
			$arResult['app']['types'][] = 'Реабилитация';
		}
	}
}

//Получение других препаратов для данной компании
$obQuery = $obApps->createQuery();
$obQuery->builder()->from('estelife_apparatus');
$obQuery->builder()->filter()
	->_eq('company_id', $arResult['app']['company_id'])
	->_ne('id', $arResult['app']['id']);
$obQuery->builder()->slice(0,3);
$arProductions = $obQuery->select()->all();

foreach ($arProductions as $val){
	$val['img'] = CFile::ShowImage($val['logo_id'],150, 140, 'alt='.$val['name']);
	$val['link'] = '/ap'.$val['id'].'/';
	$val['preview_text'] = \core\types\VString::truncate($val['preview_text'], 90, '...');
	$arResult['app']['production'][] = $val;
}

$arResult['app']['name'] = trim(strip_tags(html_entity_decode($arResult['app']['name'], ENT_QUOTES, 'utf-8')));
$arResult['app']['seo_name'] = preg_replace('#[^\w\d\s\.\,\-\(\)]+#iu',' ',$arResult['app']['name']);
$arResult['app']['seo_preview_text'] = trim(strip_tags(html_entity_decode($arResult['app']['detail_text'], ENT_QUOTES, 'utf-8')));
$arResult['app']['seo_preview_text'] = preg_replace('#[^\w\d\s\.\,\-\(\)]+#iu',' ',$arResult['app']['seo_preview_text']);

$APPLICATION->SetPageProperty("title", $arResult['app']['seo_name']);
$APPLICATION->SetPageProperty("description", VString::truncate($arResult['app']['seo_preview_text'],160,''));
$APPLICATION->SetPageProperty("keywords", "Estelife, Аппараты, ".$arResult['app']['seo_name']);


$this->IncludeComponentTemplate();