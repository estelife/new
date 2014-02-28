<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

global $APPLICATION;
$sTitle='Акции '.(!empty($arResult['city']['R_NAME']) ? $arResult['city']['R_NAME'] : '');
echo json_encode(array(
	'list'=>array_values($arResult['akzii']),
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
	'nav'=>$arResult['nav']
));