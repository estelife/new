<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

if (isset($arParams['FORM']) && is_object($arParams['FORM']))
	$obForm = $arParams['FORM'];

if (isset($arParams['ERRORS']) && !empty($arParams['ERRORS']))
	$arResult['errors'] = $arParams['ERRORS'];

$obForm->getScriptForToken();

$arResult['form'] = array(
	'fields' => $obForm->getFields(),
	'name' => $obForm->getName(),
	'id' => $obForm->getId(),
	'action' => $obForm->getAction(),
	'method' => $obForm->getMethod(),
	'create_token' => $obForm->getScriptForToken(true)
);


$this->IncludeComponentTemplate();
