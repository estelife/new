<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

global $APPLICATION;
$sTitle='Клиники '.(!empty($arResult['city']['PROPERTY_CITY_VALUE']) ? $arResult['city']['PROPERTY_CITY_VALUE'] : '');
echo json_encode(array(
	'list'=>array_values($arResult['clinics']),
	'title'=>array(
		'name'=>$sTitle
	),
	'seo'=>array(
		'title'=>$APPLICATION->GetPageProperty('title'),
		'description'=>$APPLICATION->GetPageProperty('description'),
		'keywords'=>$APPLICATION->GetPageProperty('keywords')
	),
	'count'=>$arResult['count'],
	'crumb'=>array(
		array(
			'name'=>'Главная',
			'link'=>'/'
		),
		array(
			'name'=>$sTitle,
			'link'=>'#'
		)
	),
	'nav'=>$arResult['nav'],
));