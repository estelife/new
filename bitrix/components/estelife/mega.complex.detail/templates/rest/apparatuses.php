<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

bitrix\ERESULT::$KEY='detail';
$APPLICATION->IncludeComponent(
	"estelife:apparatuses.detail",
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
		"MAKER"=>bitrix\ERESULT::$DATA['detail']['company_id'],
		"MAKER_LINK"=> bitrix\ERESULT::$DATA['detail']['company_link'],
		"COMPONENT"=> 'similar_list',
		"PREP_ID" => bitrix\ERESULT::$DATA['detail']['id'],
	)
);

echo json_encode(bitrix\ERESULT::$DATA);