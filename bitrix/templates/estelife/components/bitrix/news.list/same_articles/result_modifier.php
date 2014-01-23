<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

CModule::IncludeModule("estelife");

if (!empty($arResult['SECTION']['PATH'])){
	$arResult['LAST_SECTION'] = array_pop($arResult['SECTION']['PATH']);
}

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
	}
}