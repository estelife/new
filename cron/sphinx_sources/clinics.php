<?php
use core\types\VString;

$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__).'/../../');
$DOCUMENT_ROOT =$_SERVER["DOCUMENT_ROOT"];

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
@set_time_limit(0);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/estelife/prolog.php");

CModule::IncludeModule('estelife');
$arTypes=$arData=$APPLICATION->IncludeComponent(
	"estelife:system-settings","",
	array('filter'=>'types')
);
$arTypes=array_flip($arTypes);

$obDriver=\core\database\VDatabase::driver();
$obQuery=$obDriver->createQuery();
$obBuilder=$obQuery->builder();
$obJoin=$obBuilder
	->from('estelife_clinics','clinic')
	->field('clinic.id','id')
	->field('clinic.name','name')
	->field('clinic.preview_text','preview_text')
	->field('clinic.detail_text','detail_text')
	->field('clinic.date_edit','date_edit')
	->field('clinic.active','active')
	->field('clinic.address','address')
	->field('city.NAME','city')
	->field('city.ID','city_id')
	->join();
$obJoin->_left()
	->_from('clinic','city_id')
	->_to('iblock_element','ID','city')
	->_cond()
	->_eq('city.IBLOCK_ID',16);
// TODO: раскоментировать после 1-го  запуска
$obBuilder->filter()
	->_eq('clinic.clinic_id',0);
//	->_gte('clinic.date_edit',time());

$arResult=$obQuery
	->select()
	->all();

if(!empty($arResult)){
	$arTemp=array();

	foreach($arResult as &$arValue){
		$arValue['address']=array($arValue['city'].', '.$arValue['address']);
		$arTemp[]=$arValue['id'];
	}

	// Получаем список специализаций
	$obQuery=$obDriver->createQuery();
	$obBuilder=$obQuery->builder();
	$obJoin=$obBuilder
		->from('estelife_clinic_services', 'clinic_services')
		->field('specialization.name','specialization_name')
		->field('services.name','service_name')
		->field('service_concreate.name','service_concreate_name')
		->field('specialization.name','specialization_name')
		->field('clinic_services.clinic_id','clinic_id')
		->sort('clinic_services.clinic_id','asc')
		->sort('clinic_services.specialization_id','asc')
		->sort('clinic_services.service_id','asc')
		->sort('clinic_services.service_concreate_id','asc')
		->join();

	$obJoin->_left()
		->_from('clinic_services','specialization_id')
		->_to('estelife_specializations','id','specialization');
	$obJoin->_left()
		->_from('clinic_services','service_id')
		->_to('estelife_services','id','services');
	$obJoin->_left()
		->_from('clinic_services','service_concreate_id')
		->_to('estelife_service_concreate','id','service_concreate');

	$obBuilder->filter()
		->_in('ecs.clinic_id', $arTemp);

	$arSpecs=$obQuery
		->select()
		->all();

	if(!empty($arSpecs)){
		foreach($arSpecs as $arSpec){
			$nClinicKey=array_search($arSpec['clinic_id'],$arTemp);

			if(!isset($arResult[$nClinicKey]['tags']))
				$arResult[$nClinicKey]['tags']=array();

			if(!in_array($arSpec['specialization_name'],$arResult[$nClinicKey]['tags']))
				$arResult[$nClinicKey]['tags'][]=$arSpec['specialization_name'];

			if(!in_array($arSpec['service_name'],$arResult[$nClinicKey]['tags']))
				$arResult[$nClinicKey]['tags'][]=$arSpec['service_name'];

			if(!in_array($arSpec['service_concreate_name'],$arResult[$nClinicKey]['tags']))
				$arResult[$nClinicKey]['tags'][]=$arSpec['service_concreate_name'];
		}
	}

	// Получаем список филлиалов
	$obDriver=\core\database\VDatabase::driver();
	$obQuery=$obDriver->createQuery();
	$obBuilder=$obQuery->builder();
	$obJoin=$obBuilder
		->from('estelife_clinics','clinic')
		->field('clinic.address','address')
		->field('city.NAME','city')
		->field('clinic.clinic_id','clinic_id')
		->join();
	$obJoin->_left()
		->_from('clinic','city_id')
		->_to('iblock_element','ID','city')
		->_cond()->_eq('city.IBLOCK_ID',16);
	$obBuilder->filter()
		->_eq('clinic.active',1)
		->_in('clinic.clinic_id',$arTemp);

	$arOffices=$obQuery
		->select()
		->all();

	if(!empty($arOffices)){
		foreach($arOffices as $arOffice){
			$nClinicKey=array_search($arOffice['clinic_id'],$arTemp);
			$arResult[$nClinicKey]['address'][]=$arOffice['city'].', '.$arOffice['address'];
		}
	}
}


