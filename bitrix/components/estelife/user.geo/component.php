<?php
use core\database\mysql\VFilter;
use core\database\VDatabase;
use geo\Geo;

CModule::IncludeModule("estelife");
CModule::IncludeModule("iblock");
$arOption = array();
$arOption['charset'] = 'utf-8';
$geo = new Geo($arOption);
$data = $geo->get_value();
if (empty($data['city'])){
	$data['city'] = 'Москва';
}

//Получение ID города
$obCity = VDatabase::driver();
$obQuery = $obCity->createQuery();
$obQuery->builder()->from('iblock_element');
$obQuery->builder()->filter()
	->_eq('IBLOCK_ID', 16)
	->_like('NAME',$data['city'],VFilter::LIKE_BEFORE|VFilter::LIKE_AFTER);
$arCities = $obQuery->select()->all();

if (!empty($arCities)){
	$arCity = reset($arCities);
	setcookie('estelife_city', $arCity['ID'], time() + 12*60*60*24*30, '/');
}

$this->IncludeComponentTemplate();