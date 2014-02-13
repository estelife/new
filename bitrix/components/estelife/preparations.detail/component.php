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

if ($arParams['PREFIX']=='ps'){
	$nType=1;
	$arResult['type']="Препараты";
	$arResult['type_link']="/preparations/";
}elseif ($arParams['PREFIX']=='th'){
	$nType=2;
	$arResult['type']="Нити";
	$arResult['type_link']="/threads/";
}else{
	$nType=3;
	$arResult['type']="Имплантаты";
	$arResult['type_link']="/implants/";
}

//Получаем данные по препарату
$obQuery = $obPills->createQuery();
$obQuery->builder()->from('estelife_pills', 'ep');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ep', 'company_id')
	->_to('estelife_companies', 'id', 'ec');
$obJoin->_left()
	->_from('ep','type_id')
	->_to('iblock_element','ID','pt')
	->_cond()->_eq('pt.IBLOCK_ID',28);
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
	$obFilter->_eq('ep.id',$nCompanyId);
}else{
	$obFilter->_eq('ep.id',0);
}

$arResult['pill'] = $obQuery->select()->assoc();


if (!empty($arResult['pill']['type_company_name'])){
	$arResult['pill']['company_name'] = $arResult['pill']['type_company_name'];
	$arResult['pill']['company_id'] = $arResult['pill']['type_company_id'];
}
unset($arResult['pill']['type_company_name']);
unset($arResult['pill']['type_company_id']);

if (!empty($arResult['pill']['type_country_name'])){
	$arResult['pill']['country_name'] = $arResult['pill']['type_country_name'];
	$arResult['pill']['country_id'] = $arResult['pill']['type_country_id'];
}
unset($arResult['pill']['type_country_name']);
unset($arResult['pill']['type_country_id']);

if (!empty($arResult['pill']['type_company_translit'])){
	$arResult['pill']['company_translit'] = $arResult['pill']['type_company_translit'];
}
unset($arResult['pill']['type_company_translit']);

if (!empty($arResult['pill']['type_company_id'])){
	$arResult['pill']['company_id'] = $arResult['pill']['type_company_id'];
}
unset($arResult['pill']['type_company_id']);

$arResult['pill']['company_link'] = '/pm'.$arResult['pill']['company_id'].'/';

$arResult['pill']['img'] = CFile::ShowImage($arResult['pill']['logo_id'],200, 85, 'alt='.$arResult['pill']['name']);

$arResult['pill']['detail_text'] = nl2br(htmlspecialchars_decode($arResult['pill']['detail_text'],ENT_NOQUOTES));
$arResult['pill']['registration'] = nl2br(htmlspecialchars_decode($arResult['pill']['registration'],ENT_NOQUOTES));
$arResult['pill']['action'] = nl2br(htmlspecialchars_decode($arResult['pill']['action'],ENT_NOQUOTES));
$arResult['pill']['undesired'] = nl2br(htmlspecialchars_decode($arResult['pill']['undesired'],ENT_NOQUOTES));
$arResult['pill']['evidence'] = nl2br(htmlspecialchars_decode($arResult['pill']['evidence'],ENT_NOQUOTES));
$arResult['pill']['structure'] = nl2br(htmlspecialchars_decode($arResult['pill']['structure'],ENT_NOQUOTES));
$arResult['pill']['effect'] = nl2br(htmlspecialchars_decode($arResult['pill']['effect'],ENT_NOQUOTES));
$arResult['pill']['form'] = nl2br(htmlspecialchars_decode($arResult['pill']['form'],ENT_NOQUOTES));
$arResult['pill']['contra'] = nl2br(htmlspecialchars_decode($arResult['pill']['contra'],ENT_NOQUOTES));
$arResult['pill']['usage'] = nl2br(htmlspecialchars_decode($arResult['pill']['usage'],ENT_NOQUOTES));
$arResult['pill']['storage'] = nl2br(htmlspecialchars_decode($arResult['pill']['storage'],ENT_NOQUOTES));
$arResult['pill']['advantages'] = nl2br(htmlspecialchars_decode($arResult['pill']['advantages'],ENT_NOQUOTES));
$arResult['pill']['area'] = nl2br(htmlspecialchars_decode($arResult['pill']['area'],ENT_NOQUOTES));
$arResult['pill']['security'] = nl2br(htmlspecialchars_decode($arResult['pill']['security'],ENT_NOQUOTES));
$arResult['pill']['mix'] = nl2br(htmlspecialchars_decode($arResult['pill']['mix'],ENT_NOQUOTES));
$arResult['pill']['protocol'] = nl2br(htmlspecialchars_decode($arResult['pill']['protocol'],ENT_NOQUOTES));
$arResult['pill']['specs'] = nl2br(htmlspecialchars_decode($arResult['pill']['specs'],ENT_NOQUOTES));

//получение галереи
$obQuery = $obPills->createQuery();
$obQuery->builder()->from('estelife_pill_photos');
$obQuery->builder()->filter()->_eq('pill_id', $arResult['pill']['id']);
$arPhotos = $obQuery->select()->all();
if (!empty($arPhotos)){
	foreach ($arPhotos as $val){
		$file =  CFile::GetFileArray($val['original']);
		$arResult['pill']['gallery'][] = $file['SRC'];
	}
}

//Получение других препаратов для данной компании
$obQuery = $obPills->createQuery();
$obQuery->builder()->from('estelife_pills');
$obQuery->builder()->filter()
	->_eq('company_id', $arResult['pill']['company_id'])
	->_ne('id', $arResult['pill']['id']);
$obQuery->builder()->slice(0,3);
$arProductions = $obQuery->select()->all();

foreach ($arProductions as $val){
	$val['img'] = CFile::ShowImage($val['logo_id'],150, 140, 'alt='.$val['name']);
	$val['preview_text'] = \core\types\VString::truncate($val['preview_text'], 100, '...');
	$val['link'] = '/'.$arParams['PREFIX'].$val['id'].'/';
	$arResult['pill']['production'][] = $val;
}

$arResult['pill']['name'] = trim(strip_tags(html_entity_decode($arResult['pill']['name'], ENT_QUOTES, 'utf-8')));
$arResult['pill']['seo_name'] = VString::pregStrSeo($arResult['pill']['name']);

$arResult['pill']['seo_preview_text'] = trim(strip_tags(html_entity_decode($arResult['pill']['detail_text'], ENT_QUOTES, 'utf-8')));
$arResult['pill']['seo_preview_text'] = VString::pregStrSeo($arResult['pill']['seo_preview_text']);

$APPLICATION->SetPageProperty("title", $arResult['pill']['seo_name']);
$APPLICATION->SetPageProperty("description", VString::truncate($arResult['pill']['seo_preview_text'],160,''));
$APPLICATION->SetPageProperty("keywords", "Estelife, препараты, ".mb_strtolower($arResult['pill']['seo_name']));

$this->IncludeComponentTemplate();