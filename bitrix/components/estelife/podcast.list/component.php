<?php
use core\database\VDatabase;
use core\exceptions\VException;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

try{
	if (empty($arParams['SECTION_ID']) )
		throw new VException("Ошибка в задании параметров");

	CModule::IncludeModule("iblock");
	CModule::IncludeModule("estelife");

	//Получение ID секции
	$sNow = date('d.m.Y', time());
	$sDateNow = strtotime($sNow.' 00:00:00');

	$arFilter = array(
		'IBLOCK_ID' => $arParams['IBLOCK_ID'],
		'SECTION_ID' => $arParams['SECTION_ID'],
		'<=UF_DATE_PUB_SECTION' => $sNow

	);
	$arSelect = array('ID', 'NAME', 'UF_DATE_PUB_SECTION', 'UF_DATE_UPD_SECTION');
	$obResult = CIBlockSection::GetList(Array('UF_DATE_PUB_SECTION'=>'DESC'), $arFilter, false, $arSelect, array('nPageSize'=>1));

	while($asRes = $obResult->Fetch()){
		$arSection = $asRes;
	}

	$arResult['SECTION_NAME'] = $arSection['NAME'];

	//проверка на публикацию сегодня
	$flag = 0;
	$sSectionTime = date('d.m.Y', strtotime($arSection['UF_DATE_UPD_SECTION']));
	if (strtotime($sSectionTime.' 00:00:00') < $sDateNow){
		$flag = 1;
		$bs = new CIBlockSection;
		$arFields = Array(
			"UF_DATE_UPD_SECTION" => $sNow,
			"IBLOCK_ID" => $arParams['IBLOCK_ID'],
		);
		$res = $bs->Update($arSection['ID'], $arFields);
	}


	//Получение статей
	if (!empty($arSection)){
		$arFilter = array(
			'IBLOCK_ID' => $arParams['IBLOCK_ID'],
			'SECTION_ID' => $arSection['ID'],
			'ACTIVE' => 'Y'
		);
		$arSelect = array('ID', 'NAME', 'PREVIEW_TEXT', 'PROPERTY_COUNT', 'PROPERTY_FRONTRIGHT', 'PROPERTY_FRONTBIG');
		$obResult = CIBlockElement::GetList(Array('PROPERTY_COUNT'=>'ASC'), $arFilter, false, array('nPageSize'=>$arParams['NEWS_COUNT']),$arSelect);
		$bFirst=true;

		while($asRes = $obResult->Fetch()){
			$asRes['DETAIL_URL'] = '/'.$arParams['PREFIX'].$asRes['ID'].'/';

			if($bFirst){
				$asRes['PREVIEW_TEXT_B'] = \core\types\VString::truncate($asRes['PREVIEW_TEXT'], 165, '...').'<span></span>';
				$asRes['IMG_B'] = CFile::GetFileArray($asRes['PROPERTY_FRONTBIG_VALUE']);
				$asRes['IMG_B']=$asRes['IMG_B']['SRC'];
				$bFirst=false;
			}

			//$asRes['PREVIEW_TEXT_S'] = \core\types\VString::truncate($asRes['PREVIEW_TEXT'], 30, '...');
			$asRes['IMG_S'] = CFile::GetFileArray($asRes['PROPERTY_FRONTRIGHT_VALUE']);
			$asRes['IMG_S']=$asRes['IMG_S']['SRC'];

			unset(
				$asRes['PREVIEW_TEXT'],
				$asRes['PROPERTY_FRONTRIGHT_VALUE'],
				$asRes['PROPERTY_FRONTBIG_VALUE'],
				$asRes['PROPERTY_FRONTRIGHT_VALUE_ID'],
				$asRes['PROPERTY_FRONTBIG_VALUE_ID']
			);
			$arElements[] = $asRes;
		}

	}
	$arResult['FIRST']=array_shift($arElements);
	unset($arResult['FIRST']['IMG_S']);

	if ($flag == 1){
		$nValue=$arResult['FIRST']['PROPERTY_COUNT_VALUE'] + 1;
		CIBlockElement::SetPropertyValues($arResult['FIRST']['ID'], $arParams['IBLOCK_ID'], $nValue, 'COUNT');
	}

	if (!empty($arElements)){
		$arResult['ELEMENTS'] = $arElements;
	}
}catch(VException $e){
	echo $e->getMessage(), "\n";
}
$this->IncludeComponentTemplate();