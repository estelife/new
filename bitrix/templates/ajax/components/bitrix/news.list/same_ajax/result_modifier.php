<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(isset($arResult['ITEMS'])){
	$arAvai=array('SRC','NAME','PREVIEW_TEXT','DETAIL_PAGE_URL','ACTIVE_FROM', 'LIKES');
	foreach ($arResult["ITEMS"] as $arItem){
		$arIds[] = $arItem['ID'];
	}
	if (!empty($arIds)){
		if (!empty($arIds)){
			$obLike = new \like\VLike(\like\VLike::ARTICLE);
			$arNewLikes= $obLike->getOnlyLikes($arIds);
		}
	}
	foreach($arResult['ITEMS'] as &$arItem){
		$arItem['LIKES'] = $arNewLikes[$arItem['ID']];
		$arItem['PREVIEW_TEXT']=\core\types\VString::truncate($arItem['PREVIEW_TEXT'], 70, '...').'<span></span>';

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