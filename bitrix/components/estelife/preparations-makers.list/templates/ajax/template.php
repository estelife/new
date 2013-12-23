<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

$sTitle='Производители';
echo json_encode(array(
	'list'=>array_values($arResult['pills']),
	'title'=>array(
		'name'=>$sTitle,
		'menu'=>array(
			array(
				'name'=>'Препараты',
				'link'=>'/preparations/'
			),
			array(
				'name'=>'Аппараты',
				'link'=>'/apparatuses/'
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