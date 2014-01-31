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

$arTypes=array(
	3=>$arTypes['ns'],
	36=>$arTypes['pt'],
	35=>$arTypes['ex'],
	14=>$arTypes['ar']
);

$obQuery=\core\database\VDatabase::driver()->createQuery();
$obBuilder=$obQuery->builder();
$obJoin=$obBuilder
	->from('iblock_element','ielement')
	->field('ielement.ID','id')
	->field('ielement.IBLOCK_ID','iblock_id')
	->field('ielement.NAME','name')
	->field('ielement.PREVIEW_TEXT','preview_text')
	->field('ielement.DETAIL_TEXT','detail_text')
	->field('ielement.TIMESTAMP_X','date_edit')
	->field('ielement.TAGS','tags')
	->field('ielement.ACTIVE','active')
	->field('ielement.ACTIVE_FROM','active_from')
	->field('ielement.ACTIVE_TO','active_to')
	->field('isection.NAME','section_name')
	->field('isection.ACTIVE','section_active')
	->field('isection_top.NAME','section_top_name')
	->field('isection_top.ACTIVE','section_top_active')
	->join();
$obJoin->_left()
	->_from('ielement','IBLOCK_SECTION_ID')
	->_to('iblock_section','ID','isection');
$obJoin->_left()
	->_from('isection','IBLOCK_SECTION_ID')
	->_to('iblock_section','ID','isection_top');

$obBuilder->filter()
	->_in('ielement.IBLOCK_ID',array(
		3,36,35,14
	))
	->_gte('TIMESTAMP_X',date('Y-m-d H:i:s'));

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
	<sphinx:attr name="tags" type="string" default=:: />
	<sphinx:attr name="date_edit" type="timestamp" />
	<sphinx:attr name="id" type="int" bits="16" default="0" />
	<sphinx:attr name="type" type="int" bits="16" default="1" />
	<sphinx:attr name="city" type="int" bits="16" default="0" />
</sphinx:schema>
';

$nTime=time();

foreach($arResult as $arValue){
	if((isset($arValue['section_active']) && $arValue['section_active']=='N')  ||
		(isset($arValue['section__top_active']) && $arValue['section__top_active']=='N')){
		$arKillList[]=$arValue['id'];
		continue;
	}

	$arValue['active_from']=(!empty($arValue['active_from'])) ? strtotime($arValue['active_from']) : false;
	$arValue['active_to']=(!empty($arValue['active_to'])) ? strtotime($arValue['active_to']) : false;

	if(($arValue['active_from'] && $arValue['active_from']>$nTime) ||
		($arValue['active_to'] && $arValue['active_to']<$nTime) || $arValue['active']=='N'){
		$arKillList[]=$arValue['id'];
		continue;
	}

	$sResult.='
		<sphinx:document id="'.$arValue['id'].'">
			<search-name>'.trim($arValue['name']).'</search-name>
			<search-category>'.trim($arValue['section_name']).' - '.trim($arValue['section_top_name']).'</search-category>
			<search-preview><![CDATA[['.trim(strip_tags($arValue['preview_text'])).']]></search-preview>
			<search-detail><![CDATA[['.trim(strip_tags($arValue['detail_text'])).']]></search-detail>
			<search-tags>'.trim($arValue['tags']).'</search-tags>
			<name>'.$arValue['name'].'</name>
			<description>'.$arValue['preview_text'].'</description>
			<tags>'.$arValue['tags'].'</tags>
			<date_edit>'.strtotime($arValue['date_edit']).'</date_edit>
			<id>'.$arValue['id'].'</id>
			<type>'.$arTypes[$arValue['iblock_id']].'</type>
			<city>0</city>
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