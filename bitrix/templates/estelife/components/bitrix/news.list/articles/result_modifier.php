<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

CModule::IncludeModule("estelife");

if (!empty($arResult['SECTION']['PATH'])){
	$arResult['LAST_SECTION'] = array_pop($arResult['SECTION']['PATH']);
}

if (isset($_GET['PAGEN_3'])){
	$arDopSection = " - cтраница ".intval($_GET['PAGEN_3']);
}elseif(isset($_GET['PAGEN_2'])){
	$arDopSection = " - cтраница ".intval($_GET['PAGEN_2']);
}elseif(isset($_GET['PAGEN_1'])){
	$arDopSection = " - cтраница ".intval($_GET['PAGEN_1']);
}
$arSectionNameForSeo = $arResult['LAST_SECTION']['NAME'].$arDopSection;
$APPLICATION->SetPageProperty("title", $arSectionNameForSeo);
$APPLICATION->SetPageProperty("description", "Все статьи по теме ".$arSectionNameForSeo);
$APPLICATION->SetPageProperty("keywords", "Estelife, ".$arResult['LAST_SECTION']['NAME']);