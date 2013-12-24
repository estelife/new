<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
global $APPLICATION;

$arDirectories=array_values($APPLICATION->IncludeComponent(
	'estelife:system-settings',
	'',
	array('filter'=>'directions')
));

if(isset($arParams['DIRECTORIES']) && is_array($arParams['DIRECTORIES']))
	$arDirectories=array_merge($arDirectories,$arParams['DIRECTORIES']);

$arDefaultUrlTemplates404 = array(
	"articles" => "#CURRENT_CODE#/#DOP_CODE#/",
	"sections" => "#CURRENT_CODE#/",
);
$arDefaultVariableAliases404 = array();
$arDefaultVariableAliases = array();
$arComponentVariables = array(
	"CURRENT_CODE",
	"DOP_CODE"
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
$sDopCode=htmlspecialchars($arVariables['DOP_CODE'],ENT_QUOTES,'utf-8');

if (empty($sCode) || !in_array($sCode, $arDirectories))
{
	$componentPage='index';
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
}else{
	$componentPage = $sCode;
	$arPrefix = array_search($sCode ,$arDirectories);
}

CComponentEngine::InitComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);

$arResult = array(
	"FOLDER" => $arParams["SEF_FOLDER"],
	"URL_TEMPLATES" => $arUrlTemplates,
	"VARIABLES" => $arVariables,
	"ALIASES" => $arVariableAliases,
	"PREFIX"=> $arPrefix
);

$this->IncludeComponentTemplate($componentPage);