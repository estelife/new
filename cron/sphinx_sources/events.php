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
$nTime=time();

$obDriver=\core\database\VDatabase::driver();
$obQuery=$obDriver->createQuery();
$obBuilder=$obQuery->builder();
$obJoin=$obBuilder
	->from('estelife_events','activity')
	->field('activity.id','id')
	->field('activity.full_name','full_name')
	->field('activity.preview_text','preview_text')
	->field('activity.detail_text','detail_text')
	->field('activity.date_edit','date_edit')
	->field('training_type.id','is_training')
	->field('city.NAME','city')
	->field('city.ID','city_id')
	->field('company.name','company')
	->field('calendar.date','date_from')
	->join();

$obJoin->_left()
	->_from('activity', 'id')
	->_to('estelife_calendar','event_id','calendar')
	->_cond()
	->_gte('calendar.date',$nTime);

// Выясняем, является ли оно тренингом или событием
$obJoin->_left()
	->_from('activity','id')
	->_to('estelife_event_types','event_id','training_type')
	->_cond()
	->_eq('training_type.type',3);

// Город
$obJoin->_left()
	->_from('activity','city_id')
	->_to('iblock_element','ID','city')
	->_cond()->_eq('city.IBLOCK_ID',16);

// Фигачим компанию
$obJoin->_left()
	->_from('activity','id')
	->_to('estelife_company_events','event_id','company_link')
	->_cond()
	->_eq('company_link.is_owner',1);
$obJoin->_left()
	->_from('company_link','company_id')
	->_to('estelife_companies','id','company');

$obBuilder->filter()
	->_gte('activity.date_edit',$nTime);

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

foreach($arResult as $arValue){
	if(empty($arValue['date_from'])){
		$arKillList[]=$arValue['date_from'];
		continue;
	}

	$sCategory=(!empty($arValue['is_training'])) ? 'Семинары' : 'События';
	$nType=(!empty($arValue['is_training'])) ? $arTypes['tr'] : $arTypes['ev'];

	$arValue['tags']=array(
		$sCategory,
		$arValue['company'],
		$arValue['city']
	);

	$sResult.='
		<sphinx:document id="'.$arValue['id'].'">
			<search-name>'.trim($arValue['name']).'</search-name>
			<search-category>'.$sCategory.' '.trim($arValue['city']).'</search-category>
			<search-preview><![CDATA[['.trim(strip_tags($arValue['preview_text'])).']]></search-preview>
			<search-detail><![CDATA[['.trim(strip_tags($arValue['detail_text'])).']]></search-detail>
			<search-tags>'.trim($arValue['tags']).'</search-tags>
			<name>'.$arValue['name'].'</name>
			<description>'.htmlspecialchars($arValue['preview_text'],ENT_QUOTES,'utf-8').'</description>
			<tags>'.$arValue['tags'].'</tags>
			<date_edit>'.strtotime($arValue['date_edit']).'</date_edit>
			<id>'.$arValue['id'].'</id>
			<type>'.$nType.'</type>
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