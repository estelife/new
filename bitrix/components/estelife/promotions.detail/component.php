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

$arResult['action'] = $obQuery
	->select()
	->assoc();

if(!empty($arResult['action']['big_photo_name'])){
	$arResult['action']['big_photo']=array(
		'src'=>'/upload/'.$arResult['action']['big_photo_dir'].'/'.$arResult['action']['big_photo_name'],
		'description'=>$arResult['action']['big_photo_description']
	);
	unset($arResult['action']['big_photo_dir'],$arResult['action']['big_photo_name'],$arResult['action']['big_photo_description']);
}

//получение типов акций
$obQuery=$obActions->createQuery();
$obQuery->builder()
	->from('estelife_akzii_types')
	->field('specialization_id')
	->field('service_id')
	->field('service_concreate_id')
	->filter()
	->_eq('akzii_id', $arResult['action']['id']);

$arServices=$obQuery
	->select()
	->all();

if (!empty($arServices)){
	foreach ($arServices as $val){
		if(!empty($val['service_id']))
			$arResult['service'][] = $val['service_id'];

		if(!empty($val['specialization_id']))
			$arResult['specialization'][] = $val['specialization_id'];

		if(!empty($val['service_concreate_id']))
			$arResult['service_concreate'][] = $val['service_concreate_id'];
	}
}

$arResult['specialization'] = array_values(array_unique($arResult['specialization']));
$arResult['service'] = array_values(array_unique($arResult['service']));
$arResult['service_concreate'] = array_values(array_unique($arResult['service_concreate']));

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
	->field('ct.NAME', 'city_name')
	->field('ct.CODE', 'city_code')
	->field('ct.ID', 'city_id')
	->field('mt.NAME', 'metro')
	->field('ec.name', 'name')
	->field('ec.more_information', 'more_information')
	->field('ec.latitude', 'latitude')
	->field('ec.longitude', 'longitude')
	->field('ec.clinic_id', 'clinic_id')
	->field('ec.id', 'id')
	->field('ec.address', 'address')
	->field('eccp.value', 'phone')
	->field('eccw.value', 'web')
	->group('eca.clinic_id')
	->filter()
	->_eq('eca.akzii_id', $nActionID);

$arClinics=$obQuery
	->select()
	->all();

if (!empty($arClinics)){
	$arCurrent=array();
	$arOffices=array();

	foreach($arClinics as $arClinic){
		$arClinic['link']='/cl'.$arClinic['id'].'/';
		$arClinic['phone']=\core\types\VString::formatPhone($arClinic['phone']);
		$arClinic['web_short']=\core\types\VString::checkUrl($arClinic['web']);

		if($arClinic['clinic_id']==0)
			$arCurrent['main']=$arClinic;
		else
			$arOffices[]=$arClinic;
	}

	if (empty($arCurrent['main'])){
		$arCurrent['main']=array_shift($arOffices);
		$arCurrent['main']['id']=$arCurrent['main']['clinic_id'];
		$arCurrent['main']['link']='/cl'.$arCurrent['main']['id'].'/';
	}

	$arCurrent['offices']=$arOffices;
	$arResult['action']['clinic']=$arCurrent;
	unset($arCurrent,$arOffices,$arClinics);
}

//Получение условий акции
$obQuery = $obActions->createQuery();
$obQuery->builder()->from('estelife_akzii_prices');
$obQuery->builder()
	->filter()
	->_eq('akzii_id', $nActionID);

$arResult['action']['prices']=$obQuery->select()->all();

$arNow = time();

//Получение похожих акций
$obQuery=$obActions->createQuery();
$obBuilder=$obQuery->builder();
$obJoin=$obBuilder
	->from('estelife_akzii','ea')
	->sort($obQuery->builder()->_rand())
	->field('ea.*')
	->field('ec.name','clinic_name')
	->field('ec.id','clinic_id')
	->join();
$obJoin->_left()
	->_from('ea','id')
	->_to('estelife_clinic_akzii','akzii_id','eca');
$obJoin->_left()
	->_from('eca','clinic_id')
	->_to('estelife_clinics','id','ec');
$obJoin->_left()
	->_from('ea', 'id')
	->_to('estelife_akzii_types', 'akzii_id', 'eat');
$obFilter=$obBuilder
	->slice(0,3)
	->group('ea.id')
	->filter()
	->_eq('ea.active', 1)
	->_ne('ea.id',$arResult['action']['id'])
	->_gte('ea.end_date', time());

if(!empty($arResult['service_concreate']))
	$obFilter->_or()->_in('eat.service_concreate_id',$arResult['service']);
if(!empty($arResult['service']))
	$obFilter->_or()->_in('eat.service_id',$arResult['service']);
if(!empty($arResult['specialization']))
	$obFilter->_or()->_in('eat.specialization_id',$arResult['specialization']);

if(!empty($arResult['action']['clinic']['main']))
	$obFilter->_eq('ec.city_id',$arResult['action']['clinic']['main']['city_id']);

$arSimilar=$obQuery
	->select()
	->all();

if (!empty($arSimilar)){
	foreach ($arSimilar as $val){
		$val['img'] = CFile::GetFileArray($val["small_photo"]);
		$val['src'] = $val['img']['SRC'];
		$val['new_price'] = number_format($val['base_new_price'],0,'.',' ');
		$val['old_price'] = number_format($val['base_old_price'],0,'.',' ');
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
$arResult['action']['end_date_format']=date('d.m.Y', $arResult['action']['end_date']);
$arResult['action']['now'] = time();

$arResult['action']['clinic']['name'] = trim(strip_tags(html_entity_decode($arResult['action']['clinic']['name'], ENT_QUOTES, 'utf-8')));
$arResult['action']['clinic']['seo_name'] = \core\types\VString::pregStrSeo($arResult['action']['clinic']['name']);

$arResult['action']['preview_text'] = trim(strip_tags(html_entity_decode($arResult['action']['preview_text'], ENT_QUOTES, 'utf-8')));
$arResult['action']['seo_preview_text'] = \core\types\VString::pregStrSeo($arResult['action']['preview_text']);

if (!empty($arResult['action']['clinic']['city_name']))
	$arCity = ' ('.$arResult['action']['clinic']['city_name'].')';
else
	$arCity = '';

$APPLICATION->SetPageProperty("title", trim($arResult['action']['seo_preview_text'].' - акция '.$arResult['action']['clinic']['seo_name']));
$APPLICATION->SetPageProperty("description", trim($arResult['action']['clinic']['seo_name'].' предлагает новую акцию - '.$arResult['action']['seo_preview_text'].'. Узнайте больше и получите скидку уже сейчас.'));

$this->IncludeComponentTemplate();