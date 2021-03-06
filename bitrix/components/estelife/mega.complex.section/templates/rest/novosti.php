<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
CModule::IncludeModule("iblock");
if (!empty($arResult["VARIABLES"]["DOP_CODE"])){
	$arSection = CIBlockSection::GetList(Array($by=>$order), array('IBLOCK_ID'=>3, "CODE"=>$arResult["VARIABLES"]["DOP_CODE"]), false, array('ID'))->Fetch();

	if (empty($arSection))
		throw new \core\exceptions\VHttpEx('Page not found',404);
}
?>
<?$APPLICATION->IncludeComponent(
	"bitrix:news.list",
	"ajax",
	Array(
		"IBLOCK_TYPE"	=>	"news",
		"IBLOCK_ID"	=>	"3",
		"NEWS_COUNT"	=>	"9",
		"SORT_BY1"	=>	"ACTIVE_FROM",
		"SORT_ORDER1"	=>	"DESC",
		"SORT_BY2"	=>	"SORT",
		"SORT_ORDER2"	=>	"ASC",
		"FIELD_CODE"	=>	array("ID", "CODE", "XML_ID", "NAME", "TAGS", "SORT", "PREVIEW_TEXT", "PREVIEW_PICTURE", "DETAIL_TEXT", "DETAIL_PICTURE", "DATE_ACTIVE_FROM", "ACTIVE_FROM", "DATE_ACTIVE_TO", "ACTIVE_TO", "SHOW_COUNTER", "SHOW_COUNTER_START", "IBLOCK_TYPE_ID", "IBLOCK_ID", "IBLOCK_CODE", "IBLOCK_NAME", "IBLOCK_EXTERNAL_ID", "DATE_CREATE", "CREATED_BY", "CREATED_USER_NAME", "TIMESTAMP_X", "MODIFIED_BY", "USER_NAME"),
		"PROPERTY_CODE"	=>	array("BROWSER_TITLE", "DESCRIPTION", "KEYWORDS", "SOURCE", "FORUM_MESSAGE_CNT", "FORUM_TOPIC_ID", "GALLERY", "VIDEO", "ISVIDEO"),
		"SET_TITLE"	=>	"Y",
		"SET_STATUS_404" => "Y",
		"INCLUDE_IBLOCK_INTO_CHAIN"	=>	"Y",
		"ADD_SECTIONS_CHAIN"	=>	"Y",
		"CACHE_TYPE"	=>	"A",
		"CACHE_TIME"	=>	"3600",
		"CACHE_FILTER"	=>	"N",
		"CACHE_GROUPS" => "Y",
		"DISPLAY_TOP_PAGER"	=>	"N",
		"DISPLAY_BOTTOM_PAGER"	=>	"Y",
		"PAGER_TITLE"	=>	"Статьи",
		"PAGER_TEMPLATE"	=>	"estelife",
		"PAGER_SHOW_ALWAYS"	=>	"N",
		"PAGER_DESC_NUMBERING"	=>	"N",
		"PAGER_DESC_NUMBERING_CACHE_TIME"	=>	36000,
		"PAGER_SHOW_ALL" => "N",
		"DISPLAY_DATE"	=>	"Y",
		"DISPLAY_NAME"	=>	"Y",
		"DISPLAY_PICTURE"	=>	"Y",
		"DISPLAY_PREVIEW_TEXT"	=>	"Y",
		"PREVIEW_TRUNCATE_LEN"	=>	"0",
		"ACTIVE_DATE_FORMAT"	=>	"j F Y",
		"USE_PERMISSIONS"	=>	"N",
		"HIDE_LINK_WHEN_NO_DETAIL"	=>	"N",
		"CHECK_DATES"	=>	"Y",

		"PARENT_SECTION"	=>	$arResult["VARIABLES"]["SECTION_ID"],
		"PARENT_SECTION_CODE"	=>	$arResult["VARIABLES"]["DOP_CODE"],
		"DETAIL_URL"	=>	'/ns#ELEMENT_ID#/',
		"SECTION_URL"	=>	'#SECTION_CODE#/',
	),
	$component
);?>
