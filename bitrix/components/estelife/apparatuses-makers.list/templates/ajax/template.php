<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

global $APPLICATION;
$sTitle='Производители аппаратов';
echo json_encode(array(
	'list'=>array_values($arResult['apparatus']),
	'title'=>array(
		'name'=>$sTitle,
		'menu'=>array(
			array(
				'name'=>'Препараты',
				'link'=>'/preparations-makers/',
				'class'=>''
			),
			array(
				'name'=>'Аппараты',
				'link'=>'/apparatuses-makers/',
				'class'=>'active'
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