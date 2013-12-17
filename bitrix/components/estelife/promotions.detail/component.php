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
$obQuery->builder()
	->from('estelife_akzii', 'ea')
	->field('ea.*')
	->field('fl.SUBDIR','big_photo_dir')
	->field('fl.FILE_NAME','big_photo_name')
	->field('fl.DESCRIPTION','big_photo_description')
	->filter()
	->_eq('ea.id', $nActionID);
$obQuery->builder()
	->join()
	->_left()
	->_from('ea','big_photo')
	->_to('file','ID','fl');
$arResult['action'] = $obQuery->select()->assoc();

if(!empty($arResult['action']['big_photo_name'])){
	$arResult['action']['big_photo']=array(
		'src'=>'/upload/'.$arResult['action']['big_photo_dir'].'/'.$arResult['action']['big_photo_name'],
		'description'=>$arResult['action']['big_photo_description']
	);
	unset($arResult['action']['big_photo_dir'],$arResult['action']['big_photo_name'],$arResult['action']['big_photo_description']);
}

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
	->field('ct.ID', 'city_id')
	->field('mt.NAME', 'metro')
	->field('ec.name', 'clinic_name')
	->field('ec.id', 'clinic_id')
	->field('ec.address', 'clinic_address')
	->field('eccp.value', 'phone')
	->field('eccw.value', 'web')
	->group('eca.clinic_id')
	->filter()
	->_eq('ec.clinic_id', 0)
	->_eq('eca.akzii_id', $nActionID);

$arResult['action']['clinics'] =$obQuery->select()->assoc();

if (!empty($arResult['action']['clinics'])){
	$arResult['action']['clinics']['link'] = '/cl'.$arResult['action']['clinics']['clinic_id'].'/';
	$arResult['action']['clinics']['phone']=\core\types\VString::formatPhone($arResult['action']['clinics']['phone']);
}

//Получение условий акции
$obQuery = $obActions->createQuery();
$obQuery->builder()->from('estelife_akzii_prices');
$obQuery->builder()
	->filter()
	->_eq('akzii_id', $nActionID);
$arResult['action']['prices']=$obQuery->select()->all();

//Получение галереи
/*
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
}*/

$arNow = time();
//Получение похожих акций
$obQuery = $obActions->createQuery();
$obQuery->builder()->from('estelife_akzii');
$obQuery->builder()
	->filter()
	->_eq('service_id', $arResult['action']['service_id'])
	->_eq('active', 1)
	->_ne('id',$arResult['action']['id'])
	->_gte('end_date', time());
$obQuery->builder()->slice(0,3);
$arSimilar=$obQuery->select()->all();
if (!empty($arSimilar)){
	foreach ($arSimilar as $val){
		$val['img'] = CFile::GetFileArray($val["small_photo"]);
		$val['src'] = $val['img']['SRC'];
		$val['new_price'] = intval($val['base_new_price']);
		$val['old_price'] = intval($val['base_old_price']);
		$val['time'] = ceil(($val['end_date']-$arNow)/(60*60*24));
		$val['day'] = \core\types\VString::spellAmount($val['time'], 'день,дня,дней');
		$val['link'] = '/pr'.$val['id'].'/';
		$arResult['action']['similar'][] = $val;
	}
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