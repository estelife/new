<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

$sTitle='Производители';
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