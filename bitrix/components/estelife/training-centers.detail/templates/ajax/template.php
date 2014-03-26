<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

$arResult['company']['events']=array_values($arResult['company']['events']);
bitrix\ERESULT::$DATA[bitrix\ERESULT::$KEY]=$arResult['company'];
bitrix\ERESULT::$DATA['class']='company';
bitrix\ERESULT::$DATA['crumb']=array(
	array(
		'name'=>'Главная',
		'link'=>'/'
	),
	array(
		'name'=>'Учебные центры',
		'link'=>'/training-centers/'
	),
	array(
		'name'=>$arResult['company']['name'],
		'link'=>'#'
	)
);