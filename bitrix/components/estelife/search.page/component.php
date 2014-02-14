<?php
use core\types\VArray;
require $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/estelife/classes/search/sphinxapi.php';

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (isset($arParams['MODE']) && !empty($arParams['MODE']))
	$sMode=$arParams['MODE'];
else
	$sMode='SPH_MATCH_ALL';

if (isset($arParams['NAV_COUNT']) && !empty($arParams['NAV_COUNT']))
	$nStep=intval($arParams['NAV_COUNT']);
else
	$nStep=10;

if (isset($_REQUEST['q']) && !empty($_REQUEST['q']))
	$sQuery=trim(addslashes($_REQUEST['q']));
else
	$sQuery='';
$arResult['search']['query']=$sQuery;

if (isset($_REQUEST['PAGEN_1']) && !empty($_REQUEST['PAGEN_1']))
	$nPage=intval($_REQUEST['PAGEN_1']);
else
	$nPage=1;


//Работа с URL
$arResult['url']='/search/'."?q=".urlencode($sQuery);

if (isset($_REQUEST['tags']) && !empty($_REQUEST['tags']))
	$sTags=trim(addslashes($_REQUEST['tags']));
else
	$sTags='';

$arResult['search']['tags']=$tags;

if (isset($_REQUEST['how']) && $_REQUEST['how']=='d'){
	$sSort='date_edit';
}
$sHow=trim(addslashes($_REQUEST['how']));
$arResult['search']['how']=$sHow;

//урл дтя тегов
$arResult['search']['tags_url']=$arResult['url'].(!empty($sHow)? "&amp;how=".urlencode($sHow): "");
//урл для сортировки
$arResult['search']['sort_url']=$arResult['url'].(!empty($sTags)? "&amp;tags=".urlencode($sTags): "");

if (!empty($sQuery)){
	$obSph=new \search\VSearch();

	if (!empty($sSort))
		$obSph->setSort($sSort);

	if (!empty($sTags)){
		$arAnswer=$obSph->searchByTags($sTags);
	}else
		$arAnswer=$obSph->search($sQuery);

	if (!empty($arAnswer['matches'])){
		$arAnswer=$arAnswer['matches'];
		$nCount=count($arAnswer);
		$nCountPages=intval(($nCount-1)/abs($nStep))+1;

		if($nPage<0)
			$nPage=1;

		if($nPage>$nCountPages)
			$nPage=$nCountPages;

		$nStart=$nStep*$nPage-$nStep;

		$arTypes=$APPLICATION->IncludeComponent(
			'estelife:system-settings',
			'',
			array('filter'=>'types')
		);

		foreach ($arAnswer as $val){
			$val=$val['attrs'];
			$val['src']='/'.$arTypes[$val['type']].$val['id'].'/';
			$val['date_edit']=date('d.m.Y', $val['date_edit']);

			if(!empty($val['tags'])){
				$val['tags']=explode(',', $val['tags']);

				foreach($val['tags'] as &$sTag) {
					$sTag=trim($sTag);
					$sTag='<a href="'.$arResult['search']["tags_url"].'&tags='.$sTag.'?>">'.$sTag.'</a>';
				}

				$val['tags']=VArray::toTruncatedString($val['tags'],5);
			}

			$arTempResult[$val['type']][]=$val;
		}
		$arRelevant=array(21, 20, 8, 9, 6, 7, 5, 4, 12, 13, 15, 1, 2, 3, 4, 10, 11);

		$arResult['search']['result']=array();
		foreach ($arRelevant as $val){
			if (!empty($arTempResult[$val]))
				$arResult['search']['result']=array_merge($arResult['search']['result'], $arTempResult[$val]);
		}
		$arResult['search']['result']=array_slice($arResult['search']['result'], $nStart, $nStep);
	}
}


$arNav=array(
	'count'=>$nCount,
	'page'=>$nPage,
	'step'=>$nStep,
	'pageCount'=>$nCountPages,
	'pageWindow'=>5
);

$sTemplate=$this->getTemplateName();
$obNav=new \bitrix\VNavigationArray($arNav,($sTemplate=='ajax'));
$arResult['nav']=$obNav->getNav();

$APPLICATION->SetPageProperty("title", "Поиск");
$APPLICATION->SetPageProperty("description", "Поиск");
$APPLICATION->SetPageProperty("keywords", "Estelife, Поиск");

$this->IncludeComponentTemplate();