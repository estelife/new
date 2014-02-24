<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arDirectories=$APPLICATION->IncludeComponent(
	'estelife:system-settings',
	'',
	array('filter'=>'directions')
);
$arDefaultUrlTemplates404 = array(
	"articles" => "#CURRENT_CODE#/",
);
$arDefaultVariableAliases404 = array();
$arDefaultVariableAliases = array();
$arComponentVariables = array(
	"CURRENT_CODE",
);
$arVariables = array();

$arUrlTemplates = CComponentEngine::MakeComponentUrlTemplates($arDefaultUrlTemplates404, array());
$arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases404, $arParams["VARIABLE_ALIASES"]);

$componentPage = CComponentEngine::ParseComponentPath(
	$arParams["SEF_FOLDER"],
	$arUrlTemplates,
	$arVariables
);

//разбираем $arVariables
CModule::IncludeModule('estelife');
$sCode=htmlspecialchars($arVariables['CURRENT_CODE'],ENT_QUOTES,'utf-8');
$arLink = preg_match('/^([a-z]{2})([0-9]+)$/', $sCode, $matches);
$bNotFound = false;

if (!$arLink || empty($arDirectories[$matches[1]])){
	$componentPage='index';
	$bNotFound = true;
}else{
	$componentPage = $arDirectories[$matches[1]];

	if(in_array($matches[1],array('ar','pt','ns'))){
		$obQuery = \core\database\VDatabase::driver()->createQuery();
		$obQuery->builder()
			->from('iblock_element')
			->filter()
			->_eq('ID',intval($matches[2]));

		if(!$obQuery->count()){
			$componentPage='index';
			$bNotFound = true;
		}

	}
}

if($bNotFound){
	$folder404 = str_replace("\\", "/", $arParams["SEF_FOLDER"]);
	if ($folder404 != "/")
		$folder404 = "/".trim($folder404, "/ \t\n\r\0\x0B")."/";
	if (substr($folder404, -1) == "/")
		$folder404 .= "index.php";

	if($folder404 != $APPLICATION->GetCurPage(true))
	{
		$componentPage='404';
		$APPLICATION->SetTitle("404 Not Found");
		CHTTP::SetStatus("404 Not Found");
	}
}

CComponentEngine::InitComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);

$arResult = array(
	"FOLDER" => $arParams["SEF_FOLDER"],
	"URL_TEMPLATES" => $arUrlTemplates,
	"VARIABLES" => $arVariables,
	"ALIASES" => $arVariableAliases,
	"ID" => $matches[2],
	"PREFIX" => $matches[1],
);

$this->IncludeComponentTemplate($componentPage);