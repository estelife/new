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
$obQuery=$obDriver
	->createQuery();
$obBuilder=$obQuery
	->builder();

$obJoin=$obBuilder
	->from('estelife_companies','company')
	->field('company.id','id')
	->field('company.name','name')
	->field('company.preview_text','preview_text')
	->field('company.detail_text','detail_text')
	->field('company.date_edit','date_edit')
	->field('company.active','active')
	->field('company_geo.city_id','city_id')
	->field('company_geo.address','address')
	->field('company_city.NAME','city_name')

	->field('sponsor.name','sponsor_name')
	->field('sponsor.preview_text','sponsor_preview_text')
	->field('sponsor.detail_text','sponsor_detail_text')
	->field('sponsor_geo.city_id','sponsor_city_id')
	->field('sponsor_geo.address','sponsor_address')
	->field('sponsor_city.NAME','sponsor_city_name')

	->field('producer.name','producer_name')
	->field('producer.preview_text','producer_preview_text')
	->field('producer.detail_text','producer_detail_text')
	->field('producer_geo.city_id','producer_city_id')
	->field('producer_geo.address','producer_address')
	->field('producer_city.NAME','producer_city_name')

	->field('school.name','school_name')
	->field('school.preview_text','school_preview_text')
	->field('school.detail_text','school_detail_text')
	->field('school_geo.city_id','school_city_id')
	->field('school_geo.address','school_address')
	->field('school_city.NAME','school_city_name')

	->field('qqevent.id','has_event')
	->field('training.id','has_training')
	->field('pill.id','has_pill')
	->field('apparatus.id','has_apparatus')

	->join();

// Собираем в кучу типы компаний
$obJoin->_left()
	->_from('company','id')
	->_to('estelife_company_types','company_id','sponsor')
	->_cond()->_eq('sponsor.type',2);
$obJoin->_left()
	->_from('company','id')
	->_to('estelife_company_types','company_id','producer')
	->_cond()->_eq('producer.type',3);
$obJoin->_left()
	->_from('company','id')
	->_to('estelife_company_types','company_id','school')
	->_cond()->_eq('school.type',4);

// География
$obJoin->_left()
	->_from('company','id')
	->_to('estelife_company_geo','company_id','company_geo');
$obJoin->_left()
	->_from('sponsor','id')
	->_to('estelife_company_type_geo','company_id','sponsor_geo');
$obJoin->_left()
	->_from('producer','id')
	->_to('estelife_company_type_geo','company_id','producer_geo');
$obJoin->_left()
	->_from('school','id')
	->_to('estelife_company_type_geo','company_id','school_geo');

$obJoin->_left()
	->_from('company_geo','city_id')
	->_to('iblock_element','ID','company_city')
	->_cond()->_eq('company_city.IBLOCK_ID',16);
$obJoin->_left()
	->_from('producer_geo','city_id')
	->_to('iblock_element','ID','producer_city')
	->_cond()->_eq('producer_city.IBLOCK_ID',16);
$obJoin->_left()
	->_from('sponsor_geo','city_id')
	->_to('iblock_element','ID','sponsor_city')
	->_cond()->_eq('sponsor_city.IBLOCK_ID',16);
$obJoin->_left()
	->_from('school_geo','city_id')
	->_to('iblock_element','ID','school_city')
	->_cond()->_eq('school_city.IBLOCK_ID',16);

// Объединяем с возможнными событиями
$obJoin->_left()
	->_from('company','id')
	->_to('estelife_company_events','company_id','company_event_link');
$obJoin->_left()
	->_from('company_event_link','event_id')
	->_to('estelife_event_types','event_id','qqevent')
	->_cond()
	->_in('qqevent.type',array(1,4));
$obJoin->_left()
	->_from('company_event_link','event_id')
	->_to('estelife_event_types','event_id','training')
	->_cond()
	->_eq('training.type',3);

// Объединяем с возможными преппаратами и аппаратами
$obJoin->_left()
	->_from('company','id')
	->_to('estelife_pills','company_id','pill');
$obJoin->_left()
	->_from('company','id')
	->_to('estelife_apparatus','company_id','apparatus');

// TODO: открыть фильтр
//$obBuilder->filter()
//	->_gte('company.date_edit',time());

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
	<sphinx:attr name="type" type="int" bits="16" default="1" />
	<sphinx:attr name="city" type="int" bits="16" default="1" />
</sphinx:schema>
';

