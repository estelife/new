<?php
use core\database\VDatabase;
use core\types\VArray;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");
CModule::IncludeModule("estelife");

$obCompanies = VDatabase::driver();

$arCompanyID =  (isset($arParams['ID'])) ?
	intval($arParams['ID']) : 0;

//Получаем данные по организаторам
$obQuery = $obCompanies->createQuery();
$obQuery->builder()->from('estelife_companies', 'ec');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ec', 'id')
	->_to('estelife_company_geo', 'company_id', 'ecg');
$obJoin->_left()
	->_from('ec','id')
	->_to('estelife_company_types','company_id','ect')
	->_cond()->_eq('ect.type', 2);
$obJoin->_left()
	->_from('ect', 'id')
	->_to('estelife_company_type_geo', 'company_id', 'ectg');
$obQuery->builder()
	->field('ec.*')
	->field('ecg.address')
	->field('ectg.type_address')
	->field('ect.name', 'type_name')
	->field('ect.logo_id', 'type_logo_id')
	->field('ect.detail_text', 'type_detail_text')
	->field('ect.id', 'type_id')
	->field('ecg.country_id', 'country_id')
	->field('ectg.country_id', 'type_country_id');
$obQuery->builder()->filter()
	->_eq('ec.id', $arCompanyID);
$arResult['company'] = $obQuery->select()->assoc();

if (!empty($arResult['company']['type_name'])){
	$arResult['company']['name'] = $arResult['company']['type_name'];
}
unset($arResult['company']['type_name']);

if (!empty($arResult['company']['type_address'])){
	$arResult['company']['address'] = $arResult['company']['type_address'];
}
unset($arResult['company']['type_address']);

if (!empty($arResult['company']['type_country_id'])){
	$arResult['company']['country_id'] = $arResult['company']['type_country_id'];
}
unset($arResult['company']['type_address']);

if (!empty($arResult['company']['type_logo_id'])){
	$arResult['company']['logo_id'] = $arResult['company']['type_logo_id'];
}
unset($arResult['company']['type_logo_id']);
$arResult['company']['img'] = CFile::ShowImage($arResult['company']['logo_id'],200, 85, 'alt='.$arResult['company']['name']);

if (!empty($arResult['company']['type_detail_text'])){
	$arResult['company']['detail_text'] = $arResult['company']['type_detail_text'];
}
unset($arResult['company']['type_detail_text']);

$arResult['company']['detail_text'] = htmlspecialchars_decode($arResult['company']['detail_text'], ENT_NOQUOTES);


//Получение контактов для компании
$obQuery = $obCompanies->createQuery();
$obQuery->builder()->from('estelife_company_contacts');
$obQuery->builder()->filter()
	->_eq('company_id', $arCompanyID);
$arContacts = $obQuery->select()->all();
if (!empty($arContacts)){
	foreach ($arContacts as $val){
		if ($val['type'] == 'web'){
			$arResult['company']['web'][] = $val['value'];
		}elseif ($val['type'] == 'phone'){
			$arResult['company']['phone'][] = $val['value'];
		}elseif ($val['type'] == 'fax'){
			$arResult['company']['fax'][] = $val['value'];
		}elseif ($val['type'] == 'email'){
			$arResult['company']['email'][] = $val['value'];
		}
	}
}

//Получение контактов для типа компании
if (!empty($arResult['company']['type_id'])){
	$obQuery = $obCompanies->createQuery();
	$obQuery->builder()->from('estelife_company_type_contacts');
	$obQuery->builder()->filter()
		->_eq('company_id', $arResult['company']['type_id']);
	$arContacts = $obQuery->select()->all();
	if (!empty($arContacts)){
		foreach ($arContacts as $val){
			if ($val['type'] == 'web'){
				$arResult['company']['type_web'][] = $val['value'];
			}elseif ($val['type'] == 'phone'){
				$arResult['company']['type_phone'][] = \core\types\VString::formatPhone($val['value'], true);
			}elseif ($val['type'] == 'fax'){
				$arResult['company']['type_fax'][] = \core\types\VString::formatPhone($val['value'], true);
			}elseif ($val['type'] == 'email'){
				$arResult['company']['type_email'][] = $val['value'];
			}
		}
	}
}

if (!empty($arResult['company']['type_web'])){
	$arResult['company']['web'] = $arResult['company']['type_web'];
	unset($arResult['company']['type_web']);
}
$arResult['company']['web'] = reset($arResult['company']['web']);
$arResult['company']['web_short']=\core\types\VString::checkUrl($arResult['company']['web']);
if (!empty($arResult['company']['type_phone'])){
	$arResult['company']['phone'] = $arResult['company']['type_phone'];
	unset($arResult['company']['type_phone']);
}
if (!empty($arResult['company']['type_fax'])){
	$arResult['company']['fax'] = $arResult['company']['type_fax'];
	unset($arResult['company']['type_fax']);
}
if (!empty($arResult['company']['type_email'])){
	$arResult['company']['email'] = $arResult['company']['type_email'];
	unset($arResult['company']['type_email']);
}
$arResult['company']['contacts']['web_short'] = $arResult['company']['web_short'];
$arResult['company']['contacts']['web'] = $arResult['company']['web'];
$arResult['company']['contacts']['phone'] = implode('<br />', $arResult['company']['phone']);
$arResult['company']['contacts']['fax'] = implode('<br />', $arResult['company']['fax']);
$arResult['company']['contacts']['email'] = implode('<br />', $arResult['company']['email']);

$arResult['company']['name'] = trim(strip_tags(html_entity_decode($arResult['company']['name'], ENT_QUOTES, 'utf-8')));
$arResult['company']['preview_text'] = trim(strip_tags(html_entity_decode($arResult['company']['preview_text'], ENT_QUOTES, 'utf-8')));

$arResult['company']['seo_name'] = preg_replace('#[^\w\d\s\.\,\-\(\)]+#iu',' ',$arResult['company']['name']);
$arResult['company']['seo_preview_text'] = preg_replace('#[^\w\d\s\.\,\-\(\)]+#iu',' ',$arResult['company']['preview_text']);

$APPLICATION->SetPageProperty("title", $arResult['company']['seo_name']);
$APPLICATION->SetPageProperty("description", \core\types\VString::truncate($arResult['company']['seo_preview_text'],160,''));
$APPLICATION->SetPageProperty("keywords", "Estelife, организаторы, ".mb_strtolower($arResult['company']['seo_name'],'utf-8'));

$this->IncludeComponentTemplate();