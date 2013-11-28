<?php
use core\database\VDatabase;
use core\types\VArray;
use core\types\VString;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");
CModule::IncludeModule("estelife");

$obEvent=VDatabase::driver();
$sEventName=null;
$nEventId=null;

if(isset($arParams['EVENT_NAME']) && strlen($arParams['EVENT_NAME'])>0){
	$sEventName=strip_tags($arParams['EVENT_NAME']);
}elseif(isset($arParams['EVENT_ID']) && strlen($arParams['EVENT_ID'])>0){
	$nEventId=intval($arParams['EVENT_ID']);
}

//Получаем данные по мероприятию
$obQuery = $obEvent->createQuery();
$obQuery->builder()->from('estelife_events', 'ee');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ee','country_id')
	->_to('iblock_element','ID','ct')
	->_cond()->_eq('ct.IBLOCK_ID',15);
$obJoin->_left()
	->_from('ee','city_id')
	->_to('iblock_element','ID','cty')
	->_cond()->_eq('cty.IBLOCK_ID',16);
$obQuery->builder()
	->field('ct.NAME','country_name')
	->field('cty.NAME','city_name')
	->field('ee.*');

$obFilter = $obQuery->builder()->filter();

if(!is_null($sEventName))
	$obFilter->_eq('ee.translit', $sEventName);
else if(!is_null($nEventId))
	$obFilter->_eq('ee.id', $nEventId);
else
	$obFilter->_eq('ee.id', 0);

$arResult['event'] = $obQuery->select()->assoc();
$arResult['event']['img'] = CFile::ShowImage($arResult['event']['logo_id'],280, 120, 'alt='.$arResult['event']['name']);
$arResult['event']['detail_text'] = htmlspecialchars_decode($arResult['event']['detail_text'],ENT_NOQUOTES);

if(!empty($arResult['event']['web'])){
	$arResult['event']['short_web']=VString::checkUrl($arResult['event']['web']);
}

if(!empty($arResult['event']['dop_web'])){
	$arResult['event']['short_dop_web']=VString::checkUrl($arResult['event']['dop_web']);
}

//Получение дат проведения
$obQuery = $obEvent->createQuery();
$obQuery->builder()
	->from('estelife_calendar')
	->sort('date','asc');
$obQuery->builder()->filter()
	->_eq('event_id', $arResult['event']['id']);
//	->_gte('date',strtotime(date('d.m.Y 00:00')));

$arCalendar = $obQuery->select()->all();
if (!empty($arCalendar)){
	foreach ($arCalendar as $val){
		$val['full_date'] = \core\types\VDate::date($val['date']);
		$arResult['event']['calendar'][] = $val;
	}
}

//Получение организаторов
$obQuery = $obEvent->createQuery();
$obQuery->builder()->from('estelife_company_events', 'ece');
$obJoin = $obQuery->builder()->join();
$obJoin->_left()
	->_from('ece', 'company_id')
	->_to('estelife_companies', 'id', 'ec');
$obJoin->_left()
	->_from('ec', 'id')
	->_to('estelife_company_geo', 'company_id', 'ecg');
$obJoin->_left()
	->_from('ec', 'id')
	->_to('estelife_company_contacts', 'company_id', 'ecc')
	->_cond()->_eq('type', 'web');
$obJoin->_left()
	->_from('ecg','country_id')
	->_to('iblock_element','ID','ct')
	->_cond()->_eq('ct.IBLOCK_ID',15);
$obJoin->_left()
	->_from('ecg','city_id')
	->_to('iblock_element','ID','cty')
	->_cond()->_eq('cty.IBLOCK_ID',16);
$obQuery->builder()
	->field('ec.name', 'company_name')
	->field('ec.id', 'company_id')
	->field('ecg.address', 'address')
	->field('ct.NAME', 'country_name')
	->field('cty.NAME', 'city_name')
	->field('ecc.value', 'web')
	->field('ece.is_owner', 'is_owner');
$obQuery->builder()->filter()
	->_eq('ece.event_id', $arResult['event']['id']);
$arCompanies = $obQuery->select()->all();

if (!empty($arCompanies)){
	foreach ($arCompanies as $val){
		if ($val['is_owner'] == 1){
			if (!empty($val['city_name'])){
				$val['city_name'] = 'г. '. $val['city_name'];
			};
//			$val['full_address'] = $val['country_name'].' '.$val['city_name'].' '.$val['address'];
			$val['full_address'] = $val['address'];

			if(!empty($val['web']))
				$val['short_web']=VString::checkUrl($val['web']);

			$arResult['event']['main_org'] = $val;
		}else{
			$arResult['event']['org'][] = $val;
		}
	}
}

//Получение контактных данных
$obQuery = $obEvent->createQuery();
$obQuery->builder()->from('estelife_event_contacts', 'ece');
$obQuery->builder()->filter()
	->_eq('event_id', $arResult['event']['id']);
$arContacts = $obQuery->select()->all();

if (!empty($arContacts)){
	foreach ($arContacts as $val){
		if ($val['type'] == 'email'){
			$arEmails[] = $val['value'];
		}elseif($val['type'] == 'fax'){
			$arFaxes[] = $val['value'];
		}elseif($val['type'] == 'phone'){
			$arPhones[] = $val['value'];
		}
	}
}

if (!empty($arEmails)){
	$arResult['event']['contacts']['email'] = implode(', ', $arEmails);
}
if (!empty($arFaxes)){
	$arResult['event']['contacts']['fax'] = implode(', ', $arFaxes);
}
if (!empty($arPhones)){
	$arResult['event']['contacts']['phone'] = implode(', ', $arPhones);
}

$APPLICATION->SetPageProperty("title", "Estelife - ".trim(preg_replace('#[^\w\d\s\.\,\-а-я]+#iu','',$arResult['event']['short_name'])));
$APPLICATION->SetPageProperty("description", trim(preg_replace('#[^\w\d\s\.\,\-а-я]+#iu','',$arResult['event']['full_name'])));
$APPLICATION->SetPageProperty("keywords", "Estelife, События, Мероприятия, ".$arResult['event']['short_name']);

$this->IncludeComponentTemplate();