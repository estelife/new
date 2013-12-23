<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

$sTitle='Расписание семинаров';
echo json_encode(array(
	'list'=>array_values($arResult['training']),
	'title'=>array(
		'name'=>$sTitle,
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