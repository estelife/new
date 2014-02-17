<?php
/**
 * @author Maxim Shlemarev <shlemarev@gmail.com>
 * @since 12.02.14
 */

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

global $APPLICATION;
$sTitle=($arResult['active']==0 ? 'Акции ' : 'Клиники ').(($arResult['city']['ID']==359) ? 'Москвы' : (($arResult['city']['ID']==358) ? 'Санкт-Петербурга' : ''));
echo json_encode(array(
	'list'=>$arResult,
	'title'=>array(
		'name'=>$sTitle
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