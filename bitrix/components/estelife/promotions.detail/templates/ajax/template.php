<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

echo json_encode(array(
	'detail'=>$arResult['action'],
	'crumb'=>array(
		array(
			'name'=>'Главная',
			'link'=>'/'
		),
		array(
			'name'=>'Акции',
			'link'=>'/promotions/'
		),
		array(
			'name'=>$arResult['action']['name'],
			'link'=>'#'
		)
	),
	'class'=>'promotion'
));