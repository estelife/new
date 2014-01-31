<?php
$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__).'/../../');
$DOCUMENT_ROOT =$_SERVER["DOCUMENT_ROOT"];

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
@set_time_limit(0);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/estelife/prolog.php");

CModule::IncludeModule('estelife');

$obDriver=\core\database\VDatabase::driver();
$obQuery=$obDriver->createQuery();
$obBuilder=$obQuery->builder();
$obJoin=$obBuilder
	->from('estelife_apparatus','ap')
	->field('ap.id','id')
	->field('ap.name','name')
	->field('ap.preview_text', 'preview_text')
	->field('ap.detail_text', 'detail_text')
	->field('ap.date_edit', 'date_edit')
	->field('ec.name', 'company_name')
	->join();
$obJoin->_left()
	->_from('ap', 'company_id')
	->_to('estelife_companies', 'id', 'ec');
//TODO: Раскомментировать после первой индексации
//$obBuilder->filter()
//	->_gte('ap.date_edit',time())

$arApparatuses=$obQuery
	->select()
	->all();


if (!empty($arApparatuses)){
	foreach ($arApparatuses as $key=>$val){
		$val['type'][]=$val['company_name'];
		$arResult[$val['id']]=$val;
		$arIds[]=$val['id'];
	}
}

if (!empty($arIds)){
	$obQuery=$obDriver->createQuery();
	$obQuery->builder()
		->from('estelife_apparatus_type')
		->field('type_id')
		->field('apparatus_id')
		->filter()
			->_in('apparatus_id', $arIds);
	$arTypes=$obQuery
		->select()
		->all();
}

$arTypesString=array(
	'1'=>'Anti-Age терапия',
	'2'=>'Коррекция фигуры',
	'3'=>'Эпиляция',
	'4'=>'Миостимуляция',
	'5'=>'Микротоки',
	'6'=>'Лазеры',
	'7'=>'Диагностика',
	'8'=>'Реабилитация',
	'9'=>'Микропигментация',
);

if (!empty($arTypes)){
	foreach ($arTypes as $val){
		$arResult[$val['apparatus_id']]['type'][]=trim($arTypesString[$val['type_id']]);
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
	<sphinx:attr name="type" type="int" bits="16" default="1" />
	<sphinx:attr name="city" type="int" bits="16" default="0" />
</sphinx:schema>
';

$nTime=time();

foreach($arResult as $arValue){
	$sResult.='
		<sphinx:document id="'.$arValue['id'].'">
			<search-name>'.trim($arValue['name']).'</search-name>
			<search-category>Аппараты</search-category>
			<search-preview><![CDATA[['.trim(strip_tags($arValue['preview_text'])).']]></search-preview>
			<search-detail><![CDATA[['.trim(strip_tags($arValue['detail_text'])).']]></search-detail>
			<search-tags>'.implode(', ',$arValue['tags']).'</search-tags>
			<name>'.$arValue['name'].'</name>
			<description>'.htmlspecialchars($arValue['preview_text'],ENT_QUOTES,'utf-8').'</description>
			<tags>'.implode(', ',$arValue['tags']).'</tags>
			<date_edit>'.strtotime($arValue['date_edit']).'</date_edit>
			<id>'.$arValue['id'].'</id>
			<type>ap</type>
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