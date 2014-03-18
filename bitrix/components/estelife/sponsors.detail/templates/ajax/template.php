<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

echo json_encode(array(
	'detail'=>$arResult['company'],
	'crumb'=>array(
		array(
			'name'=>'Главная',
			'link'=>'/'
		),
		array(
			'name'=>'Организаторы',
			'link'=>'/sponsors/'
		),
		array(
			'name'=>$arResult['company']['name'],
			'link'=>'#'
		)
	),
	'seo'=>array(
		'title'=>$APPLICATION->GetPageProperty('title'),
		'description'=>$APPLICATION->GetPageProperty('description'),
		'keywords'=>$APPLICATION->GetPageProperty('keywords')
	),
	'class'=>'sponsor'
));