<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

bitrix\ERESULT::$KEY='PODCASTS';
$APPLICATION->IncludeComponent(
	"estelife:podcast.list",
	"home_ajax",
	array(
		"IBLOCK_ID"=>14,
		"NEWS_COUNT" => 7,
		"MAIN_URL" => "podcast",
		"PREFIX" => "pt",
		"SECTION_ID"=>208
	)
);

bitrix\ERESULT::$KEY='BANNER_RIGHT';
$APPLICATION->IncludeComponent(
	"bitrix:advertising.banner",
	"ajax",
	array(
		"TYPE" => "main_right_1",
		"CACHE_TYPE" => "A",
		"NOINDEX" => "N",
		"CACHE_TIME" => "3600"
	)
);

bitrix\ERESULT::$KEY='BANNER_TOP';
$APPLICATION->IncludeComponent(
	"bitrix:advertising.banner",
	"ajax",
	array(
		"TYPE" => "main_center_1",
		"CACHE_TYPE" => "A",
		"NOINDEX" => "N",
		"CACHE_TIME" => "3600"
	)
);

bitrix\ERESULT::$KEY='BANNER_BOTTOM';
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

bitrix\ERESULT::$KEY='EXPERTS';
$APPLICATION->IncludeComponent(
	"estelife:expert.list",
	"home_ajax",
	array(
		"IBLOCK_ID"=>35,
		"NEWS_COUNT" => 2,
		"MAIN_URL" => "",
		"TITLE"=>"Экспертное мнение",
		"MORE_TITLE"=>"",
		"IMG" => 174,
		"AUTOR"=> 172,
		"PROFESSION" => 173,
		"PREFIX" => ""
	)
);

bitrix\ERESULT::$KEY='PROMOTIONS';
$APPLICATION->IncludeComponent(
	"estelife:promotions.list",
	"home_ajax",
	array(
		"COUNT" => 3
	),
	false
);

bitrix\ERESULT::$KEY='ARTICLES';
$APPLICATION->IncludeComponent(
	"estelife:articles.list",
	"home_ajax",
	array(
		"IBLOCK_ID"=>14,
		"SECTIONS_ID"=> array(194,195,196,197),
		"SECTIONS_NAME"=> array("Красивое лицо", "Идеальное тело", "Изящные ручки", "Прекрасные ножки"),
		"NEWS_COUNT" => 4,
		"NEED_SECTION" => "N",
		"MAIN_URL" => "articles",
		"TITLE"=>"Советы о красоте",
		"MORE_TITLE"=>"Больше советов о красоте",
		"IMG_FIELD" => 151,
		"PREFIX" => "ar"
	)
);

bitrix\ERESULT::$KEY='PHOTOGALLERY';
$APPLICATION->IncludeComponent(
	"estelife:photogallery",
	"home_ajax",
	array(
		"COUNT" => 18,
		"ONLY_VIDEO"=>"Y",
		"ONLY_PHOTO"=>"Y",
	),
	false
);

bitrix\ERESULT::$KEY='NEWS';
$APPLICATION->IncludeComponent(
	"estelife:articles.list",
	"home_ajax",
	array(
		"IBLOCK_ID"=>3,
		"SECTIONS_ID"=> array(172,173,176,177),
		"SECTIONS_NAME"=> array("Косметология", "Пластическая хирургия", "Косметика", "Обо всем"),
		"NEWS_COUNT" => 4,
		"NEED_SECTION" => "N",
		"MAIN_URL" => "novosti",
		"TITLE"=>"Новости сферы",
		"MORE_TITLE"=>"Архив новостей",
		"IMG_FIELD" =>145,
		"PREFIX" => "ns"
	)
);

echo json_encode(bitrix\ERESULT::$DATA);