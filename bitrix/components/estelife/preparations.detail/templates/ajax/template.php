<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

echo json_encode(array(
	'detail'=>$arResult['pill'],
	'crumb'=>array(
		array(
			'name'=>'Главная',
			'link'=>'/'
		),
		array(
			'name'=>'Препараты',
			'link'=>'/preparations/'
		),
		array(
			'name'=>$arResult['pill']['name'],
			'link'=>'#'
		)
	),
	'class'=>'product'
));