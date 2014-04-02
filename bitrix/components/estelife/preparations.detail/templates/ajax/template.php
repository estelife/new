<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

bitrix\ERESULT::$DATA[bitrix\ERESULT::$KEY]=$arResult['pill'];
bitrix\ERESULT::$DATA['class']='product';
bitrix\ERESULT::$DATA['crumb']=array(
	array(
		'name'=>'Главная',
		'link'=>'/'
	),
	array(
		'name'=>$arResult['type'],
		'link'=>$arResult['type_link']
	),
	array(
		'name'=>$arResult['pill']['name'],
		'link'=>'#'
	)
);