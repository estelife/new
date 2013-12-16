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
$arResult['company']['img'] = CFile::ShowImage($arResult['company']['logo_id'],200, 85, 'alt='.$arResult['company']['name']);

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
	$val['preview_text'] = \core\types\VString::truncate($val['preview_text'], 100, '...');
	$val['link'] = '/ap'.$val['id'].'/';
	$arResult['production'][] = $val;
}

//Получение обучений для данной компании
$obQuery = $obCompanies->createQuery();
$obQuery->builder()->from('estelife_company_events', 'ece');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ece', 'event_id')
	->_to('estelife_events', 'id', 'ee');
$obJoin->_left()
	->_from('ee', 'id')
	->_to('estelife_event_types', 'event_id', 'eet')
	->_cond()->_eq('type', 3);
$obJoin->_left()
	->_from('ee', 'id')
	->_to('estelife_company_events', 'event_id', 'ecec')
	->_cond()->_eq('ecec.is_owner', 1);
$obJoin->_left()
	->_from('ecec', 'company_id')
	->_to('estelife_companies', 'id', 'ec');
$obJoin->_left()
	->_from('ec', 'id')
	->_to('estelife_company_geo', 'company_id', 'ecg');
$obJoin->_left()
	->_from('ecg' ,'city_id')
	->_to('iblock_element','ID','ct')
	->_cond()->_eq('ct.IBLOCK_ID',16);
$obJoin->_left()
	->_from('ec', 'id')
	->_to('estelife_company_contacts','company_id','eccw')
	->_cond()->_eq('eccw.type', 'web');
$obJoin->_left()
	->_from('ec', 'id')
	->_to('estelife_company_contacts','company_id','eccp')
	->_cond()->_eq('eccp.type', 'phone');
$obJoin->_left()
	->_from('ec','id')
	->_to('estelife_company_types', 'company_id', 'ect')
	->_cond()->_eq('ect.type', 4);
$obJoin->_left()
	->_from('ect', 'id')
	->_to('estelife_company_type_geo', 'company_id', 'ecgt');
$obJoin->_left()
	->_from('ecgt' ,'city_id')
	->_to('iblock_element','ID','cttype')
	->_cond()->_eq('ct.IBLOCK_ID',16);
$obJoin->_left()
	->_from('ect', 'id')
	->_to('estelife_company_type_contacts','company_id','eccwt')
	->_cond()->_eq('eccwt.type', 'web');
$obJoin->_left()
	->_from('ect', 'id')
	->_to('estelife_company_contacts','company_id','eccpt')
	->_cond()->_eq('eccpt.type', 'phone');
$obQuery->builder()
	->field('ee.short_name', 'event_name')
	->field('ee.id', 'event_id')
	->field('ee.translit', 'translit')
	->field('ec.name', 'company_name')
	->field('ec.logo_id', 'company_logo')
	->field('ect.name', 'type_company_name')
	->field('ct.NAME', 'city')
	->field('cttype.NAME', 'type_city')
	->field('ecg.address', 'address')
	->field('ecgt.address', 'type_address')
	->field('eccw.value', 'web')
	->field('eccp.value', 'phone')
	->field('eccwt.value', 'type_web')
	->field('eccpt.value', 'type_phone');
$obQuery->builder()->filter()
	->_eq('ece.company_id', $arResult['company']['id'])
	->_eq('ece.is_owner', 0);
$obQuery->builder()->group('ee.id');
$arCompanies = $obQuery->select()->all();

foreach ($arCompanies as $val){
	$arIds[] = $val['event_id'];
	if (!empty($val['type_company_name'])){
		$val['company_name'] = $val['type_company_name'];
	}
	unset($val['type_company_name']);

	if (!empty($val['type_city'])){
		$val['city'] = $val['type_city'];
	}
	unset($val['type_city']);

	if (!empty($val['type_address'])){
		$val['address'] = $val['type_address'];
	}
	unset($val['type_address']);

	if (!empty($val['type_web'])){
		$val['web'] = $val['type_web'];
	}

	if(!empty($val['web']))
		$val['web_short']=\core\types\VString::checkUrl($val['web']);

	unset($val['type_web']);

	if (!empty($val['type_phone'])){
		$val['phone'] = $val['type_phone'];
	}
	unset($val['type_phone']);
	$val['phone'] = \core\types\VString::formatPhone($val['phone']);

	if (!empty($val['type_company_logo'])){
		$val['company_logo'] = $val['type_company_logo'];
	}
	unset($val['type_company_logo']);

	$val['img'] = CFile::ShowImage($val['company_logo'],150, 140, 'alt='.$val['company_name']);
	$val['link'] = '/training/'.$val['translit'].'/';

	$arResult['training'][$val['event_id']] = $val;
}


//Получение расписаний
if (!empty($arIds)){
	$obQuery = $obCompanies->createQuery();
	$obQuery->builder()->from('estelife_calendar');
	$obQuery->builder()->filter()->_in('event_id', $arIds);
	$obQuery->builder()->sort('date', 'desc');
	$arCalendar = $obQuery->select()->all();


	foreach ($arCalendar as $val){
		$val['full_date'] = \core\types\VDate::date($val['date']);
		$arResult['training'][$val['event_id']]['calendar'][] = $val;
	}
}
$arResult['company']['seo_description'] = mb_substr(strip_tags($arResult['company']['preview_text']), 0, 140, 'utf-8');

$APPLICATION->SetPageProperty("title", mb_strtolower(trim(preg_replace('#[^\w\d\s\.\,\-а-я]+#iu','',$arResult['company']['name'])),'utf-8'));
$APPLICATION->SetPageProperty("description", $arResult['company']['seo_description']);
$APPLICATION->SetPageProperty("keywords", "Estelife, Производители апппаратов, ".$arResult['company']['name']);

$this->IncludeComponentTemplate();