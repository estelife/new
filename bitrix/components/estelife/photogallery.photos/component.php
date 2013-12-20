<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");

$nId=(isset($arParams['ID'])) ?
	$arParams['ID'] : 0;
$sKey=(is_numeric($nId)) ?
	'SECTION_ID' : 'SECTION_CODE';

$obSection=CIBlockSection::GetByID($nId);
$arSection=$obSection->Fetch();

if(!empty($arSection)){
	$arResult['gallery']=array(
		'name'=>$arSection['NAME'],
		'description'=>$arSection['PREVIEW_TEXT']
	);

	$db_list=CIBlockElement::GetList(
		array("created"=>"desc"),
		array(
			'IBLOCK_ID'=>4,
			'GLOBAL_ACTIVE'=>'Y',
			"DEPTH_LEVEL"=>1,
			'SECTION_ID'=>$arSection['ID']
		),
		false,
		array('nPageSize'=>100),
		array('ID', 'NAME','PREVIEW_TEXT', 'PREVIEW_PICTURE','PROPERTY_REAL_PICTURE')
	);

	$arResult['images']=array();

	while ($arPhoto=$db_list->Fetch()){
		$arPreview=CFile::GetFileArray($arPhoto['PREVIEW_PICTURE']);
		$arDetail=CFile::GetFileArray($arPhoto['PROPERTY_REAL_PICTURE_VALUE']);

		$arResult['images'][]=array(
			'id'=>$arPhoto['ID'],
			'title'=>$arPhoto['PREVIEW_TEXT'],
			'small'=>$arPreview['SRC'],
			'big'=>$arDetail['SRC']
		);
	}
}

$this->IncludeComponentTemplate($componentPage);