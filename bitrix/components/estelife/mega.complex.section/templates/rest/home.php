<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

bitrix\ERESULT::$KEY='PROMOTIONS';
$APPLICATION->IncludeComponent(
	"estelife:promotions.clinics.main.list",
	"home_ajax",
	array(
		"COUNT" => 3
	),
	false
);

bitrix\ERESULT::$KEY='BANNER_BOTTOM_2';
$APPLICATION->IncludeComponent(
	"bitrix:advertising.banner",
	"ajax",
	array(
		"TYPE" => "main_center_2",
		"CACHE_TYPE" => "A",
		"NOINDEX" => "N",
		"CACHE_TIME" => "3600"
	)
);

bitrix\ERESULT::$KEY='BANNER_BOTTOM_3';
$APPLICATION->IncludeComponent(
	"bitrix:advertising.banner",
	"ajax",
	array(
		"TYPE" => "main_center_3",
		"CACHE_TYPE" => "A",
		"NOINDEX" => "N",
		"CACHE_TIME" => "3600"
	)
);

bitrix\ERESULT::$KEY='BANNER_BOTTOM_4';
$APPLICATION->IncludeComponent(
	"bitrix:advertising.banner",
	"ajax",
	array(
		"TYPE" => "main_center_4",
		"CACHE_TYPE" => "A",
		"NOINDEX" => "N",
		"CACHE_TIME" => "3600"
	)
);


bitrix\ERESULT::$DATA['seo']=array(
	'title'=>'EsteLife.RU - информационный портал о косметологии и пластической хирургии',
	'description'=>'EsteLife.RU - информационный портал о косметологии и пластической хирургии',
	'keywords'=>'косметология, пластическая хирургия'
);
echo json_encode(bitrix\ERESULT::$DATA);