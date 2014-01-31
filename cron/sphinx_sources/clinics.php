<?php
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
	->field('city.NAME','city')
	->field('city.ID','city_id')
	->join();
$obJoin->_left()
	->_from('clinic','city_id')
	->_to('iblock_element','ID','city')
	->_cond()->_eq('city.IBLOCK_ID',16);
$obBuilder->filter()
	->_gte('clinic.date_edit',time());

$arResult=$obQuery
	->select()
	->all();

if(!empty($arResult)){
	$arTemp=array();

	foreach($arResult as $arValue)
		$arTemp[]=$arValue['id'];

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

	$sResult.='
		<sphinx:document id="'.$arValue['id'].'">
			<search-name>'.trim($arValue['name']).'</search-name>
			<search-category>Клиники '.trim($arValue['city']).'</search-category>
			<search-preview><![CDATA[['.trim(strip_tags($arValue['preview_text'])).']]></search-preview>
			<search-detail><![CDATA[['.trim(strip_tags($arValue['detail_text'])).']]></search-detail>
			<search-tags>'.trim($arValue['tags']).'</search-tags>
			<name>'.$arValue['name'].'</name>
			<description>'.htmlspecialchars($arValue['preview_text'],ENT_QUOTES,'utf-8').'</description>
			<tags>'.$arValue['tags'].'</tags>
			<date_edit>'.strtotime($arValue['date_edit']).'</date_edit>
			<id>'.$arValue['id'].'</id>
			<type>'.$arTypes['cl'].'</type>
			<city>'.$arTypes['city_id'].'</city>
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