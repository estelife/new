<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

bitrix\ERESULT::$KEY='detail';
$APPLICATION->IncludeComponent(
	"estelife:apparatuses-makers.detail",
	"ajax",
	array(
		"PREFIX"=>$arResult['PREFIX'],
		'ID'=>$arResult['ID']
	),
	false
);

$APPLICATION->IncludeComponent(
	"estelife:apparatuses.list",
	"ajax",
	array(
		"MAKER"=>bitrix\ERESULT::$DATA['detail']['id'],
		"COMPONENT"=> 'maker_list',
	)
);

echo json_encode(bitrix\ERESULT::$DATA);