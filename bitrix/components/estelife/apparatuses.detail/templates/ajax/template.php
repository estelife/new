<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

bitrix\ERESULT::$DATA[bitrix\ERESULT::$KEY]=$arResult['app'];
bitrix\ERESULT::$DATA['class']='product';
bitrix\ERESULT::$DATA['crumb']=array(
	array(
		'name'=>'Главная',
		'link'=>'/'
	),
	array(
		'name'=>'Аппараты',
		'link'=>'/apparatuses/'
	),
	array(
		'name'=>$arResult['app']['name'],
		'link'=>'#'
	)
);