<?php
use core\database\VDatabase;
use core\types\VArray;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");
CModule::IncludeModule("estelife");

$obClinics = VDatabase::driver();

if (isset($arParams['CLINIC_NAME']) && strlen($arParams['CLINIC_NAME'])>0){
	if(preg_match('#([\d]+)$#',$arParams['CLINIC_NAME'],$arMatches)){
		$arClinicID = intval($arMatches[1]);
	}
}else{
	$arClinicID = 0;
}

//Получаем данные по клинике
$obQuery = $obClinics->createQuery();
$obQuery->builder()->from('estelife_clinics', 'ec');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ec','city_id')
	->_to('iblock_element','ID','ct')
	->_cond()->_eq('ct.IBLOCK_ID',16);
$obJoin->_left()
	->_from('ec','metro_id')
	->_to('iblock_element','ID','mt')
	->_cond()->_eq('mt.IBLOCK_ID',17);
$obJoin->_left()
	->_from('ec','id')
	->_to('estelife_clinic_contacts','clinic_id','eccp')
	->_cond()->_eq('eccp.type', 'phone');
$obJoin->_left()
	->_from('ec','id')
	->_to('estelife_clinic_contacts','clinic_id','eccw')
	->_cond()->_eq('eccw.type', 'web');
$obQuery->builder()
	->field('ec.*')
	->field('ct.NAME', 'city')
	->field('ct.CODE', 'city_code')
	->field('mt.NAME', 'metro')
	->field('eccp.value', 'phone')
	->field('eccw.value', 'web');
$obQuery->builder()->filter()
	->_eq('ec.id', $arClinicID);
$arResult['clinic'] = $obQuery->select()->assoc();

$arResult['clinic']['contacts'][] = array(
	'city' => $arResult['clinic']['city'],
	'address' => $arResult['clinic']['address'],
	'metro' => $arResult['clinic']['metro'],
	'phone' => \core\types\VString::formatPhone($arResult['clinic']['phone']),
	'web_short' => \core\types\VString::checkUrl($arResult['clinic']['web']),
	'web'=>$arResult['clinic']['web']
);

//Получаем платежи
$obQuery = $obClinics->createQuery();
$obQuery->builder()->from('estelife_clinic_pays');
$obQuery->builder()->filter()
	->_eq('clinic_id', $arClinicID);
$arResult['clinic']['pays'] = $obQuery->select()->all();

//получаем услуги
$obQuery = $obClinics->createQuery();
$obQuery->builder()->from('estelife_clinic_services', 'ecs');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ecs','specialization_id')
	->_to('estelife_specializations','id','es');
$obJoin->_left()
	->_from('ecs','service_id')
	->_to('estelife_services','id','eser');
$obJoin->_left()
	->_from('ecs','service_concreate_id')
	->_to('estelife_service_concreate','id','econ');
$obQuery->builder()
	->field('es.name','s_name')
	->field('es.id','s_id')
	->field('eser.name','ser_name')
	->field('eser.id','ser_id')
	->field('econ.name','con_name')
	->field('econ.id','con_id')
	->field('ecs.price_from');
$obQuery->builder()->filter()
	->_eq('ecs.clinic_id', $arClinicID);
$arServices = $obQuery->select()->all();

foreach ($arServices as $val){
	$arResult['clinic']['specialization'][$val['s_id']] = $val;
}

foreach ($arServices as $val){
	$arResult['clinic']['service'][$val['ser_id']] = $val;
}

foreach ($arServices as $val){
	$arResult['clinic']['con'][$val['con_id']] = $val;
}

//Получаем галерею
$obQuery = $obClinics->createQuery();
$obQuery->builder()->from('estelife_clinic_photos');
$obQuery->builder()->filter()
	->_eq('clinic_id', $arClinicID);
$arResult['clinic']['gallery'] = $obQuery->select()->all();

if(!empty($arResult['clinic']['gallery'])){
	foreach($arResult['clinic']['gallery'] as &$arGallery){
		$file=CFile::GetFileArray($arGallery['original']);
		$arGallery['original']=$file['src'];
	}
}

