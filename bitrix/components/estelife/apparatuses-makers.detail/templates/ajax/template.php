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
			'name'=>'Производители аппаратов',
			'link'=>'/apparatuses-makers/'
		),
		array(
			'name'=>$arResult['company']['name'],
			'link'=>'#'
		)
	),
	'class'=>'producer'
));