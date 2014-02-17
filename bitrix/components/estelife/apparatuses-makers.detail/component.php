<?php
use core\database\VDatabase;
use core\types\VArray;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");
CModule::IncludeModule("estelife");

$obCompanies = VDatabase::driver();
$nCompanyId=null;
$sCompanyName=null;

$nCompanyId= (isset($arParams['ID']))?
	intval($arParams['ID']) : 0;

//Получаем данные по клинике
$obQuery = $obCompanies->createQuery();
$obQuery->builder()->from('estelife_companies', 'ec');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ec', 'id')
	->_to('estelife_company_geo', 'company_id', 'ecg');
$obJoin->_left()
	->_from('ecg','country_id')
	->_to('iblock_element','ID','ct')
	->_cond()->_eq('ct.IBLOCK_ID',15);
$obJoin->_left()
	->_from('ec','id')
	->_to('estelife_company_contacts','company_id','ecc')
	->_cond()->_eq('ecc.type', 'web');
$obJoin->_left()
	->_from('ec','id')
	->_to('estelife_company_types','company_id','ect')
	->_cond()->_eq('ect.type', 3);
$obJoin->_left()
	->_from('ect', 'id')
	->_to('estelife_company_type_geo', 'company_id', 'ectg');
$obJoin->_left()
	->_from('ectg','country_id')
	->_to('iblock_element','ID','cttype')
	->_cond()->_eq('ct.IBLOCK_ID',15);
$obJoin->_left()
	->_from('ect','id')
	->_to('estelife_company_type_contacts','company_id','ectc')
	->_cond()->_eq('ectc.type', 'web');
$obQuery->builder()
	->field('ec.*')
	->field('ct.NAME', 'country_name')
	->field('ct.ID', 'country_id')
	->field('cttype.NAME', 'type_country_name')
	->field('cttype.ID', 'type_country_id')
	->field('ect.name', 'type_name')
	->field('ect.logo_id', 'type_logo_id')
	->field('ect.detail_text', 'type_detail_text')
	->field('ectc.value', 'type_web')
	->field('ecc.value', 'web');

$obFilter=$obQuery->builder()->filter();

if(!is_null($nCompanyId)){
	$obFilter->_eq('ec.id', $nCompanyId);
}else{
	$obFilter->_eq('ec.id',0);
}

$arResult['company'] = $obQuery->select()->assoc();

if (!empty($arResult['company']['type_name'])){
	$arResult['company']['name'] = $arResult['company']['type_name'];
}
unset($arResult['company']['type_name']);

if (!empty($arResult['company']['type_logo_id'])){
	$arResult['company']['logo_id'] = $arResult['company']['type_logo_id'];
}
unset($arResult['company']['type_logo_id']);
$arResult['company']['img'] = CFile::ShowImage($arResult['company']['logo_id'],180, 180, 'alt='.$arResult['company']['name']);

if (!empty($arResult['company']['type_country_name'])){
	$arResult['company']['country_name'] = $arResult['company']['type_country_name'];
	$arResult['company']['country_id'] = $arResult['company']['type_country_id'];
}
unset($arResult['company']['type_country_id']);
unset($arResult['company']['type_country_name']);

if (!empty($arResult['company']['type_web'])){
	$arResult['company']['web'] = $arResult['company']['type_web'];
}

if(!empty($arResult['company']['web']))
	$arResult['company']['web_short']=\core\types\VString::checkUrl($arResult['company']['web']);

unset($arResult['company']['type_web']);

if (!empty($arResult['company']['type_detail_text'])){
	$arResult['company']['detail_text'] = $arResult['company']['type_detail_text'];
}
unset($arResult['company']['type_detail_text']);

$arResult['company']['detail_text'] = nl2br(htmlspecialchars_decode($arResult['company']['detail_text'], ENT_NOQUOTES));

//Получение препаратов для данной компании
$obQuery = $obCompanies->createQuery();
$obQuery->builder()->from('estelife_apparatus');
$obQuery->builder()->sort('name','asc');
$obQuery->builder()->filter()
	->_eq('company_id', $arResult['company']['id']);
$arProductions = $obQuery->select()->all();

foreach ($arProductions as $val){
	$val['img'] = CFile::ShowImage($val['logo_id'],150, 150, 'alt='.$val['name']);
	$val['preview_text'] = \core\types\VString::truncate($val['preview_text'], 90, '...');
	$val['link'] = '/ap'.$val['id'].'/';
	$arResult['company']['production'][] = $val;
}

$arResult['company']['name'] = trim(strip_tags(html_entity_decode($arResult['company']['name'], ENT_QUOTES, 'utf-8')));
$arResult['company']['seo_description'] = \core\types\VString::pregStrSeo($arResult['company']['name']);
$arResult['company']['seo_name'] = \core\types\VString::pregStrSeo($arResult['company']['name'].', '.$arResult['company']['country_name'].' - информация о производителе аппаратов');


$APPLICATION->SetPageProperty("title", $arResult['company']['seo_name']);
$APPLICATION->SetPageProperty("description", \core\types\VString::truncate('Подробная информация о компании '.$arResult['company']['seo_description'].' - история, контактные данные и продукция. Узнайте подробнее у нас.',160,''));
$APPLICATION->SetPageProperty("keywords", "Производители апппаратов, ".$arResult['company']['seo_name']);

$this->IncludeComponentTemplate();