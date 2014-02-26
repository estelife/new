<?php
use core\exceptions\VException;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

global $APPLICATION;

try {
	$nEventId = isset($arParams['EVENT_ID']) ? abs(intval($arParams['EVENT_ID'])) : 0;
	$sDate = isset($_GET['date']) ? trim(htmlspecialchars($_GET['date'])) : '';
	$obQuery = \core\database\VDatabase::driver()
		->createQuery();

	if($nEventId==0)
		throw new VException('event id not found', 404);

	if($sDate !='' && !preg_match('#^([0-9]{2})\.([0-9]{2})$#', $sDate))
		throw new VException('invalid date', 404);

	$obQuery->builder()
		->from('estelife_events')
		->field('id')
		->field('short_name')
		->filter()
		->_eq('id', $nEventId);

	$arEvent = $obQuery
		->select()
		->all();

	if(empty($arEvent))
		throw new VException('event not found', 404);

	$obQuery->builder()
		->from('estelife_event_sections_dates', 'section')
		->field('date')
		->sort('date','asc')
		->group('date')
		->union();
	$obQuery->builder()
		->from('estelife_event_activities', 'activity')
		->field('date')
		->sort('date','asc')
		->group('date');

	$arTemp = $obQuery
		->select()
		->all();

	$arDates = array();

	foreach($arTemp as $arDate){
		if(isset($arDates[$arDate['date']]))
			continue;

		$nDate = strtotime($arDate['date'].' 00:00');
		$arValue = explode(' ', \core\types\VDate::date($nDate, 'j F'));
		$arDate['day'] = $arValue[0];
		$arDate['month'] = mb_substr($arValue[1],0,3,'utf-8');
		$arDate['date'] = date('d.m', $nDate);

		$arDates[$nDate] = $arDate;
	}

	ksort($arDate,SORT_NUMERIC);

	if($sDate == ''){
		$arDate = reset($arDates);
		$sDate = $arDate['date'];
	}

	$sDate = preg_replace('#^([0-9]{2})\.([0-9]{2})$#', date('Y').'-$2-$1', $sDate);

	$obBuilder = $obQuery
		->builder()
		->from('estelife_event_halls');

	$arTemp = $obQuery
		->select()
		->all();

	if(empty($arTemp))
		throw new VException('halls not found');

	$arHalls = array();

	foreach($arTemp as $arHall)
		$arHalls[$arHall['id']] = $arHall;

	$obBuilder = $obQuery->builder();
	$obBuilder->from('estelife_event_sections', 'section')
		->field('section.id', 'id')
		->field('section.name', 'name')
		->field('section.number', 'number')
		->field('section.time_from', 'time_from')
		->field('section.time_to', 'time_to')
		->filter()
		->_eq('section.event_id', $nEventId)
		->_eq('section.date', $sDate);

	$obBuilder->join()
		->_left()
		->_from('section', 'id')
		->_to('estelife_event_sections_dates', 'section_id');

	$arSections = $obQuery
		->select()
		->all();

	if (!empty($arSections)) {
		$arSectionIds = array();

		foreach ($arSections as &$arSection) {
			$arSection['group'] = 1;
			$arSection['with_video'] = 0;
			$arSectionIds[] = $arSection['id'];
		}

		$obQuery->builder()
			->from('estelife_event_section_halls')
			->field('hall_id')
			->field('section_id')
			->filter()
			->_in('section_id', $arSectionIds);

		$arSectionHalls = $obQuery
			->select()
			->all();

		if (!empty($arSectionHalls)) {
			foreach($arSectionHalls as $arHall){
				if(!isset($arHalls[$arHall['id']]))
					continue;

				$nKey = array_search($arHall['section_id'], $arSectionIds);
				$arHalls[$arHall['hall_id']]['activities'][$arSections[$nKey]['time_from']] = $arSections[$nKey];
			}
		}
	}

	$obQuery->builder()
		->from('estelife_event_activities', 'activity')
		->field('activity.name', 'name')
		->field('activity.with_video', 'with_video')
		->field('activity.time_from', 'time_from')
		->field('activity.time_to', 'time_to')
		->field('activity.hall_id', 'hall_id')
		->field('type.name', 'type')
		->filter()
		->_eq('activity.event_id',$nEventId)
		->_eq('activity.date',$sDate)
		->_isNull('activity.section_id');

	$obQuery->builder()
		->join()
		->_left()
		->_from('activity','type_id')
		->_to('estelife_activity_types','id','type');

	$arActivities = $obQuery
		->select()
		->all();

	foreach($arActivities as &$arActivity){
		$arActivity['group'] = 0;
		$arHalls[$arActivity['hall_id']]['activities'][$arActivity['time_from']] = $arActivity;
	}

	$arResult['event'] = $arEvent;
	$arResult['dates'] = $arDates;
	$arResult['halls'] = $arHalls;
} catch(VException $e){
	var_dump($e->getMessage());

	if($e->getCode()==404){
		$APPLICATION->SetTitle("404 Not Found");
		CHTTP::SetStatus("404 Not Found");
	}

	$arResult['ERROR'] = array(
		'MESSAGE' => $e->getMessage(),
		'CODE' => $e->getCode()
	);
}

$this->IncludeComponentTemplate();