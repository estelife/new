<?php
use core\database\VDatabase;
use core\types\VArray;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");
CModule::IncludeModule("estelife");

$obActions = VDatabase::driver();
$nActionID= (isset($arParams['ID']))?
	intval($arParams['ID']) : 0;

//Получаем данные по акции
$obQuery = $obActions->createQuery();
$obQuery->builder()->from('estelife_akzii', 'ea');
$obQuery->builder()
	->field('ea.*');
$obQuery->builder()->filter()
	->_eq('ea.id', $nActionID);
$arResult['action'] = $obQuery->select()->assoc();

//получение клиник
$obQuery = $obActions->createQuery();
$obQuery->builder()->from('estelife_clinic_akzii', 'eca');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('eca','clinic_id')
	->_to('estelife_clinics','id','ec');
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
	->field('ct.NAME', 'city')
	->field('ct.CODE', 'city_code')
	->field('mt.NAME', 'metro')
	->field('ec.name', 'clinic_name')
	->field('ec.id', 'clinic_id')
	->field('ec.address', 'clinic_address')
	->field('eccp.value', 'phone')
	->field('eccw.value', 'web')
	->group('eca.clinic_id')
	->filter()
	->_eq('eca.akzii_id', $nActionID);

$arClinics=$obQuery->select()->all();
$arResult['action']['clinics'] = array();

if (!empty($arClinics)){
	foreach ($arClinics as $val){
		$val['link'] = '/cl'.$val['clinic_id'].'/';
		$val['phone']=\core\types\VString::formatPhone($val['phone']);
		$arResult['action']['clinics'][] = $val;
	}
}

//Получение условий акции
$obQuery = $obActions->createQuery();
$obQuery->builder()->from('estelife_akzii_prices');
$obQuery->builder()
	->filter()
	->_eq('akzii_id', $nActionID);
$arResult['action']['prices']=$obQuery->select()->all();

//Получение галереи
$obQuery=$obActions->createQuery();
$obQuery->builder()
	->from('estelife_akzii_photos')
	->filter()
	->_eq('akzii_id', $nActionID);

$arResult['action']['photos'] = $obQuery->select()->all();
$arResult['action']['photos_count']=0;
$arResult['action']['photo_desc']='';

if(!empty($arResult['action']['photos'])){
	foreach($arResult['action']['photos'] as $nKey=>&$arPhoto){
		$arPhoto['original']=CFile::ShowImage($arPhoto['original'],624);
		$arPhoto['description']=(!empty($arPhoto['description'])) ?
			html_entity_decode($arPhoto['description'],ENT_QUOTES,'utf-8') : '';

		if($nKey==0)
			$arResult['action']['photo_desc']=$arPhoto['description'];
	}

	$arResult['action']['photos_count']=count($arResult['action']['photos']);
}

$arResult['action']['detail_text']=html_entity_decode($arResult['action']['detail_text'], ENT_QUOTES, 'UTF-8');
$arResult['action']['new_price']=number_format($arResult['action']['base_new_price'],0,'.',' ');
$arResult['action']['old_price']=number_format($arResult['action']['base_old_price'],0,'.',' ');

$arResult['action']['day_count']=ceil(($arResult['action']['end_date']-time())/86400);
$arResult['action']['day_count']=$arResult['action']['day_count'].' '.\core\types\VString::spellAmount($arResult['action']['day_count'],'день,дня,дней');
$arResult['action']['end_date']=date('d.m.Y', $arResult['action']['end_date']);

$APPLICATION->SetPageProperty("title", mb_strtolower(trim(preg_replace('#[^\w\d\s\.\,\-а-я]+#iu','',$arResult['action']['name'])),'utf-8'));
$APPLICATION->SetPageProperty("description", mb_substr(strip_tags($arResult['action']['preview_text']), 0, 140, 'utf-8'));
$APPLICATION->SetPageProperty("keywords", "Estelife, Акции, Клиники, ".$arResult['action']['name']);

$this->IncludeComponentTemplate();