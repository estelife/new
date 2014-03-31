<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

$arGet = !empty($arResult['clinic']['city_id']) ? '?city='.$arResult['clinic']['city_id'] : '';
$arGetTitle = !empty($arResult['clinic']['city_name']) ? ' '.$arResult['clinic']['city_name'] : '';
$arResult['clinic']['CURRENT_TAB'] = $arResult['CURRENT_TAB'];

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
	'seo'=>array(
		'title'=>$APPLICATION->GetPageProperty('title'),
		'description'=>$APPLICATION->GetPageProperty('description'),
		'keywords'=>$APPLICATION->GetPageProperty('keywords')
	),
	'class'=>'company'
));