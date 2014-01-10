<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

echo json_encode(array(
	'list'=>array_values($arResult["ITEMS"]),
	'title'=>array(
		'name'=>$arResult['LAST_SECTION']['NAME']
	),
	'crumb'=>array(
		array(
			'name'=>'Главная',
			'link'=>'/'
		),
		array(
			'name'=>$arResult['LAST_SECTION']['NAME'],
			'link'=>'#'
		)
	),
	'nav'=>$arResult['NAV']
));