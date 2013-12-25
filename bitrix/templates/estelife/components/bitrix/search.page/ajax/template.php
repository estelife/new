<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

echo json_encode(array(
	'crumb'=>array(
		array(
			'name'=>'Главная',
			'link'=>'/'
		),
		array(
			'name'=>'Поиск',
			'link'=>'#'
		)
	),
	'title'=>array(
		'name'=>'Поиск по сайту'
	),
	'SEARCH_PAGE'=>array(
		'REQUEST'=>$arResult["REQUEST"],
		'SEARCH_COUNT'=>count($arResult["SEARCH"]),
		'SEARCH'=>$arResult["SEARCH"],
		'URL'=>$arResult['URL']
	),
	'nav'=>$arResult['NAV']
));