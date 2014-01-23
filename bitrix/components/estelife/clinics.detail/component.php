<?php
use core\database\VDatabase;
use core\types\VArray;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");
CModule::IncludeModule("estelife");

$obClinics = VDatabase::driver();
$nClinicID =  (isset($arParams['ID'])) ?
	intval($arParams['ID']) : 0;


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
	->field('ct.ID', 'city_id')
	->field('ct.CODE', 'city_code')
	->field('mt.NAME', 'metro')
	->field('eccp.value', 'phone')
	->field('eccw.value', 'web');
$obQuery->builder()->filter()
	->_eq('ec.id', $nClinicID);
$arResult['clinic'] = $obQuery->select()->assoc();

if (!empty($arResult['clinic']['preview_text'])){
	$arResult['clinic']['name'] = $arResult['clinic']['preview_text'];
}


$arResult['clinic']['main_contact'] = array(
	'city' => $arResult['clinic']['city'],
	'city_id' => $arResult['clinic']['city_id'],
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
	->_eq('clinic_id', $nClinicID);
$obQuery->builder()->field('name');
$arPays = $obQuery->select()->all();

if (!empty($arPays)){
	foreach ($arPays as $val){
		$arResult['clinic']['pays'][] = $val['name'];
	}
}

$arResult['clinic']['contacts'][$arResult['clinic']['id']] = array(
	'city' => $arResult['clinic']['city'],
	'address' => $arResult['clinic']['address'],
	'metro' => $arResult['clinic']['metro'],
	'phone' => \core\types\VString::formatPhone($arResult['clinic']['phone']),
	'web_short' => \core\types\VString::checkUrl($arResult['clinic']['web']),
	'web'=> $arResult['clinic']['web'],
	'pays'=> mb_strtolower(implode(', ', $arResult['clinic']['pays']), 'utf-8'),
	'name'=> $arResult['clinic']['name'],
	'lat'=>$arResult['clinic']['latitude'],
	'lng'=>$arResult['clinic']['longitude']
);

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
	->_eq('ecs.clinic_id', $nClinicID);
$arServices = $obQuery->select()->all();

foreach ($arServices as $val){
	$arResult['clinic']['specializations'][$val['s_id']] = $val;
}

foreach ($arServices as $val){
	$arResult['clinic']['service'][$val['ser_id']] = $val;
}

foreach ($arServices as $val){
	$val['price_from']=number_format($val['price_from'],0,'.',' ');
	$arResult['clinic']['con'][$val['con_id']] = $val;
}

foreach ($arResult['clinic']['specializations'] as $val){
	$arResult['clinic']['specializations_string'][] = $val['s_name'];
}

$arResult['clinic']['specializations_string'] = implode(', ', $arResult['clinic']['specializations_string']);

//Получаем галерею
$obQuery = $obClinics->createQuery();
$obQuery->builder()->from('estelife_clinic_photos');
$obQuery->builder()->filter()
	->_eq('clinic_id', $nClinicID);
$arResult['clinic']['gallery'] = $obQuery->select()->all();


if(!empty($arResult['clinic']['gallery'])){
	foreach($arResult['clinic']['gallery'] as &$arGallery){
		$file=CFile::GetFileArray($arGallery['original']);
		$arGallery['original']=$file['SRC'];
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
	->field('ea.small_photo','logo_id')
	->field('ea.view_type','view_type');
$obQuery->builder()->filter()
	->_eq('ecs.clinic_id', $nClinicID)
	->_eq('ea.active', 1)
	->_gte('ea.end_date', time());

$arActions = $obQuery->select()->all();



$arNow = time();
foreach ($arActions as $val){
	$val['time'] = ceil(($val['end_date']-$arNow)/(60*60*24));
	$val['day'] = \core\types\VString::spellAmount($val['time'], 'день,дня,дней');
	$val['link'] = '/pr'.$val['id'].'/';
	$val['new_price']=number_format($val['new_price'],0,'.',' ');
	$val['old_price']=number_format($val['old_price'],0,'.',' ');

	if(!empty($val['logo_id'])){
		$file=CFile::GetFileArray($val["logo_id"]);
		$val['logo']=$file['SRC'];
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
	->field('ec.longitude', 'lng')
	->field('ec.latitude', 'lat')
	->field('ct.NAME', 'city')
	->field('mt.NAME', 'metro')
	->field('eccp.value', 'phone')
	->field('eccw.value', 'web')
	->field('ec.id', 'id')
	->field('ec.name', 'name');

$obQuery->builder()->filter()
	->_eq('ec.clinic_id', $nClinicID);

$arResult['filial'] = $obQuery->select()->all();

foreach ($arResult['filial'] as $val){
	$arFilials[] = $val['id'];
	if(!empty($val['phone']))
		$val['phone']=\core\types\VString::formatPhone($val['phone']);

	if(!empty($val['web'])){
		$val['web_short']=\core\types\VString::checkUrl($val['web']);
	}

	$arResult['clinic']['contacts'][$val['id']] = $val;
}


if (!empty($arFilials)){
	$obQuery = $obClinics->createQuery();
	$obQuery->builder()->from('estelife_clinic_pays');
	$obQuery->builder()->filter()
		->_in('clinic_id', $arFilials);
	$arFilialsPays= $obQuery->select()->all();
}

$arPays = array();

if (!empty($arFilialsPays)){
	foreach ($arFilialsPays as $val){
		$arPays[$val['clinic_id']][] = $val['name'];
	}

	foreach ($arPays as $key=>$val){
		$arResult['clinic']['contacts'][$key]['pays'] =  mb_strtolower(implode(', ', $val), 'utf-8');
	}
}


if(!empty($arResult['clinic']['logo_id']))
	$arResult['clinic']['logo']=CFile::ShowImage($arResult['clinic']['logo_id'],200,85);

$arResult['clinic']['detail_text']=htmlspecialchars_decode($arResult['clinic']['detail_text'],ENT_NOQUOTES);

//получение города в родительском патеже
if (!empty($arResult['clinic']['city_id'])){
	$obRes = CIBlockElement::GetList(Array(), array("IBLOCK_ID"=>16,"ID"=>$arResult['clinic']['city_id']), false, false, array("PROPERTY_CITY"));
	$arCity = $obRes->Fetch();
	if (!empty($arCity['PROPERTY_CITY_VALUE'])){
		$arCity = $arCity['PROPERTY_CITY_VALUE'];
	}else{
		$arCity = $arResult['clinic']['city'];
	}
}

if (!empty($arCity))
	$arCity = ' в '.$arCity;
else
	$arCity = '';

$arResult['clinic']['name'] = trim(strip_tags(html_entity_decode($arResult['clinic']['name'], ENT_QUOTES, 'utf-8')));
$arResult['clinic']['seo_name'] = preg_replace('#[^\w\d\s\.\,\-\(\)]+#iu',' ',$arResult['clinic']['name']);

$arResult['clinic']['seo_title'] = 'Клиника '.$arResult['clinic']['seo_name'].$arCity.' - акции, цены, адреса';
$arResult['clinic']['seo_description'] = 'Акции, а так же цены и адреса клиники '.$arResult['clinic']['seo_name'].$arCity.'. Смотрите здесь.';

$APPLICATION->SetPageProperty("title", $arResult['clinic']['seo_title']);
$APPLICATION->SetPageProperty("description", $arResult['clinic']['seo_description']);


$this->IncludeComponentTemplate();