foreach($arResult as $arValue){
	$bFound=false;

	if(!empty($arValue['has_events']) || !empty($arValue['sponsor_name'])){
		appendToResult($sResult,array(
			'id'=>$arValue['id'],
			'name'=>(!empty($arValue['sponsor_name'])) ? $arValue['sponsor_name'] : $arValue['name'],
			'city'=>(!empty($arValue['sponsor_city_name'])) ? $arValue['sponsor_city_name'] : $arValue['city_name'],
			'preview_text'=>(!empty($arValue['sponsor_preview_text'])) ? $arValue['sponsor_preview_text'] : $arValue['preview_text'],
			'detail_text'=>(!empty($arValue['sponsor_detail_text'])) ? $arValue['sponsor_detail_text'] : $arValue['detail_text'],
			'city_id'=>(!empty($arValue['sponsor_city_id'])) ? $arValue['sponsor_city_id'] : $arValue['city_id'],
			'address'=>(!empty($arValue['sponsor_address'])) ? $arValue['sponsor_address'] : $arValue['address'],
			'date_edit'=>$arValue['date_edit'],
			'type'=>$arTypes['sp'],
			'category'=>'Организаторы'
		));
		$bFound=true;
	}

	if(!empty($arValue['has_pills']) || !empty($arValue['has_apparatus']) || !empty($arValue['producer_name'])) {
		appendToResult($sResult,array(
			'id'=>$arValue['id'],
			'name'=>(!empty($arValue['producer_name'])) ? $arValue['producer_name'] : $arValue['name'],
			'city'=>(!empty($arValue['producer_city_name'])) ? $arValue['producer_city_name'] : $arValue['city_name'],
			'preview_text'=>(!empty($arValue['producer_preview_text'])) ? $arValue['producer_preview_text'] : $arValue['preview_text'],
			'detail_text'=>(!empty($arValue['producer_detail_text'])) ? $arValue['producer_detail_text'] : $arValue['detail_text'],
			'city_id'=>(!empty($arValue['producer_city_id'])) ? $arValue['producer_city_id'] : $arValue['city_id'],
			'address'=>(!empty($arValue['producer_address'])) ? $arValue['producer_address'] : $arValue['address'],
			'date_edit'=>$arValue['date_edit'],
			'type'=>$arTypes['pm'],
			'category'=>'Производители'
		));
		$bFound=true;
	}

	if(!empty($arValue['has_training']) || !empty($arValue['school_name'])) {
		appendToResult($sResult,array(
			'id'=>$arValue['id'],
			'name'=>(!empty($arValue['school_name'])) ? $arValue['school_name'] : $arValue['name'],
			'city'=>(!empty($arValue['school_city_name'])) ? $arValue['school_city_name'] : $arValue['city_name'],
			'preview_text'=>(!empty($arValue['school_preview_text'])) ? $arValue['school_preview_text'] : $arValue['preview_text'],
			'detail_text'=>(!empty($arValue['school_detail_text'])) ? $arValue['school_detail_text'] : $arValue['detail_text'],
			'city_id'=>(!empty($arValue['school_city_id'])) ? $arValue['school_city_id'] : $arValue['city_id'],
			'address'=>(!empty($arValue['school_address'])) ? $arValue['school_address'] : $arValue['address'],
			'date_edit'=>$arValue['date_edit'],
			'type'=>$arTypes['tc'],
			'category'=>'Учебные центры'
		));
		$bFound=true;
	}

	if(!$bFound)
		$arKillList[]=$arValue['id'];
}

if(!empty($arKillList)){
	$sResult.='<sphinx:killlist>';

	foreach($arKillList as $nId)
		$sResult.='<id>'.$nId.'</id>';

	$sResult.='</sphinx:killlist>';
}

$sResult.='</sphinx:docset>';
echo $sResult;

function appendToResult(&$sResult,array $arValue){
	$arValue['tags']=implode(', ',(!empty($arValue['tags']) ? $arValue['tags'] : array($arValue['city'],$arValue['category'])));
	$arValue['tags']=htmlspecialchars(trim(strip_tags($arValue['tags'])),ENT_QUOTES,'utf-8');
	$sSearchTags=htmlspecialchars($arValue['tags'].', '.$arValue['city'].', '.$arValue['address'],ENT_QUOTES,'utf-8');

	$sPreviewText=trim(htmlspecialchars(strip_tags($arValue['preview_text']),ENT_QUOTES,'utf-8'));
	$sDetailText=trim(htmlspecialchars(strip_tags($arValue['detail_text']),ENT_QUOTES,'utf-8'));
	$sDescription=!empty($sDetailText) ? VString::truncate($sDetailText,300) : $sPreviewText;

	$sResult.='
		<sphinx:document id="'.$arValue['id'].'">
			<search-name>'.trim(htmlspecialchars(strip_tags($arValue['name']),ENT_QUOTES,'utf-8')).'</search-name>
			<search-category>'.$arValue['category'].' '.trim($arValue['city']).'</search-category>
			<search-preview><![CDATA[['.$sPreviewText.']]></search-preview>
			<search-detail><![CDATA[['.$sDetailText.']]></search-detail>
			<search-tags>'.$sSearchTags.'</search-tags>
			<name>'.htmlspecialchars($arValue['name'],ENT_QUOTES,'utf-8').'</name>
			<description>'.$sDescription.'</description>
			<tags>'.$arValue['tags'].'</tags>
			<date_edit>'.$arValue['date_edit'].'</date_edit>
			<id>'.$arValue['id'].'</id>
			<type>'.$arValue['type'].'</type>
			<city>'.$arValue['city_id'].'</city>
		</sphinx:document>
	';
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");