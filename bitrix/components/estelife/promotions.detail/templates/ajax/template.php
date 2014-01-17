<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

echo json_encode(array(
	'detail'=>$arResult['action'],
	'crumb'=>array(
		array(
			'name'=>'Главная',
			'link'=>'/'
		),
		array(
			'name'=>'Акции'.($arResult['action']['clinics']['city_id']==359 ? ' Москвы' : ($arResult['action']['clinics']['city_id']==358 ? ' Санкт-Петербурга' : '')),
			'link'=>'/promotions/'.($arResult['action']['clinics']['city_id']==359 ? '?city=359' : ($arResult['action']['clinics']['city_id']==358 ? '?city=358' : ''))
		),
		array(
			'name'=>$arResult['action']['preview_text'],
			'link'=>'#'
		)
	),
	'class'=>'promotion'
));