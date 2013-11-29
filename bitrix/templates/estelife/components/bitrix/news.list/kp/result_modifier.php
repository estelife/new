<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

CModule::IncludeModule("estelife");
if (!empty($arResult['ITEMS'])){
	$arResult['FIRST'] = array_shift($arResult['ITEMS']);
}
