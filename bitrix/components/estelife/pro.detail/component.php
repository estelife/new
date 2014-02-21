<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

global $APPLICATION;
CModule::IncludeModule('iblock');

$nIblockId = isset($arParams['IBLOCK_ID']) ? intval($arParams['IBLOCK_ID']) : 0;
$arPath = explode('/', GetPagePath());
$arPath = array_splice($arPath, 2, -1);
$sPath = '/'.implode('/',$arPath).'/';

$obElement = new CIBlockElement();
$obResult = $obElement->GetList(
	array('SORT'=>'asc'),
	array(
		'PROPERTY_PATH'=>$sPath,
		'IBLOCK_ID' => $nIblockId
	),
	false,
	array('nPageSize'=>1)
);

if($obResult->SelectedRowsCount() > 0){
	$arResult = $obResult->Fetch();

	$ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($nIblockId, $arResult["ID"]);
	$arSeoProperties = $ipropValues->getValues();

	$sSeoTitle = isset($arSeoProperties['ELEMENT_META_TITLE']) ?
		$arSeoProperties['ELEMENT_META_TITLE'] :
		$arResult['NAME'];
	$sSeoDescription = isset($arSeoProperties['ELEMENT_META_DESCRIPTION']) ?
		$arSeoProperties['ELEMENT_META_DESCRIPTION'] :
		mb_substr($arResult['DETAIL_TEXT'],0,160,'utf-8');
	$sSeoKeywords = isset($arSeoProperties['ELEMENT_META_KEYWORDS']) ?
		$arSeoProperties['ELEMENT_META_KEYWORDS'] :
		$sSeoDescription;

	$APPLICATION->SetPageProperty("title", $sSeoTitle);
	$APPLICATION->SetPageProperty("description", $sSeoDescription);
	$APPLICATION->SetPageProperty("keywords", $sSeoKeywords);
}

$arResult['PATH'] = '/pro'.$sPath;
$this->IncludeComponentTemplate();