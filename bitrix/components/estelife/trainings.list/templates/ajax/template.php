<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

global $APPLICATION;
if ($arParams['COMPONENT'] == 'list'){
	$sTitle='Расписание семинаров';
	echo json_encode(array(
		'list'=>array_values($arResult['training']),
		'title'=>array(
			'name'=>$sTitle,
		),
		'seo'=>array(
			'title'=>$APPLICATION->GetPageProperty('title'),
			'description'=>$APPLICATION->GetPageProperty('description'),
			'keywords'=>$APPLICATION->GetPageProperty('keywords')
		),
		'count'=>$arResult['count'],
		'crumb'=>array(
			array(
				'name'=>'Главная',
				'link'=>'/'
			),
			array(
				'name'=>$sTitle,
				'link'=>'#'
			)
		),
		'nav'=>$arResult['nav']
	));
}elseif ($arParams['COMPONENT'] == 'centers_list'){
	bitrix\ERESULT::$DATA['detail']['events']=array_values($arResult['training']);
}