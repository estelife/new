<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

bitrix\ERESULT::$DATA[bitrix\ERESULT::$KEY]=$arResult['company'];
bitrix\ERESULT::$DATA['class']='producer';
bitrix\ERESULT::$DATA['crumb']=array(
	array(
		'name'=>'Главная',
		'link'=>'/'
	),
	array(
		'name'=>'Производители аппаратов',
		'link'=>'/apparatuses-makers/'
	),
	array(
		'name'=>$arResult['company']['name'],
		'link'=>'#'
	)
);