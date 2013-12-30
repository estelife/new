<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

$sTitle='Клиники '.(($arResult['city']['ID']==359) ? 'Москвы' : (($arResult['city']['ID']==358) ? 'Санкт-Петербурга' : ''));
echo json_encode(array(
	'list'=>array_values($arResult['clinics']),
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
	),
	'nav'=>$arResult['nav']
));