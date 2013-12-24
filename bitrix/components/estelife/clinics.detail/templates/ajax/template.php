<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

if ($arResult['clinic']['main_contact']['city_id']==359){
	$arGet = '?=359';
	$arGetTitle = ' Москвы';
}elseif($arResult['clinic']['main_contact']['city_id']==358){
	$arGet = '?=358';
	$arGetTitle = ' Санкт-Петербурга';
}else{
	$arGet='';
	$arGetTitle = '';
}

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