<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

global $APPLICATION;
$sTitle='Производители препаратов';
echo json_encode(array(
	'list'=>array_values($arResult['pills']),
	'title'=>array(
		'name'=>$sTitle,
		'menu'=>array(
			array(
				'name'=>'Препараты',
				'link'=>'/preparations-makers/',
				'class'=>'active'
			),
			array(
				'name'=>'Аппараты',
				'link'=>'/apparatuses-makers/',
				'class'=>''
			),
			array(
				'name'=>'Нити',
				'link'=>'/threads-makers/',
				'class'=>''
			),
		)
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
	'seo'=>array(
		'title'=>$APPLICATION->GetPageProperty('title'),
		'description'=>$APPLICATION->GetPageProperty('description'),
		'keywords'=>$APPLICATION->GetPageProperty('keywords')
	)
));