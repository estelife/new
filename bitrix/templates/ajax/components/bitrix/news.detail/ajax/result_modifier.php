<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

CModule::IncludeModule("estelife");

if (!empty($arResult['SECTION']['PATH'])){
	$arResult['LAST_SECTION'] = array_pop($arResult['SECTION']['PATH']);
}


$arResult['LAST_SECTION']['SECTION_PAGE_URL'] = preg_replace('/stati/', $arParams['SECTION_CODE'], $arResult['LAST_SECTION']['SECTION_PAGE_URL']);

if ($arResult['ID']>0){
	$obLikes=new \like\VLike(\like\VLike::ARTICLE);
	$arResult['LIKES']=$obLikes->getLikes($arResult['ID']);
}

$arIblockIds = array(
	14 => 'ar',
	36 => 'pt',
	3 => 'ns'
);
$arResult['TYPE']=$arIblockIds[$arResult['IBLOCK_ID']];