//Получаем акции
$obQuery = $obClinics->createQuery();
$obQuery->builder()->from('estelife_clinic_akzii', 'ecs');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ecs','akzii_id')
	->_to('estelife_akzii','id','ea');
$obQuery->builder()
	->field('ea.id','id')
	->field('ea.name','name')
	->field('ea.end_date','end_date')
	->field('ea.base_old_price','old_price')
	->field('ea.base_new_price','new_price')
	->field('ea.base_sale','sale')
	->field('ea.big_photo','logo_id');
$obQuery->builder()->filter()
	->_eq('ecs.clinic_id', $arClinicID);
$arActions = $obQuery->select()->all();

$arNow = time();
foreach ($arActions as $val){
	$val['time'] = ceil(($val['end_date']-$arNow)/(60*60*24));
	$val['day'] = \core\types\VString::spellAmount($val['time'], 'день,дня,дней');
	$val['link'] = '/promotions/'.\core\types\VString::translit($val['name']).'-'.$val['id'].'/';

	if(!empty($val['logo_id'])){
		$file=CFile::ResizeImageGet($val["logo_id"], array('width'=>303, 'height'=>143), BX_RESIZE_IMAGE_EXACT, true);
		$val['logo']=$file['src'];
	}

	$arResult['clinic']['akzii'][]=$val;
}

//Получаем филиалы
$obQuery = $obClinics->createQuery();
$obQuery->builder()->from('estelife_clinics', 'ec');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ec','city_id')
	->_to('iblock_element','ID','ct')
	->_cond()->_eq('ct.IBLOCK_ID',16);
$obJoin->_left()
	->_from('ec','metro_id')
	->_to('iblock_element','ID','mt')
	->_cond()->_eq('mt.IBLOCK_ID',17);
$obJoin->_left()
	->_from('ec','id')
	->_to('estelife_clinic_contacts','clinic_id','eccp')
	->_cond()->_eq('eccp.type', 'phone');
$obJoin->_left()
	->_from('ec','id')
	->_to('estelife_clinic_contacts','clinic_id','eccw')
	->_cond()->_eq('eccw.type', 'web');
$obQuery->builder()
	->field('ec.address', 'address')
	->field('ct.NAME', 'city')
	->field('mt.NAME', 'metro')
	->field('eccp.value', 'phone')
	->field('eccw.value', 'web');
$obQuery->builder()->filter()
	->_eq('ec.clinic_id', $arClinicID);
$arResult['filial'] = $obQuery->select()->all();

foreach ($arResult['filial'] as $val){
	if(!empty($val['phone']))
		$val['phone']=\core\types\VString::formatPhone($val['phone']);

	if(!empty($val['web'])){
		$val['web_short']=\core\types\VString::checkUrl($val['web']);
	}

	$arResult['clinic']['contacts'][] = $val;
}

$arResult['clinic']['contacts_count']=count($arResult['clinic']['contacts']);

if(!empty($arResult['clinic']['logo_id']))
	$arResult['clinic']['logo']=CFile::ShowImage($arResult['clinic']['logo_id'],200,85);

$arResult['clinic']['detail_text']=htmlspecialchars_decode($arResult['clinic']['detail_text'],ENT_NOQUOTES);
$arResult['clinic']['seo_description'] = mb_substr(strip_tags($arResult['clinic']['preview_text']), 0, 140, 'utf-8');

$arResult['clinic']['count'] = count($arResult['clinic']['contacts']);



$APPLICATION->SetPageProperty("title", "Estelife - ".mb_strtolower(trim(preg_replace('#[^\w\d\s\.\,\-а-я]+#iu','',$arResult['clinic']['name'])),'utf-8'));
$APPLICATION->SetPageProperty("description", $arResult['clinic']['seo_description']);
$APPLICATION->SetPageProperty("keywords", "Estelife, Акции, Клиники, ".$arResult['clinic']['name']);


$this->IncludeComponentTemplate();