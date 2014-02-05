<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

$arResult['company']['events']=array_values($arResult['company']['events']);
echo json_encode(array(
	'detail'=>$arResult['company'],
	'crumb'=>array(
		array(
			'name'=>'Главная',
			'link'=>'/'
		),
		array(
			'name'=>'Учебные центры',
			'link'=>'/training-centers/'
		),
		array(
			'name'=>$arResult['company']['name'],
			'link'=>'#'
		)
	),
	'class'=>'company'
));