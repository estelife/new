<?
use core\database\VDatabase;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

global $APPLICATION;
CModule::IncludeModule("estelife");

if (!empty($arResult['SECTION']['PATH']))
	$arResult['LAST_SECTION'] = array_pop($arResult['SECTION']['PATH']);

$arResult['LAST_SECTION']['SECTION_PAGE_URL'] = preg_replace('/stati/', $arParams['SECTION_CODE'], $arResult['LAST_SECTION']['SECTION_PAGE_URL']);
$arResult['IMG']=CFile::GetFileArray($arResult['PROPERTIES']['INSIDE']['VALUE']);

$APPLICATION->AddHeadString('<meta name="og:title" content="'.$arResult["NAME"].'" />');
$APPLICATION->AddHeadString('<meta name="og:description" content="'.$arResult["PREVIEW_TEXT"].'" />');
$APPLICATION->AddHeadString('<meta name="og:image" content="http://estelife.ru'.$arResult['IMG']['SRC'].'" />');

if ($arResult['ID']>0){
	$obLikes=new \like\VLike(\like\VLike::ARTICLE);
	$arResult['LIKES']=$obLikes->getLikes($arResult['ID']);
}

if(!empty($_GET['utm']) && $_GET['utm']=='arc'){
	$arAvNums = array(2,4,6,8);
	$arResult['utm'] = array();

	for($i=0,$c=count($arAvNums); $i<$c; $i++) {
		$nKey = array_rand($arAvNums,1);
		$arResult['utm'][] = $arAvNums[$nKey];
	}

	$arResult['utm'] = implode('', $arResult['utm']);
}