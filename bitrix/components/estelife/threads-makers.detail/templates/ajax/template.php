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
		'name'=>'Производители нитей',
		'link'=>'/threads-makers/'
	),
	array(
		'name'=>$arResult['company']['name'],
		'link'=>'#'
	)
);
bitrix\ERESULT::$DATA['seo'] = array(
	'title'=>$APPLICATION->GetPageProperty('title'),
	'description'=>$APPLICATION->GetPageProperty('description'),
	'keywords'=>$APPLICATION->GetPageProperty('keywords')
);
