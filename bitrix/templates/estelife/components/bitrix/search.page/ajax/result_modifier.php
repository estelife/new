<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

$arResult['NAV']=array();

if(isset($arResult['NAV_RESULT']) && is_object($arResult['NAV_RESULT'])){
	$obNav=new \bitrix\VNavigation($arResult['NAV_RESULT'],true);
	$arResult['NAV']=$obNav->getAjaxNav();
}

if(isset($arResult['REQUEST'])){
	unset(
		$arResult['REQUEST']['FROM'],
		$arResult['REQUEST']['TO'],
		$arResult['REQUEST']['WHERE'],
		$arResult['REQUEST']['~FROM'],
		$arResult['REQUEST']['~QUERY'],
		$arResult['REQUEST']['~TAGS'],
		$arResult['REQUEST']['~TAGS_ARRAY'],
		$arResult['REQUEST']['~TO']
	);
}

if(isset($arResult['SEARCH'])){
	$arAvai=array('TITLE_FORMATED','BODY_FORMATED','DATE_CHANGE','TAGS','CHAIN_PATH','SEARCH_PATH');

	foreach($arResult['SEARCH'] as &$arSearch){
		foreach($arSearch as $sKey=>&$mValue){
			if(!in_array($sKey,$arAvai))
				unset($arSearch[$sKey]);

			if($sKey=='TAGS'){
				foreach($mValue as &$arTag){
					$arTag['URL']=preg_replace('#^\/rest#','',$arTag['URL']);
				}
			}
		}
	}
}

if(isset($arResult['URL'])){
	$arResult['URL']=preg_replace('#^\/rest#','',$arResult['URL']);
}