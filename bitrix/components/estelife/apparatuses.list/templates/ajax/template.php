<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

$sTitle='Аппараты';
echo json_encode(array(
	'list'=>array_values($arResult['apps']),
	'title'=>array(
		'name'=>$sTitle,
		'menu'=>array(
			array(
				'name'=>'Препараты',
				'link'=>'/preparations/',
				'class'=>''
			),
			array(
				'name'=>'Аппараты',
				'link'=>'/apparatuses/',
				'class'=>'active'
			)
		)
	),
	'crumb'=>array(
		array(
			'name'=>'Главная',
			'link'=>'/'
		),
		array(
			'name'=>$sTitle,
			'link'=>'#'
		)
	)
));