<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

bitrix\ERESULT::$DATA[bitrix\ERESULT::$KEY]=array(
	'TITLE'=>$arParams['TITLE'],
	'first_section'=>$arResult['first_section'],
	'MORE_TITLE'=>$arParams['MORE_TITLE'],
	'SECTIONS_NAME'=>array_values($arResult['SECTIONS_NAME']),
	'iblock'=>array_values($arResult['iblock']),
	'first'=>$arResult['first']
);