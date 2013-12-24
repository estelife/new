<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

echo json_encode(array(
	'detail'=>$arResult['app'],
	'crumb'=>array(
		array(
			'name'=>'Главная',
			'link'=>'/'
		),
		array(
			'name'=>'Аппараты',
			'link'=>'/apparatuses/'
		),
		array(
			'name'=>$arResult['app']['name'],
			'link'=>'#'
		)
	),
	'class'=>'product'
));