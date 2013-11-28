<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (isset($arParams['IBLOCK_ID']) && intval($arParams['IBLOCK_ID'])>0){
	$arIblockId = $arParams['IBLOCK_ID'];
}else{
	$arIblockId = 4;
}

if (isset($arParams['DEPTH_LEVEL']) && intval($arParams['DEPTH_LEVEL'])>0){
	$arDepthLevel = $arParams['DEPTH_LEVEL'];
}else{
	$arDepthLevel = 1;
}

$arFilter = Array('IBLOCK_ID'=>$arIblockId, 'GLOBAL_ACTIVE'=>'Y',  "DEPTH_LEVEL"=>$arDepthLevel);
$db_list = CIBlockSection::GetList(Array("sort"=>"asc"), $arFilter, true);
$db_list->NavStart(0);

$small = array(3,4,5,6);

$count = 1;
$str = 1;
while($ar_result = $db_list->GetNext())
{
	$x=($str%2 == 0);

	if (empty($ar_result['PICTURE'])) {
		$res = CIBlockElement::GetList(
			Array("SORT"=>"DESC","NAME" => "ASC"),
			Array("IBLOCK_ID"=>$arIblockId, "GLOBAL_ACTIVE"=>"Y", "SECTION_ID"=>$ar_result['ID']),
			false,
			Array("nPageSize" => 1),
			Array()
		);

		$d = array();
		while($ar = $res->GetNextElement())
		{
			$ar_props = $ar->GetProperties();
			$photo = CFile::GetFileArray($ar_props['REAL_PICTURE']['VALUE']);
		}
	} else {
		$photo = CFile::GetFileArray($ar_result['PICTURE']);
	}


	$arResult['photo'][] = array(
		'SRC' => $photo['SRC'],
		'NAME'=>$ar_result['NAME'],
		'SMALL'=>intval($x),
		'URL' => $ar_result['SECTION_PAGE_URL'],
	);

	if(($count%($x?4:2))==0){
		$str++;
		$count=0;
	}

	$count++;
}
$this->IncludeComponentTemplate($componentPage);