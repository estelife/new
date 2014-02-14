<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

bitrix\ERESULT::$DATA[bitrix\ERESULT::$KEY]=array(
	'list'=>$arResult,
	'link'=>$arResult['link'],
	'city_name'=>$arResult['city']['NAME'],
	'city_r_name'=>$arResult['city']['R_NAME'],
	'city_t_name'=>$arResult['city']['T_NAME']
);