<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

GLOBAL $samefilter;
$samefilter=array("!=ID"=>$arResult['ID']);
$APPLICATION->IncludeComponent(
	"bitrix:news.list",
	"same_ajax",
	Array(
		"DISPLAY_DATE" => "Y",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"AJAX_MODE" => "N",
		"IBLOCK_TYPE" => "news",
		"IBLOCK_ID" => "14",
		"NEWS_COUNT" => "3",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "SORT",
		"SORT_ORDER2" => "ASC",
		"FILTER_NAME" => "samefilter",
		"FIELD_CODE" => array("ID", "CODE", "XML_ID", "NAME", "TAGS", "SORT", "PREVIEW_TEXT", "PREVIEW_PICTURE", "DETAIL_TEXT", "DETAIL_PICTURE", "DATE_ACTIVE_FROM", "ACTIVE_FROM", "DATE_ACTIVE_TO", "ACTIVE_TO", "SHOW_COUNTER", "SHOW_COUNTER_START", "IBLOCK_TYPE_ID", "IBLOCK_ID", "IBLOCK_CODE", "IBLOCK_NAME", "IBLOCK_EXTERNAL_ID", "DATE_CREATE", "CREATED_BY", "CREATED_USER_NAME", "TIMESTAMP_X", "MODIFIED_BY", "USER_NAME"),
		"PROPERTY_CODE" => array("FORUM_MESSAGE_CNT"),
		"CHECK_DATES" => "Y",
		"PREVIEW_TRUNCATE_LEN" => "0",
		"ACTIVE_DATE_FORMAT" => "j F Y",
		"SET_TITLE" => "N",
		"SET_STATUS_404" => "N",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
		"ADD_SECTIONS_CHAIN" => "Y",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"PARENT_SECTION" => $arResult['IBLOCK_SECTION_ID'],
		"PARENT_SECTION_CODE" => "",
		"INCLUDE_SUBSECTIONS" => "Y",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"PAGER_TITLE" => "Новости",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"DETAIL_URL"	=>	$arParams['DETAIL_URL'],
	)
);

$img = CFile::GetFileArray($arResult['PROPERTIES']['INSIDE']['VALUE']);
echo json_encode(array(
	'crumb'=>array(
		array(
			'name'=>'Главная',
			'link'=>'/'
		),
		array(
			'name'=>$arResult['LAST_SECTION']['NAME'],
			'link'=>$arResult['LAST_SECTION']['SECTION_PAGE_URL']
		),
		array(
			'name'=>$arResult["NAME"],
			'link'=>'#'
		)
	),
	'detail'=>array(
		'NAME'=>$arResult['NAME'],
		'ACTIVE_FROM'=>(!empty($arResult['ACTIVE_FROM'])) ? date('d.m.Y',strtotime($arResult['ACTIVE_FROM'])) : '',
		'IMG'=>array(
			'SRC'=>$img['SRC'],
			'DESCRIPTION'=>(!empty($img['DESCRIPTION'])) ? $img['DESCRIPTION'] : $arResult['NAME']
		),
		'PREVIEW_TEXT'=>$arResult['PREVIEW_TEXT'],
		'DETAIL_TEXT'=>$arResult['DETAIL_TEXT'],
		'AUTHOR'=>(!empty($arResult['PROPERTIES']['AUTHOR']['VALUE'])) ? $arResult['PROPERTIES']['AUTHOR']['VALUE'] : ''
	),
	'class'=>'article',
	'same_data'=>(isset(bitrix\ERESULT::$DATA['SAME_ARTICLES'])) ?
		bitrix\ERESULT::$DATA['SAME_ARTICLES'] : array()
));