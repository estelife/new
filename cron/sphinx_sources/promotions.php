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
	->from('b_estelife_akzii','promotion')
	->field('promotion.id','id')
	->field('promotion.name','name')
	->field('promotion.preview_text','preview_text')
	->field('promotion.detail_text','detail_text')
	->field('promotion.date_edit','date_edit')
	->field('promotion.active','active')
	->field('specialization.name','specialization')
	->field('service.name','service')
	->field('service_concreate.name','service_concreate')
	->field('clinic.name','clinic')
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
	->_cond()->_eq('city.IBLOCK_ID',16);
// TODO: Раскоментировать после первого запуска
//$obBuilder->filter()
//	->_gte('promotion.date_edit',time());

$arResult=$obQuery
	->select()
	->all();

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

	$arValue['tags']=array();
	$arValue['tags'][]=$arValue['specialization'];
	$arValue['tags'][]=$arValue['service'];
	$arValue['tags'][]=$arValue['service_concreate'];
	$arValue['tags'][]=$arValue['clinic'];

	$sResult.='
		<sphinx:document id="'.$arValue['id'].'">
			<search-name>'.trim(htmlspecialchars(strip_tags($arValue['name']),ENT_QUOTES,'utf-8')).'</search-name>
			<search-category>Акции клиник '.trim($arValue['city']).'</search-category>
			<search-preview><![CDATA[['.trim(strip_tags($arValue['preview_text'])).']]></search-preview>
			<search-detail><![CDATA[['.trim(strip_tags($arValue['detail_text'])).']]></search-detail>
			<search-tags>'.trim($arValue['tags']).'</search-tags>
			<name>'.htmlspecialchars($arValue['name'],ENT_QUOTES,'utf-8').'</name>
			<description>'.htmlspecialchars($arValue['preview_text'],ENT_QUOTES,'utf-8').'</description>
			<tags>'.$arValue['tags'].'</tags>
			<date_edit>'.strtotime($arValue['date_edit']).'</date_edit>
			<id>'.$arValue['id'].'</id>
			<type>'.$arTypes['pr'].'</type>
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