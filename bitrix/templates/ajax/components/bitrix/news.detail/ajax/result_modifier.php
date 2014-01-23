<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

CModule::IncludeModule("estelife");

if (!empty($arResult['SECTION']['PATH'])){
	$arResult['LAST_SECTION'] = array_pop($arResult['SECTION']['PATH']);
}
$arResult['LAST_SECTION']['SECTION_PAGE_URL'] = preg_replace('/stati/', $arParams['SECTION_CODE'], $arResult['LAST_SECTION']['SECTION_PAGE_URL']);
