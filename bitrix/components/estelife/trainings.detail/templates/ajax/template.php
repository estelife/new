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
			'name'=>'Расписание семинаров',
			'link'=>'/trainings/'
		),
		array(
			'name'=>$arResult['event']['full_name'],
			'link'=>'#'
		)
	),
	'class'=>'training'
));