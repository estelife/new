<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

bitrix\ERESULT::$KEY='comments';
$APPLICATION->IncludeComponent(
	"estelife:comments.list",
	"ajax",
	array(
		'count'=>$_REQUEST['count'],
		'element_id'=>$_REQUEST['id'],
		'type'=>$_REQUEST['type']
	),
	false
);

$APPLICATION->IncludeComponent(
	"estelife:forms",
	"ajax",
	array(
		'FORM'=>bitrix\ERESULT::$DATA['comments']['form'],
		'ERRORS'=>bitrix\ERESULT::$DATA['comments']['error'],
	)
);

echo json_encode(bitrix\ERESULT::$DATA);