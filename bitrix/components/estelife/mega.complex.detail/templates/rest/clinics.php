<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

$APPLICATION->IncludeComponent(
	"estelife:clinics.detail",
	"ajax",
	array(
		"PREFIX"=>$arResult['PREFIX'],
		'ID'=>$arResult['ID'],
		'CURRENT_TAB' => isset($arResult['CURRENT_TAB']) ? $arResult['CURRENT_TAB'] : 'base'
	),
	false
);