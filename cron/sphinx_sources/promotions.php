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
	->from('b_estelife_akzii','promotion')
	->field('promotion.id','id')
	->field('promotion.name','name')
	->field('promotion.preview_text','preview_text')
	->field('promotion.detail_text','detail_text')
	->field('promotion.date_edit','date_edit')
	->field('promotion.active','active')
	->field('promotion.start_date','start_date')
	->field('promotion.end_date','end_date')
	->field('specialization.name','specialization')
	->field('service.name','service')
	->field('service_concreate.name','service_concreate')
	->field('clinic.name','clinic')
	->field('clinic.address','address')
	->field('clinic.id','clinic_id')
	->field('city.NAME','city')
	->field('city.ID','city_id')
	->join();
$obJoin->_left()
	->_from('promotion','specialization_id')
	->_to('estelife_specializations','id','specialization');
$obJoin->_left()
	->_from('promotion','service_id')
	->_to('estelife_services','id','service');
$obJoin->_left()
	->_from('promotion','service_concreate_id')
	->_to('estelife_service_concreate','id','service_concreate');
$obJoin->_left()
	->_from('promotion','id')
	->_to('estelife_clinic_akzii','akzii_id','clinic_promotion');
$obJoin->_left()
	->_from('clinic_promotion','clinic_id')
	->_to('estelife_clinics','id','clinic');
$obJoin->_left()
	->_from('clinic','city_id')
	->_to('iblock_element','ID','city')
	->_cond()
	->_eq('city.IBLOCK_ID',16);

// TODO: Раскоментировать после первого запуска
//$obBuilder->filter()
//	->_gte('promotion.date_edit',time());

$nTime=time();
$arResult=$obQuery
	->select()
	->all();

if(!empty($arResult)){
	$arTemp=array();

	foreach($arResult as &$arValue){
		$arValue['address']=array($arValue['city'].', '.$arValue['address']);
		$arTemp[]=$arValue['clinic_id'];
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

	$arResult['clinic']['address']=array($arResult['city'].', '.$arResult['address']);

	if(!empty($arOffices)){
		foreach($arOffices as $arOffice){
			$nKey=array_search($arOffice['clinic_id'],$arTemp);
			$arResult[$nKey]['address'][]=$arOffice['city'].', '.$arOffice['address'];
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
	if($arValue['active']!=1 || $arValue['start_date']>$nTime || $arValue['end_date']<$nTime){
		$arKillList[]=$arValue['id'];
		continue;
	}

	$arValue['tags']=array();

	if(!empty($arValue['specialization']))
		$arValue['tags'][]=$arValue['specialization'];

	if(!empty($arValue['service']))
		$arValue['tags'][]=$arValue['service'];

	if(!empty($arValue['service_concreate']))
		$arValue['tags'][]=$arValue['service_concreate'];

	$arValue['tags'][]=$arValue['clinic'];
	$arValue['tags']=implode(', ',$arValue['tags']);
	$sSearchTags=$arValue['tags'];

	if(!empty($arValue['address']))
		$sSearchTags.=', '.implode(',',$arValue['address']);

	$sPreviewText=!empty($arValue['detail_text']) ?
		$arValue['detail_text'] :
		$arValue['preview_text'];

	$sPreviewText=trim(strip_tags(html_entity_decode($sPreviewText,ENT_QUOTES,'utf-8')));
	$sPreviewText=htmlspecialchars($sPreviewText,ENT_QUOTES,'utf-8');
	$sDescription=VString::truncate($sPreviewText,300);
	$sName=trim(htmlspecialchars(strip_tags($arValue['name']),ENT_QUOTES,'utf-8'));

	$sResult.='
		<sphinx:document id="'.$arTypes['pr'].$arValue['id'].'">
			<search-name>'.$sName.'</search-name>
			<search-category>Акции '.trim($arValue['city']).'</search-category>
			<search-preview></search-preview>
			<search-detail>'.$sPreviewText.'</search-detail>
			<search-tags>'.trim(htmlspecialchars(strip_tags($sSearchTags))).'</search-tags>
			<name>'.$sName.'</name>
			<description>'.$sDescription.'</description>
			<tags>'.$arValue['tags'].'</tags>
			<date_edit>'.strtotime($arValue['date_edit']).'</date_edit>
			<id>'.$arValue['id'].'</id>
			<type>'.$arTypes['pr'].'</type>
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