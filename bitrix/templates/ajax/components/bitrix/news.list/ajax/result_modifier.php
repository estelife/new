<?php
use core\database\VDatabase;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!empty($arResult['SECTION']['PATH']))
	$arResult['LAST_SECTION'] = array_pop($arResult['SECTION']['PATH']);

$arResult['NAV']=array();

if(isset($arResult['NAV_RESULT']) && is_object($arResult['NAV_RESULT'])){
	$obNav=new \bitrix\VNavigation($arResult['NAV_RESULT'],true);
	$arResult['NAV']=$obNav->getAjaxNav();
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

if(isset($arResult['ITEMS'])){
	foreach ($arResult["ITEMS"] as $arItem){
		$arIds[] = $arItem['ID'];
	}

	if (!empty($arIds)){
		$obLike = new \like\VLike(\like\VLike::ARTICLE);
		$arNewLikes= $obLike->getOnlyLikes($arIds);
	}
	$arAvai=array('SRC','NAME','PREVIEW_TEXT','DETAIL_PAGE_URL','ACTIVE_FROM','LIKES');

	foreach($arResult['ITEMS'] as &$arItem){
		$arItem['LIKES'] = $arNewLikes[$arItem['ID']];
		if (!empty($arItem['PROPERTIES']['SHORT_TEXT']['VALUE']['TEXT'])){
			$arItem['PREVIEW_TEXT']=$arItem['PROPERTIES']['SHORT_TEXT']['VALUE']['TEXT'].'<span></span>';
		}else{
			$arItem['PREVIEW_TEXT']=\core\types\VString::truncate($arItem['PREVIEW_TEXT'], 70, '...').'<span></span>';
		}

		if(!empty($arItem['ACTIVE_FROM']))
			$arItem['ACTIVE_FROM']=date('d.m.Y',strtotime($arItem['ACTIVE_FROM']));

		$img=CFile::GetFileArray($arItem['PROPERTIES']['LISTIMG']['VALUE']);
		$arItem['SRC']=$img['SRC'];

		foreach($arItem as $sKey=>$mValue){
			if(!in_array($sKey,$arAvai))
				unset($arItem[$sKey]);
		}
	}
}