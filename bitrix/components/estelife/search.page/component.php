<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (isset($arParams['MODE']) && !empty($arParams['MODE']))
	$sMode=$arParams['MODE'];
else
	$sMode='SPH_MATCH_ALL';

if (isset($arParams['QUERY_TIME']) && !empty($arParams['QUERY_TIME']))
	$nTime=intval($arParams['QUERY_TIME']);
else
	$nTime=20;

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
	$obSph=new SphinxClient;
	$obSph->setServer('localhost', 3312);
	$obSph->setMaxQueryTime($nTime);
	$obSph->setArrayResult(true);
	$obSph->setMatchMode(SPH_MATCH_ALL);

	if (!empty($sSort))
		$obSph->setSortMode(SPH_SORT_ATTR_DESC, $sSort);

	$obSph->setFieldWeights(array(
		'search-name'=>'100',
		'search-category'=>'80',
		'search-preview'=>'60',
		'search-detail'=>'70',
		'search-tags'=>'90'
	));
	$obSph->resetFilters();
//	$obSph->setFilter('city', array(0, intval($_COOKIE['city'])));

	if (!empty($sTags)){
		$obSph->setMatchMode(SPH_MATCH_EXTENDED);
		$arAnswer=$obSph->query('@search-tags: '.$sTags);
	}else
		$arAnswer=$obSph->query($sQuery, '*');

	if (!empty($arAnswer['matches'])){
		$arAnswer=$arAnswer['matches'];
		$nCount=count($arAnswer);

		$nCountPages=intval(($nCount-1)/abs($nStep))+1;

		if($nPage<0)
			$nPage=1;
		if($nPage>$nCountPages)
			$nPage=$nCountPages;

		$nStart=$nStep*$nPage-$nStep;
		$arAnswer=array_slice($arAnswer, $nStart, $nStep);

		if (!empty($arAnswer)){

			$arTypes=$APPLICATION->IncludeComponent(
				'estelife:system-settings',
				'',
				array('filter'=>'types')
			);

			foreach ($arAnswer as $val){
				$val=$val['attrs'];
				$val['src']='/'.$arTypes[$val['type']].$val['id'].'/';
				$val['date_edit']=date('d.m.Y', $val['date_edit']);
				$val['tags']=explode(', ', $val['tags']);
				$arResult['search']['result'][]=$val;
			}
		}
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