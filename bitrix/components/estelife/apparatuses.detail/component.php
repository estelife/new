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
if (empty($arResult['app']))
	throw new \core\exceptions\VHttpEx('Invalid request', 404);


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

$arResult['app']['img'] = CFile::ShowImage($arResult['app']['logo_id'],180, 180, 'alt='.$arResult['app']['name']);

$arResult['app']['detail_text'] = html_entity_decode($arResult['app']['detail_text'],ENT_QUOTES);
$arResult['app']['registration'] = html_entity_decode($arResult['app']['registration'],ENT_QUOTES);
$arResult['app']['action'] = html_entity_decode($arResult['app']['action'],ENT_QUOTES);
$arResult['app']['undesired'] = html_entity_decode($arResult['app']['undesired'],ENT_QUOTES);
$arResult['app']['evidence'] = html_entity_decode($arResult['app']['evidence'],ENT_QUOTES);
$arResult['app']['contra'] = html_entity_decode($arResult['app']['contra'],ENT_QUOTES);
$arResult['app']['advantages'] = html_entity_decode($arResult['app']['advantages'],ENT_QUOTES);
$arResult['app']['func'] = html_entity_decode($arResult['app']['func'],ENT_QUOTES);
$arResult['app']['security'] = html_entity_decode($arResult['app']['security'],ENT_QUOTES);
$arResult['app']['procedure'] = html_entity_decode($arResult['app']['procedure'],ENT_QUOTES);
$arResult['app']['protocol'] = html_entity_decode($arResult['app']['protocol'],ENT_QUOTES);
$arResult['app']['specs'] = html_entity_decode($arResult['app']['specs'],ENT_QUOTES);
$arResult['app']['equipment'] = html_entity_decode($arResult['app']['equipment'],ENT_QUOTES);
$arResult['app']['effect'] = html_entity_decode($arResult['app']['effect'],ENT_QUOTES);
$arResult['app']['specialist'] = html_entity_decode($arResult['app']['specialist'],ENT_QUOTES);
$arResult['app']['patient'] = html_entity_decode($arResult['app']['patient'],ENT_QUOTES);
$arResult['app']['area'] = html_entity_decode($arResult['app']['area'],ENT_QUOTES);
$arResult['app']['mix'] = html_entity_decode($arResult['app']['mix'],ENT_QUOTES);
$arResult['app']['rules'] = html_entity_decode($arResult['app']['rules'],ENT_QUOTES);
$arResult['app']['acs'] = html_entity_decode($arResult['app']['acs'],ENT_QUOTES);

//получение галереи
$obQuery = $obApps->createQuery();
$obQuery->builder()->from('estelife_apparatus_photos');
$obQuery->builder()->filter()->_eq('apparatus_id', $arResult['app']['id']);
$arPhotos = $obQuery->select()->all();
if (!empty($arPhotos)){
	foreach ($arPhotos as $val){
		$file =  CFile::GetFileArray($val['original']);
		$arResult['app']['gallery'][] = $file['SRC'];
		$arResult['app']['gallery'][] = $val['type'];
	}
}


//получение регистрации
$obQuery = $obApps->createQuery();
$obQuery->builder()->from('estelife_apparatus_photos');
$obQuery->builder()->filter()->_eq('apparatus_id', $arResult['app']['id'])->_eq('type',2);
$arRegistration = $obQuery->select()->all();
$arResult['app']['registration_photo'] = array();
if (!empty($arRegistration)){
	foreach ($arRegistration as $key=>$val){
		$file =  CFile::ShowImage($val['original'],165, 220, 'alt='.$val['description']);
		$arResult['pill']['registration_photo'][$key]['file'] = $file;
		$arResult['app']['registration_photo'][$key]['desc'] = $val['description'];
	}
}


$arResult['app']['name']=trim(strip_tags(html_entity_decode($arResult['app']['name'], ENT_QUOTES, 'utf-8')));
$arResult['app']['seo_name']=VString::pregStrSeo($arResult['app']['name']);
$arResult['app']['seo_preview_text']=trim(strip_tags(html_entity_decode($arResult['app']['detail_text'], ENT_QUOTES, 'utf-8')));
$arResult['app']['seo_preview_text']=VString::pregStrSeo($arResult['app']['seo_preview_text']);

$APPLICATION->SetPageProperty("title", $arResult['app']['seo_name']);
$APPLICATION->SetPageProperty("description", VString::truncate($arResult['app']['seo_preview_text'],160,''));
$APPLICATION->SetPageProperty("keywords", "Estelife, Аппараты, ".$arResult['app']['seo_name']);


$this->IncludeComponentTemplate();