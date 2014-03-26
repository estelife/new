<?php
$_SERVER["DOCUMENT_ROOT"] = realpath(__DIR__.'/../');
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
@set_time_limit(0);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('estelife');

VRegistry::get('managment')->init('catalog');
VRegistry::get('managment')->init('geo');
VGeo::cities()->setSelect(new VPartSelect(array(
	'id'
)));
$arCities=VGeo::cities()->items();
$obObjects=new VObjects();
$obObjects->setSelect(new VPartSelect(array(
	'rating','id'
)));
$arDeleted=array();

foreach($arCities as $arCity){
	$obRatingInfo=new VRatingInfo();
	$arScore=$obRatingInfo->setFilter(new VPartFilter(array(
		'city_id'=>$arCity['id']
	)))->items();

	if(empty($arScore))
		continue;

	$arObjects=array();
	$arCounts=array();

	foreach($arScore as $arValue){
		$arObjects[$arValue['object_id']][]=$arValue['value'];

		if(!isset($arCounts[$arValue['object_id']]))
			$arCounts[$arValue['object_id']]=0;

		$arCounts[$arValue['object_id']]++;
	}

	$nMaxScore=pow(max(array_values($arCounts)),1/10);
	$obAction=new VObjectAction(null,null,VObjectAction::UPDATE);

	foreach($arObjects as $nObjectId=>$arScore){
		$nCount=count($arScore);
		$nScore=array_sum($arScore)/$nCount;

		$nRating=(log($nMaxScore,$nCount)+$nScore)/2;

		if(is_nan($nRating))
			$nRating=0;

		try{
			$obObject=$obObjects->item($nObjectId);
			$obObject->set('rating',$nRating);
			$obObject->write();
		}catch(VException $e){
			$arDeleted[]=$nObjectId;
		}
	}
}

if(!empty($arDeleted)){
	$obRatingInfo->setActionFilter(new VPartFilter(array(
		'->object_id'=>$arDeleted
	)))->delete();
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");