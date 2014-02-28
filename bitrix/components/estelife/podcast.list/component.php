<?php
use core\database\VDatabase;
use core\exceptions\VException;
use core\types\VString;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

try{

	CModule::IncludeModule("iblock");
	CModule::IncludeModule("estelife");

	//Получение ID секции
	$sNow = date('d.m.Y H:i:s', time());
	$sDateNow =strtotime(preg_replace('/([0-9]{2}\:?){3}$/', '00:00:00', $sNow));

	$obResult = CIBlockSection::GetList(
		array('UF_DATE_PUB_SECT'=>'DESC'),
		array(
			'IBLOCK_ID' => $arParams['IBLOCK_ID'],
			'SECTION_ID' => $arParams['SECTION_ID'],
			'<=UF_DATE_PUB_SECT' => $sNow,
			'ACTIVE'=>'Y'
		),
		false,
		array(
			'ID',
			'NAME',
			'UF_DATE_PUB_SECT',
			'UF_DATE_UPD_SECTION'
		),
		array('nPageSize'=>1)
	);

	while($asRes = $obResult->Fetch())
		$arSection = $asRes;

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
		$obResult=CIBlockElement::GetList(
			array('PROPERTY_COUNT'=>'ASC'),
			array(
				'IBLOCK_ID' => $arParams['IBLOCK_ID'],
				'SECTION_ID' => $arSection['ID'],
				'ACTIVE' => 'Y'
			),
			false,
			array('nPageSize'=>$arParams['NEWS_COUNT']),
			array(
				'ID',
				'NAME',
				'PREVIEW_TEXT',
				'PROPERTY_TEXT_IN_HOME',
				'PROPERTY_COUNT',
				'PROPERTY_FRONTRIGHT',
				'PROPERTY_FRONTBIG'
			)
		);
		$bFirst=true;

		while($asRes = $obResult->Fetch()){
			$asRes['DETAIL_URL']='/'.$arParams['PREFIX'].$asRes['ID'].'/';

			if($bFirst){
				$sPreview = !empty($asRes['PROPERTY_TEXT_IN_HOME_VALUE']['TEXT']) ?
					$asRes['PROPERTY_TEXT_IN_HOME_VALUE']['TEXT'] :
					VString::truncate($asRes['PREVIEW_TEXT'],200);

				$asRes['PREVIEW_TEXT_B'] = trim($sPreview).'<span></span>';
				$asRes['IMG_B'] = CFile::GetFileArray($asRes['PROPERTY_FRONTBIG_VALUE']);
				$asRes['IMG_B']=$asRes['IMG_B']['SRC'];
				$bFirst=false;
			}

			//$asRes['PREVIEW_TEXT_S'] = \core\types\VString::truncate($asRes['PREVIEW_TEXT'], 30, '...');
			$asRes['IMG_S'] = CFile::GetFileArray($asRes['PROPERTY_FRONTRIGHT_VALUE']);
			$asRes['IMG_S']=$asRes['IMG_S']['SRC'];

			unset(
				$asRes['PROPERTY_SHORT_TEXT_VALUE'],
				$asRes['PROPERTY_SHORT_TEXT_VALUE_ID'],
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