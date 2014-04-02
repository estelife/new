<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

bitrix\ERESULT::$KEY='detail';
$APPLICATION->IncludeComponent(
	"estelife:training-centers.detail",
	"ajax",
	array(
		"PREFIX"=>$arResult['PREFIX'],
		'ID'=>$arResult['ID']
	),
	false
);

$APPLICATION->IncludeComponent(
	"estelife:trainings.list",
	"ajax",
	array(
		"MAKER"=>bitrix\ERESULT::$DATA['detail']['id'],
		"MAKER_NAME"=>bitrix\ERESULT::$DATA['detail']['name'],
		"COMPONENT"=> 'centers_list',
	)
);

echo json_encode(bitrix\ERESULT::$DATA);