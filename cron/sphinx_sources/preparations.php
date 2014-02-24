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
$arResult=array();

$obDriver=\core\database\VDatabase::driver();
$obQuery=$obDriver->createQuery();
$obBuilder=$obQuery->builder();
$obJoin=$obBuilder
	->from('estelife_pills','ep')
	->field('ep.id','id')
	->field('ep.name','name')
	->field('ep.preview_text', 'preview_text')
	->field('ep.detail_text', 'detail_text')
	->field('ep.date_edit', 'date_edit')
	->field('ec.name', 'company_name')
	->field('ep.type_id', 'type_id')
	->join();
$obJoin->_left()
	->_from('ep', 'company_id')
	->_to('estelife_companies', 'id', 'ec');

//TODO: Раскомментировать после первой индексации
//$obBuilder->filter()
//	->_gte('ep.date_edit',time())

$arPreparations=$obQuery
	->select()
	->all();

if (!empty($arPreparations)){
	foreach ($arPreparations as $key=>$val){
		$val['type'][]=$val['company_name'];
		$arResult[$val['id']]=$val;
		$arIds[]=$val['id'];
	}
}

if (!empty($arIds)){
	$obQuery=$obDriver->createQuery();
	$obQuery->builder()
		->from('estelife_pills_type')
		->field('type_id')
		->field('pill_id')
		->filter()
			->_in('pill_id', $arIds);
	$arPreparationTypes=$obQuery
		->select()
		->all();
}

$arTypesString=array(
	'1'=>'Мезотерапия',
	'2' =>'Ботулинотерапия',
	'3' =>'Биоревитализация',
	'4' =>'Контурная пластика'
);

if (!empty($arPreparationTypes)){
	foreach ($arPreparationTypes as $val)
		$arResult[$val['pill_id']]['tags'][]=trim($arTypesString[$val['type_id']]);
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
	<sphinx:attr name="type" type="int" bits="16" default="1" />
	<sphinx:attr name="city" type="int" bits="16" default="0" />
</sphinx:schema>
';

$nTime=time();

foreach($arResult as $arValue){
	$sName=trim(htmlspecialchars(strip_tags($arValue['name']),ENT_QUOTES,'utf-8'));
	$sPreviewText=trim(htmlspecialchars(strip_tags(html_entity_decode($arValue['preview_text'],ENT_QUOTES,'utf-8')),ENT_QUOTES,'utf-8'));
	$sDetailText=trim(htmlspecialchars(strip_tags(html_entity_decode($arValue['detail_text'],ENT_QUOTES,'utf-8')),ENT_QUOTES,'utf-8'));
	$sDescription=!empty($sDetailText) ? VString::truncate($sDetailText,300) : $sPreviewText;
	$sTags=htmlspecialchars(implode(', ',$arValue['tags']),ENT_QUOTES,'utf-8');

	if($arValue['type_id']==2){
		$nType=$arTypes['th'];
		$sCategory='Нити';
	}else if($arValue['type_id']==3){
		$nType=$arTypes['im'];
		$sCategory='Имплантаты';
	}else{
		$nType=$arTypes['ps'];
		$sCategory='Препараты';
	}

	$sResult.='
		<sphinx:document id="'.$nType.$arValue['id'].'">
			<search-name>'.$sName.'</search-name>
			<search-category>'.$sCategory.'</search-category>
			<search-preview><![CDATA[['.$sPreviewText.']]></search-preview>
			<search-detail><![CDATA[['.$sDetailText.']]></search-detail>
			<search-tags>'.$sTags.'</search-tags>
			<name>'.$sName.'</name>
			<description>'.$sDescription.'</description>
			<tags>'.$sTags.'</tags>
			<date_edit>'.$arValue['date_edit'].'</date_edit>
			<id>'.$arValue['id'].'</id>
			<type>'.$nType.'</type>
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