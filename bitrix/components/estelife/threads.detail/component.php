<?php
use core\database\VDatabase;
use core\types\VArray;
use core\types\VString;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");
CModule::IncludeModule("estelife");

$obPills = VDatabase::driver();
$sCompanyName=null;
$nCompanyId=null;

$nCompanyId =  (isset($arParams['ID'])) ?
	intval($arParams['ID']) : 0;

$arResult['type']="Нити";
$arResult['type_link']="/threads/";


//Получаем данные по препарату
$obQuery = $obPills->createQuery();
$obQuery->builder()->from('estelife_threads', 'ep');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ep', 'company_id')
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
	->field('ep.*')
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
	$obFilter->_eq('ep.id',$nCompanyId);
}else{
	$obFilter->_eq('ep.id',0);
}

$arResult['pill'] = $obQuery->select()->assoc();
if (empty($arResult['pill']))
	throw new \core\exceptions\VHttpEx('Invalid request', 404);


if (!empty($arResult['pill']['type_company_name'])){
	$arResult['pill']['company_name'] = $arResult['pill']['type_company_name'];
	$arResult['pill']['company_id'] = $arResult['pill']['type_company_id'];
}

if (!empty($arResult['pill']['type_country_name'])){
	$arResult['pill']['country_name'] = $arResult['pill']['type_country_name'];
	$arResult['pill']['country_id'] = $arResult['pill']['type_country_id'];
}

if (!empty($arResult['pill']['type_company_translit'])){
	$arResult['pill']['company_translit'] = $arResult['pill']['type_company_translit'];
}

if (!empty($arResult['pill']['type_company_id'])){
	$arResult['pill']['company_id'] = $arResult['pill']['type_company_id'];
}
unset(
	$arResult['pill']['type_company_id'],
	$arResult['pill']['type_company_translit'],
	$arResult['pill']['type_country_id'],
	$arResult['pill']['type_country_name'],
	$arResult['pill']['type_company_name'],
	$arResult['pill']['type_company_id']
);

$arResult['pill']['company_link'] = '/pm'.$arResult['pill']['company_id'].'/';
$arResult['pill']['img'] = CFile::ShowImage($arResult['pill']['logo_id'],180, 180, 'alt='.$arResult['pill']['name']);

$arResult['pill']['detail_text'] = html_entity_decode($arResult['pill']['detail_text'],ENT_QUOTES);
$arResult['pill']['action'] =  html_entity_decode($arResult['pill']['action'],ENT_QUOTES);
$arResult['pill']['evidence'] = html_entity_decode($arResult['pill']['evidence'],ENT_QUOTES);
$arResult['pill']['contra'] = html_entity_decode($arResult['pill']['contra'],ENT_QUOTES);
$arResult['pill']['structure'] = html_entity_decode($arResult['pill']['structure'],ENT_QUOTES);
$arResult['pill']['registration'] = html_entity_decode($arResult['pill']['registration'],ENT_QUOTES);
$arResult['pill']['advantages'] = html_entity_decode($arResult['pill']['advantages'],ENT_QUOTES);
$arResult['pill']['usage'] = html_entity_decode($arResult['pill']['usage'],ENT_QUOTES);
$arResult['pill']['area'] = html_entity_decode($arResult['pill']['area'],ENT_QUOTES);
$arResult['pill']['effect'] = html_entity_decode($arResult['pill']['effect'],ENT_QUOTES);
$arResult['pill']['security'] = html_entity_decode($arResult['pill']['security'],ENT_QUOTES);
$arResult['pill']['mix'] = html_entity_decode($arResult['pill']['mix'],ENT_QUOTES);
$arResult['pill']['specs'] = html_entity_decode($arResult['pill']['specs'],ENT_QUOTES);
$arResult['pill']['protocol'] = html_entity_decode($arResult['pill']['protocol'],ENT_QUOTES);
$arResult['pill']['form'] = html_entity_decode($arResult['pill']['form'],ENT_QUOTES);
$arResult['pill']['storage'] = html_entity_decode($arResult['pill']['storage'],ENT_QUOTES);
$arResult['pill']['undesired'] = html_entity_decode($arResult['pill']['undesired'],ENT_QUOTES);
$arResult['pill']['specialist'] = html_entity_decode($arResult['pill']['specialist'],ENT_QUOTES);
$arResult['pill']['patient'] = html_entity_decode($arResult['pill']['patient'],ENT_QUOTES);

//получение галереи
$obQuery = $obPills->createQuery();
$obQuery->builder()->from('estelife_threads_photos');
$obQuery->builder()->filter()->_eq('thread_id', $arResult['pill']['id']);
$arPhotos = $obQuery->select()->all();
if (!empty($arPhotos)){
	foreach ($arPhotos as $val){
		$file =  CFile::GetFileArray($val['original']);
		$arResult['pill']['gallery'][] = $file['SRC'];
	}
}

//получение регистрации
$obQuery = $obPills->createQuery();
$obQuery->builder()->from('estelife_threads_photos');
$obQuery->builder()->filter()->_eq('thread_id', $arResult['pill']['id'])->_eq('type',2);
$arRegistration = $obQuery->select()->all();
$arResult['pill']['registration_photo'] = array();
if (!empty($arRegistration)){
	foreach ($arRegistration as $key=>$val){
		$file =  CFile::ShowImage($val['original'],165, 220, 'alt='.$val['description']);
		$arResult['pill']['registration_photo'][$key]['file'] = $file;
		$arResult['pill']['registration_photo'][$key]['desc'] = $val['description'];
	}
}

$arResult['pill']['name'] = trim(strip_tags(html_entity_decode($arResult['pill']['name'], ENT_QUOTES, 'utf-8')));
$arResult['pill']['seo_name'] = VString::pregStrSeo($arResult['pill']['name']);

$arResult['pill']['seo_preview_text'] = trim(strip_tags(html_entity_decode($arResult['pill']['detail_text'], ENT_QUOTES, 'utf-8')));
$arResult['pill']['seo_preview_text'] = VString::pregStrSeo($arResult['pill']['seo_preview_text']);

$APPLICATION->SetPageProperty("title", $arResult['pill']['seo_name']);
$APPLICATION->SetPageProperty("description", VString::truncate($arResult['pill']['seo_preview_text'],160,''));
$APPLICATION->SetPageProperty("keywords", "Estelife, нити, ".mb_strtolower($arResult['pill']['seo_name']));

$this->IncludeComponentTemplate();
