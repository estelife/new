<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

CModule::IncludeModule("estelife");

if (!empty($arResult['SECTION']['PATH'])){
	$arResult['LAST_SECTION'] = array_pop($arResult['SECTION']['PATH']);
}
