<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

global $APPLICATION;

if ($arParams['COMPONENT']=='list'){
	$sTitle=$arResult['title'];
	echo json_encode(array(
		'list'=>array_values($arResult['pills']),
		'title'=>array(
			'name'=>$sTitle
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
		'nav'=>$arResult['nav'],
		'seo'=>array(
			'title'=>$APPLICATION->GetPageProperty('title'),
			'description'=>$APPLICATION->GetPageProperty('description'),
			'keywords'=>$APPLICATION->GetPageProperty('keywords')
		)
	));
}elseif ($arParams['COMPONENT']=='similar_list'){
	bitrix\ERESULT::$DATA['detail']['similar']=$arResult['similar_pills'];
}elseif ($arParams['COMPONENT']=='maker_list'){
	bitrix\ERESULT::$DATA['detail']['production']=array_values($arResult['company']['production']);
}