<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

echo json_encode(array(
	'detail'=>$arResult['event'],
	'crumb'=>array(
		array(
			'name'=>'Главная',
			'link'=>'/'
		),
		array(
			'name'=>'Календарь событий',
			'link'=>'/events/',
		),
		array(
			'name'=>$arResult['event']['short_name'],
			'link'=>'#'
		)
	),
	'class'=>'training'
));