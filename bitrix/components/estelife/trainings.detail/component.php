<?php
use core\database\VDatabase;
use core\types\VArray;
use core\types\VString;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");
CModule::IncludeModule("estelife");

$obEvent = VDatabase::driver();
$nEventId=null;
$sEventName=null;

$nEventId =  (isset($arParams['ID'])) ?
	intval($arParams['ID']) : 0;

//Получаем данные по мероприятию
$obQuery = $obEvent->createQuery();
$obQuery->builder()->from('estelife_events', 'ee');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ee','id')
	->_to('estelife_company_events','event_id','ece')
	->_cond()->_eq('ece.is_owner',1);
$obJoin->_left()
	->_from('ece','company_id')
	->_to('estelife_companies','id','ec');
$obJoin->_left()
	->_from('ec','id')
	->_to('estelife_company_geo','company_id','ecg');
$obJoin->_left()
	->_from('ecg','country_id')
	->_to('iblock_element','ID','ct')
	->_cond()->_eq('ct.IBLOCK_ID',15);
$obJoin->_left()
	->_from('ecg','city_id')
	->_to('iblock_element','ID','cty')
	->_cond()->_eq('cty.IBLOCK_ID',16);
$obQuery->builder()
	->field('ct.NAME','country_name')
	->field('ct.ID','country_id')
	->field('cty.NAME','city_name')
	->field('ecg.address','address')
	->field('ecg.latitude','lat')
	->field('ecg.longitude','lng')
	->field('ec.logo_id', 'logo_id')
	->field('ec.id', 'company_id')
	->field('ec.name','company_name')
	->field('ee.id','id')
	->field('ee.short_name','short_name')
	->field('ee.full_name','full_name')
	->field('ee.detail_text','detail_text');

$obFilter=$obQuery->builder()->filter();

if(!is_null($nEventId))
	$obFilter->_eq('ee.id', $nEventId);
else
	$obFilter->_eq('ee.id', 0);

$arResult['event'] = $obQuery->select()->assoc();
$arResult['event']['img'] = CFile::ShowImage($arResult['event']['logo_id'],280, 110, 'alt='.$arResult['event']['name']);
$arResult['event']['detail_text'] = htmlspecialchars_decode($arResult['event']['detail_text'],ENT_NOQUOTES);
$arResult['event']['company_link']='/tc'.$arResult['event']['company_id'].'/';

if(!empty($arResult['event']['web'])){
	$arResult['event']['short_web']=VString::checkUrl($arResult['event']['web']);
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
//		$val['full_date'] = \core\types\VDate::date($val['date']);
		$arResult['event']['calendar'][] = $val['date'];
	}
}


$arResult['event']['calendar']=\core\types\VDate::createDiapasons($arResult['event']['calendar'],function(&$nFrom,&$nTo){
	$arNowTo = strtotime(date('d.m.Y', time()).' 00:00:00');
	$arNowFrom =strtotime(date('d.m.Y', time()).' 23:59:59');

	if (($arNowTo<=$nTo && $arNowFrom>=$nFrom) || ($arNowTo<=$nFrom) || ($arNowTo<=$nFrom && $arNowFrom>=$nFrom)){
		if($nTo==0){
			$nFrom=\core\types\VDate::date($nFrom, 'j F');
		}else{
			$arFrom=explode('.',date('n',$nFrom));
			$arTo=explode('.',date('n',$nTo));
			$sPattern='j F';

			if($arFrom[1]==$arTo[1])
				$sPattern=($arFrom[0]==$arTo[0]) ? 'j' : 'j F';

			$nFrom=\core\types\VDate::date($nFrom,$sPattern);
			$nTo=\core\types\VDate::date($nTo,'j F');
		}
		return false;
	}

	return true;
});

$arResult['event']['calendar']['first_period'] = reset($arResult['event']['calendar']);
$arD = preg_match("/^[0-9]+/", $arResult['event']['calendar']['first_period']['from'], $mathes);
$arResult['event']['calendar']['first_date'] =  $mathes[0]. ' <i>';

if (preg_match("/[а-я]+/u" ,$arResult['event']['calendar']['first_period']['from'], $mathes)){
	$arResult['event']['calendar']['first_date'] .= mb_substr($mathes[0], 0, 3, 'utf-8').'</i>';
}else{
	$arM = preg_match("/[а-я]+/u", $arResult['event']['calendar']['first_period']['to'], $mathes);
	$arResult['event']['calendar']['first_date'] .= mb_substr($mathes[0], 0, 3, 'utf-8').'</i>';
}
/*
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
			$val['full_address'] = $val['address'];
			$val['link'] = '/tc'.$val['company_id'].'/';
			$arResult['event']['main_org'] = $val;
		}else{
			$arResult['event']['org'][] = $val;
		}
	}
}*/

//Получение контактных данных
$obQuery = $obEvent->createQuery();
$obQuery->builder()->from('estelife_company_contacts', 'ecc');
$obQuery->builder()->filter()
	->_eq('company_id', $arResult['event']['company_id']);
$arContacts = $obQuery->select()->all();

if (!empty($arContacts)){
	foreach ($arContacts as $val){
		if ($val['type'] == 'email'){
			$arEmails[] = $val['value'];
		}elseif($val['type'] == 'fax'){
			$arFaxes[] = $val['value'];
		}elseif($val['type'] == 'phone'){
			$arPhones[] = VString::formatPhone($val['value'], true);
		}elseif($val['type'] == 'web'){
			$arWebs[] = $val['value'];
		}
	}
}

if (!empty($arEmails))
	$arResult['event']['contacts']['email'] = implode('<br />', $arEmails);
if (!empty($arFaxes))
	$arResult['event']['contacts']['fax'] = implode('<br />', $arFaxes);
if (!empty($arPhones))
	$arResult['event']['contacts']['phone'] = implode('<br />', $arPhones);

$arResult['event']['contacts']['lat']=$arResult['event']['lat'];
$arResult['event']['contacts']['lng']=$arResult['event']['lng'];

if(!empty($arWebs[0])){
	$arResult['event']['web'] = $arWebs[0];
	$arResult['event']['web_short'] = VString::checkUrl($arResult['event']['web']);
	$arResult['event']['contacts']['web'] = $arResult['event']['web'];
	$arResult['event']['contacts']['web_short']=$arResult['event']['web_short'];
}


$sFullName=mb_strtolower(trim(preg_replace('#[^\w\d\s\.\,\-а-я]+#iu','',$arResult['event']['full_name'])));
$APPLICATION->SetPageProperty("title", $arResult['event']['short_name']);
$APPLICATION->SetPageProperty("description", $sFullName,'utf-8');
$APPLICATION->SetPageProperty("keywords", "Estelife, учебный центр, ".$sFullName);

$this->IncludeComponentTemplate();