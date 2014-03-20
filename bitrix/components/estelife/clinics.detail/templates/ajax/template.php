<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

if (!empty($arResult['clinic']['city_id']))
	$arGet = '?city='.$arResult['clinic']['city_id'];
else
	$arGet='';

if (!empty($arResult['clinic']['city_name']))
	$arGetTitle = ' '.$arResult['clinic']['city_name'];
else
	$arGetTitle='';

echo json_encode(array(
	'detail'=>$arResult['clinic'],
	'crumb'=>array(
		array(
			'name'=>'Главная',
			'link'=>'/'
		),
		array(
			'name'=>'Клиники'.$arGetTitle,
			'link'=>'/clinics/'.$arGet,
		),
		array(
			'name'=>$arResult['clinic']['name'],
			'link'=>'#'
		)
	),
	'class'=>'company'
));