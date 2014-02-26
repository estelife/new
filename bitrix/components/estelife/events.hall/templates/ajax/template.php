<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

echo json_encode(array(
	'detail'=>$arResult,
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
			'name'=>$arResult['event'],
			'link'=>'/events/ev'.$arResult['event_id'].'/',
		),
		array(
			'name'=>'Программа',
			'link'=>'/events/ev'.$arResult['event_id'].'/program/'
		),
		array(
			'name'=>$arResult['hall'],
			'link'=>'#'
		)
	),
));