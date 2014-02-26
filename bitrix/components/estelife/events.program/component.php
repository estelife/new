<?php
use core\exceptions\VException;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

global $APPLICATION;

try {
	$nEventId = isset($arParams['EVENT_ID']) ? abs(intval($arParams['EVENT_ID'])) : 0;

	if($nEventId==0)
		throw new VException('event id not found', 404);

	$obQuery = \core\database\VDatabase::driver()
		->createQuery();

	$obBuilder = $obQuery->builder();
	$obJoin = $obBuilder->from('estelife_event_halls','hall')
		->field('hall.id','id')
		->field('hall.name','name')
		->field('hall.translit','translit')
		->field('event.name', 'event_name')
		->field('event.id', 'event_id')
		->join();

	$obJoin->_left()
		->_from('hall','event_id')
		->_to('estelife_events','id','event');

	$obBuilder->filter()
		->_eq('event.id', $nEventId);

	$arTemp = $obQuery
		->select()
		->all();

	if(empty($arTemp))
		throw new VException('not found halls');

	$arHalls = array();

	foreach($arTemp as $arHall)
		$arHalls[$arHall['id']] = $arHall;

	$arHallIds = array_keys($arHalls);
	$obBuilder = $obQuery->builder();
	$obBuilder->from('estelife_event_sections')
		->field('name')
		->field('number')
		->filter()
		->_in('hall_id',$arHallIds);

	$arTemp = $obQuery
		->select()
		->all();

	if(empty($arTemp))
		throw new VException('not found sections');

	foreach($arTemp as $arSection){

	}
} catch(VException $e){
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