$arKillList=array();

$sResult='<?xml version="1.0" encoding="utf-8"?>';
$sResult.='<sphinx:docset>';
$sResult.='
<sphinx:schema>
	<sphinx:field name="search-name"/>
	<sphinx:field name="search-category"/>
	<sphinx:field name="search-preview"/>
	<sphinx:field name="search-detail"/>
	<sphinx:field name="search-tags"/>
	<sphinx:attr name="name" type="string" />
	<sphinx:attr name="description" type="string" />
	<sphinx:attr name="tags" type="string" default="" />
	<sphinx:attr name="date_edit" type="timestamp" />
	<sphinx:attr name="id" type="int" bits="16" default="0" />
	<sphinx:attr name="type" type="int" bits="16" default="0" />
	<sphinx:attr name="city" type="int" bits="16" default="0" />
</sphinx:schema>
';

$nTime=time();

foreach($arResult as $arValue){
	if($arValue['active']!=1){
		$arKillList[]=$arValue['id'];
		continue;
	}

	$arValue['tags']=(isset($arValue['tags'])) ?
		implode(',',$arValue['tags']) : '';
	$sSearchTags=trim($arValue['tags'].(!empty($arValue['address']) ? ', '.implode(',',$arValue['address']) : ''));
	$sSearchTags=htmlspecialchars($sSearchTags,ENT_QUOTES,'utf-8');
	$arValue['tags']=htmlspecialchars($arValue['tags'],ENT_QUOTES,'utf-8');

	$sPreviewText=!empty($arValue['detail_text']) ?
		$arValue['detail_text'] :
		$arValue['preview_text'];
	$sPreviewText=trim(strip_tags(htmlspecialchars_decode($sPreviewText),ENT_QUOTES,'utf-8'));
	$sPreviewText=htmlspecialchars($sPreviewText,ENT_QUOTES,'utf-8');
	$sDescription=(!empty($sPreviewText)) ?
		VString::truncate($sPreviewText,300) :
		'К сожалению, на данный момент клиника не предоставила нам официальные данные об оказываемых услугах и проводимых акциях.';

	$sName=trim(htmlspecialchars(strip_tags($arValue['name']),ENT_QUOTES,'utf-8'));
	$sSearchName=trim(preg_replace('/(клиник|clinic)[а-яa-z]*/iu','',$sName));

	$sResult.='
		<sphinx:document id="'.$arTypes['cl'].$arValue['id'].'">
			<search-name><![CDATA[['.$sSearchName.']]></search-name>
			<search-category>Клиники '.trim($arValue['city']).'</search-category>
			<search-preview></search-preview>
			<search-detail><![CDATA[['.$sPreviewText.']]></search-detail>
			<search-tags><![CDATA[['.$sSearchTags.']]></search-tags>
			<name>'.$sName.'</name>
			<description>'.$sDescription.'</description>
			<tags>'.$arValue['tags'].'</tags>
			<date_edit>'.$arValue['date_edit'].'</date_edit>
			<id>'.$arValue['id'].'</id>
			<type>'.$arTypes['cl'].'</type>
			<city>'.$arValue['city_id'].'</city>
		</sphinx:document>
	';
}

if(!empty($arKillList)){
	$sResult.='<sphinx:killlist>';

	foreach($arKillList as $nId)
		$sResult.='<id>'.$nId.'</id>';

	$sResult.='</sphinx:killlist>';
}

$sResult.='</sphinx:docset>';
echo $sResult;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");