<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

$sTitle='Акции '.(($_GET['city']==359) ? 'Москвы' : (($_GET['city']==358) ? 'Санкт-Петербурга' : ''));
echo json_encode(array(
	'list'=>array_values($arResult['akzii']),
	'title'=>array(
		'name'=>$sTitle
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