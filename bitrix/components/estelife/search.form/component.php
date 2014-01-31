<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if(!isset($arParams["PAGE"]) || strlen($arParams["PAGE"])<=0)
	$arParams["PAGE"] = "#SITE_DIR#search/index.php";

$arResult["FORM_ACTION"] = htmlspecialcharsbx(str_replace("#SITE_DIR#", SITE_DIR, $arParams["PAGE"]));

$this->IncludeComponentTemplate();