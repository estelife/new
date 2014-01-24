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

$nEventId =  (isset($arParams['ID'])) ?
	intval($arParams['ID']) : 0;

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

if(!is_null($nEventId))
	$obFilter->_eq('ee.id', $nEventId);
else
	$obFilter->_eq('ee.id', 0);

$arResult['event'] = $obQuery->select()->assoc();
$arResult['event']['img'] = CFile::ShowImage($arResult['event']['logo_id'],280, 120, 'alt='.$arResult['event']['name']);
$arResult['event']['detail_text'] = htmlspecialchars_decode($arResult['event']['detail_text'],ENT_NOQUOTES);

if(!empty($arResult['event']['web']))
	$arResult['event']['short_web']=VString::checkUrl($arResult['event']['web'],true);

if(!empty($arResult['event']['dop_web'])){
	$arResult['event']['short_dop_web']=VString::checkUrl($arResult['event']['dop_web']);
}

if(!empty($arResult['event']['dop_address'])){
	$arResult['event']['dop_address']=html_entity_decode($arResult['event']['dop_address'],ENT_QUOTES,'utf-8');
}
if(!empty($arResult['event']['address'])){
	$arResult['event']['address']=html_entity_decode($arResult['event']['address'],ENT_QUOTES,'utf-8');
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

$nNow=time();
$arResult['event']['calendar']=\core\types\VDate::createDiapasons($arResult['event']['calendar'],function(&$nFrom,&$nTo) use($nNow){
	$nNowTo=strtotime(date('d.m.Y', $nNow).' 00:00:00');
	$nNowFrom=strtotime(date('d.m.Y', $nNow).' 23:59:59');
	$nTempTo=$nTo;
	$nTempFrom=$nFrom;

	if($nTo==0){
		$nFrom=\core\types\VDate::date($nFrom, 'j F Y');
	}else{
		$arFrom=explode('.',date('n',$nFrom));
		$arTo=explode('.',date('n',$nTo));
		$sPattern='j F';

		if($arFrom[1]==$arTo[1])
			$sPattern=($arFrom[0]==$arTo[0]) ? 'j' : 'j F';

		$nFrom=\core\types\VDate::date($nFrom,$sPattern);
		$nTo=\core\types\VDate::date($nTo,'j F Y');
	}


	if(($nNowTo<=$nTempTo && $nNowFrom>=$nTempFrom) || ($nNowTo<=$nTempFrom) || ($nNowTo<=$nTempFrom && $nNowFrom>=$nTempFrom))
		return false;

	return true;
});
$arResult['event']['calendar']['first_period'] = end($arResult['event']['calendar']);
$arD = preg_match("/^[0-9]+/", $arResult['event']['calendar']['first_period']['from'], $mathes);
$arResult['event']['calendar']['first_date'] =  $mathes[0]. ' <i>';

if (preg_match("/[а-я]+/u" ,$arResult['event']['calendar']['first_period']['from'], $mathes)){
	$arResult['event']['calendar']['first_date'] .= mb_substr($mathes[0], 0, 3, 'utf-8').'</i>';
}else{
	$arM = preg_match("/[а-я]+/u", $arResult['event']['calendar']['first_period']['to'], $mathes);
	$arResult['event']['calendar']['first_date'] .= mb_substr($mathes[0], 0, 3, 'utf-8').'</i>';
}


//Получение направлений
$obQuery = $obEvent->createQuery();
$obFilter=$obQuery->builder()
	->from('estelife_event_directions')
	->filter()
	->_eq('event_id', $nEventId);
$arDirections = $obQuery->select()->all();

$arDirectionsName = array(
	'1'=>'Пластическая хирургия',
	'2'=>'Косметология',
	'3'=>'Косметика',
	'4'=>'Дерматология',
	'11'=>'Менеджмент',
);

foreach ($arDirections as $key=>$val){
	$val['name'] = $arDirectionsName[$val['type']];
	$arResult['event']['directions'][] = mb_strtolower($val['name'],'utf-8');
}
if (!empty($arResult['event']['directions'])){
	$arResult['event']['directions'] = implode(', ', $arResult['event']['directions']);
}

//Получение формата
$obQuery = $obEvent->createQuery();
$obFilter=$obQuery->builder()
	->from('estelife_event_types')
	->filter()
	->_eq('event_id', $nEventId);
$arTypes = $obQuery->select()->all();

$arTypesName = array(
	'1'=>'Форум',
	'2'=>'Выставка',
	'4'=>'Тренинг',
);

foreach ($arTypes as $key=>$val){
	$val['name'] = $arTypesName[$val['type']];
	$arResult['event']['types'][] = mb_strtolower($val['name'],'utf-8');
}
if (!empty($arResult['event']['types'])){
	$arResult['event']['types'] = implode(', ', $arResult['event']['types']);
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

$arTime = $arResult['event']['calendar']['first_period']['from'];
if(!empty($arResult['event']['calendar']['first_period']['to'])){
	$arTime.=' - '.$arResult['event']['calendar']['first_period']['to'];
}

$arResult['event']['short_name'] = trim(strip_tags(html_entity_decode($arResult['event']['short_name'], ENT_QUOTES, 'utf-8')));
$arResult['event']['seo_short_name'] = preg_replace('#[^\w\d\s\.\,\-\(\)]+#iu', '', $arResult['event']['short_name']);
$arResult['event']['seo_title'] = $arResult['event']['seo_short_name'].' - '.$arTime.', '.$arResult['event']['country_name'].', '.$arResult['event']['city_name'];
$arResult['event']['seo_description'] = 'В городе '.$arResult['event']['city_name'].' ('.$arResult['event']['country_name'].')'. ' '.$arTime.' проводится '.$arResult['event']['seo_short_name'].'. Вся информация здесь.';

$APPLICATION->SetPageProperty("title", $arResult['event']['seo_title']);
$APPLICATION->SetPageProperty("description", $arResult['event']['seo_description']);

$this->IncludeComponentTemplate();