<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

$APPLICATION->IncludeComponent(
	"bitrix:advertising.banner",
	"inner_ajax",
	Array(
		"TYPE" => "main_right_1",
		"CACHE_TYPE" => "A",
		"NOINDEX" => "N",
		"CACHE_TIME" => "3600"
	)
);

$APPLICATION->IncludeComponent(
	"bitrix:advertising.banner",
	"inner_ajax",
	Array(
		"TYPE" => "main_right_2",
		"CACHE_TYPE" => "A",
		"NOINDEX" => "N",
		"CACHE_TIME" => "3600"
	)
);

echo json_encode(array(
	'BANNER' => implode(' ', bitrix\ERESULT::$DATA['BANNER'])
));