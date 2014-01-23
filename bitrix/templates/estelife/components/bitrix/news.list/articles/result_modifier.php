<?
use core\database\VDatabase;

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

if (!empty($arResult["ITEMS"])){
	foreach ($arResult["ITEMS"] as $arItem){
		$arIds[] = $arItem['ID'];
	}
	if (!empty($arIds)){
		if (!empty($arIds)){
			$obLike = new \like\VLike(\like\VLike::ARTICLE);
			$arNewLikes= $obLike->getOnlyLikes($arIds);
		}
	}

	foreach ($arResult["ITEMS"] as &$arItem){
		$arItem['LIKES'] = $arNewLikes[$arItem['ID']];
		if (!empty($arItem['PROPERTIES']['SHORT_TEXT']['VALUE']['TEXT'])){
			$arItem['PREVIEW_TEXT']=$arItem['PROPERTIES']['SHORT_TEXT']['VALUE']['TEXT'].'<span></span>';
		}else{
			$arItem['PREVIEW_TEXT']=\core\types\VString::truncate($arItem['PREVIEW_TEXT'], 70, '...').'<span></span>';
		}
	}
}

