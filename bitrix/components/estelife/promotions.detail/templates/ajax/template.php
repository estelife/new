<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

if (!empty($arResult['action']['clinic']['main']['city_id']))
	$sGet = '?='.$arResult['action']['clinic']['main']['city_id'];
else
	$sGet='';

if (!empty($arResult['action']['city_name']))
	$sGetTitle = ' '.$arResult['action']['city_name'];
else
	$sGetTitle='';


echo json_encode(array(
	'detail'=>$arResult['action'],
	'crumb'=>array(
		array(
			'name'=>'Главная',
			'link'=>'/'
		),
		array(
			'name'=>'Акции '.$sGetTitle,
			'link'=>'/promotions/'.$sGet
		),
		array(
			'name'=>$arResult['action']['preview_text'],
			'link'=>'#'
		)
	),
	'seo'=>array(
		'title'=>$APPLICATION->GetPageProperty('title'),
		'description'=>$APPLICATION->GetPageProperty('description'),
		'keywords'=>$APPLICATION->GetPageProperty('keywords')
	),
	'class'=>'promotion'
));