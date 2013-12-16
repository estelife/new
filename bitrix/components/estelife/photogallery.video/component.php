<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");

$nId=(isset($arParams['ID'])) ?
	intval($arParams['ID']) : 0;

$db_list=CIBlockElement::GetList(
	array("created"=>"desc"),
	array(
		'IBLOCK_ID'=>33,
		'GLOBAL_ACTIVE'=>'Y',
		"DEPTH_LEVEL"=>1,
		'ID'=>$nId
	),
	false,
	array('nPageSize'=>1),
	array('ID', 'NAME', 'PROPERTY_VIDEO','PREVIEW_TEXT')
);

if($db_list->SelectedRowsCount()>0){
	$arVideo=$db_list->Fetch();

	if(preg_match('#([a-z0-9_\-]+)$#i',$arVideo['PROPERTY_VIDEO_VALUE'],$arMatches)){
		$arResult['video']=array(
			'id'=>$arVideo['ID'],
			'title'=>$arVideo['NAME'],
			'video_id'=>$arMatches[1],
			'description'=>$arVideo['PREVIEW_TEXT']
		);
	}
}

$this->IncludeComponentTemplate($componentPage);