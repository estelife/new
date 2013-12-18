<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $ElementID = $APPLICATION->IncludeComponent(
	"bitrix:news.detail",
	"articles",
	Array(
		"DISPLAY_DATE" => 'Y',
		"DISPLAY_NAME" => 'Y',
		"DISPLAY_PICTURE" => 'Y',
		"DISPLAY_PREVIEW_TEXT" => 'Y',
		"IBLOCK_TYPE" => 'news',
		"IBLOCK_ID" => 14,
		"FIELD_CODE" => array("ID", "CODE", "XML_ID", "NAME", "TAGS", "SORT", "PREVIEW_TEXT", "PREVIEW_PICTURE", "DETAIL_TEXT", "DETAIL_PICTURE", "DATE_ACTIVE_FROM", "ACTIVE_FROM", "DATE_ACTIVE_TO", "ACTIVE_TO", "SHOW_COUNTER", "SHOW_COUNTER_START", "IBLOCK_TYPE_ID", "IBLOCK_ID", "IBLOCK_CODE", "IBLOCK_NAME", "IBLOCK_EXTERNAL_ID", "DATE_CREATE", "CREATED_BY", "CREATED_USER_NAME", "TIMESTAMP_X", "MODIFIED_BY", "USER_NAME"),
		"PROPERTY_CODE" => array("BROWSER_TITLE", "DESCRIPTION", "KEYWORDS", "SOURCE", "FORUM_MESSAGE_CNT", "FORUM_TOPIC_ID", "GALLERY", "VIDEO", "ISVIDEO"),
		"META_KEYWORDS" => '-',
		"META_DESCRIPTION" => '-',
		"BROWSER_TITLE" => '-',
		"SET_TITLE" => 'Y',
		"SET_STATUS_404" => 'Y',
		"INCLUDE_IBLOCK_INTO_CHAIN" => 'Y',
		"ADD_SECTIONS_CHAIN" => 'Y',
		"ACTIVE_DATE_FORMAT" => 'j F Y',
		"CACHE_TYPE" => 'A',
		"CACHE_TIME" => '3600',
		"CACHE_GROUPS" => 'Y',
		"USE_PERMISSIONS" => 'N',
		"DISPLAY_TOP_PAGER" => 'N',
		"DISPLAY_BOTTOM_PAGER" => 'Y',
		"PAGER_TITLE" => 'Страница',
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "",
		"PAGER_SHOW_ALL" => "N",
		"CHECK_DATES" => "Y",
		"ELEMENT_ID" => $arResult["ID"],
		"USE_SHARE" => "N",
		"SHARE_HIDE" => $arParams["SHARE_HIDE"],
		"DETAIL_URL"	=>	'/'.$arResult["PREFIX"].'#ELEMENT_ID#/',
		"SECTION_CODE"=>"articles"
	),
	$component
);?>
