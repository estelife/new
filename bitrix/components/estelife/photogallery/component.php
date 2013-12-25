<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

CModule::IncludeModule("iblock");

if ($arParams['ONLY_VIDEO'] == "Y"){
	//Получение видео
	$arFilter = array('IBLOCK_ID'=>33, 'GLOBAL_ACTIVE'=>'Y');
	$arSelect = array('ID', 'NAME', 'PREVIEW_PICTURE', 'CODE');
	$db_list = CIBlockElement::GetList(Array("created"=>"desc"), $arFilter, false, array('nPageSize'=>6), $arSelect);
	$nKey=0;

	while ($res = $db_list->Fetch()){
		$res['IS_VIDEO'] = 'Y';
		$res['LINK'] = '/video/'.$res['CODE'].'/';
		$res['IMG'] = CFile::GetFileArray($res['PREVIEW_PICTURE']);
		$res['IMG'] = $res['IMG']['SRC'];
		$res['KEY'] = ++$nKey;
		$arVideos[] =  $res;
	}

	$arResult = $arVideos;
}
$arCountPhotos = abs(intval($arParams['COUNT']) - count($arVideos));

if ($arParams['ONLY_PHOTO'] == "Y"){
	//Получение фотогалерей
	$arFilter = array('IBLOCK_ID'=>4, 'GLOBAL_ACTIVE'=>'Y',  "DEPTH_LEVEL"=>1);
	$arSelect = array('ID', 'NAME', 'PICTURE', 'CODE');
	$db_list = CIBlockSection::GetList(Array("created"=>"desc"), $arFilter, true, $arSelect, array('nPageSize'=>$arCountPhotos));
	$nKey=0;

	while ($res = $db_list->Fetch()){
		$res['LINK'] = '/photo/'.$res['ID'].'/';
		$res['IMG'] = CFile::GetFileArray($res['PICTURE']);
		$res['IMG'] = $res['IMG']['SRC'];
		$res['KEY'] = ++$nKey;
		$arPhotos[]=$res;
	}
	$arResult = $arPhotos;


	if (!empty($arVideos)){
		$i = 1;
		foreach ($arVideos as $val){
			array_splice($arPhotos, 3*$i, 0, array($val));
			$i++;
		}

		$arResult = $arPhotos;
	}
}

$this->IncludeComponentTemplate($componentPage);