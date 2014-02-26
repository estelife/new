<?php
$_SERVER["DOCUMENT_ROOT"] = realpath(__DIR__.'/../');
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
@set_time_limit(0);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('estelife');

$obQuery = \core\database\VDatabase::driver()
	->createQuery();
$obQuery->builder()
	->from('estelife_event_sections');
$arSections = $obQuery->select()->all();
$arSectionIds = array();

foreach($arSections as $arSection){
	$arName = explode('.',$arSection['theme']);

	$arData = array();
	$arData['theme'] = isset($arName[1]) ? $arName[1] : $arName[0];
	$arData['name'] = isset($arName[1]) ? $arName[0] : 'Секция';

	$obQuery->builder()
		->from('estelife_event_sections')
		->value('theme', $arData['theme'])
		->value('name', $arData['name'])
		->filter()
		->_eq('id', $arSection['id']);
	$obQuery->update();

	$arSectionIds[] = $arSection['id'];
}

$obQuery->builder()
	->from('estelife_event_section_halls')
	->filter()
	->_in('section_id', $arSectionIds);
$arHalls = $obQuery
	->select()
	->all();
$arTemp = array();

foreach($arHalls as $arHall)
	$arTemp[$arHall['section_id']][] = $arHall;

$arHalls = $arTemp;

$obQuery->builder()
	->from('estelife_event_sections_dates')
	->filter()
	->_in('section_id', $arSectionIds);
$arDates = $obQuery
	->select()
	->all();

foreach($arDates as $arDate){
	if(empty($arTemp[$arDate['section_id']]))
		continue;

	$arHall = array_shift($arTemp[$arDate['section_id']]);
	$obQuery->builder()
		->from('estelife_event_sections_dates')
		->value('hall_id',$arHall['hall_id'])
		->filter()
		->_eq('section_id',$arHall['section_id']);

	$obQuery->update();
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");