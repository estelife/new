<?php
use core\database\VDatabase;
use core\exceptions\VException;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

try{
	if (empty($arParams['SECTIONS_ID']) || empty($arParams['SECTIONS_NAME']) || count($arParams['SECTIONS_ID']) != count($arParams['SECTIONS_NAME']))
		throw new VException("Ошибка в задании параметров");

	if (empty($arParams['IBLOCK_ID']))
		throw new VException("Ошибка в задании ID ИБ");

	CModule::IncludeModule("iblock");
	CModule::IncludeModule("estelife");

	$arResult['first'] = $arParams['SECTIONS_ID'][0];
	foreach ($arParams['SECTIONS_NAME'] as $key=>$val){
		$arResult['SECTIONS_NAME'][$arParams['SECTIONS_ID'][$key]] = $val;
	}

	$arResult['first'] = key($arResult['SECTIONS_NAME']);

	$obIblock = VDatabase::driver();
	$obQuery = $obIblock->createQuery();
	$obQuery->builder()->from('iblock_element', 'ie');
	$obJoin = $obQuery->builder()->join();
	$obJoin->_left()
		->_from('ie','IBLOCK_SECTION_ID')
		->_to('iblock_section','ID','ic');
	$obJoin->_left()
		->_from('ie','ID')
		->_to('iblock_element_property','IBLOCK_ELEMENT_ID','iep')
		->_cond()->_eq('iep.IBLOCK_PROPERTY_ID', $arParams['IMG_FIELD']);
	$obQuery->builder()
		->field('ie.ID', 'ID')
		->field('ie.NAME', 'NAME')
		->field('ie.PREVIEW_TEXT', 'PREVIEW_TEXT')
		->field('ie.ACTIVE_FROM', 'ACTIVE_FROM')
		->field('ie.CODE', 'CODE')
		->field('ie.IBLOCK_SECTION_ID', 'SECTION_ID')
		->field('ic.NAME', 'SECTION_NAME')
		->field('ic.CODE', 'SECTION_CODE')
		->field('iep.VALUE', 'VALUE');
	$obQuery->builder()->slice(0,4);
	$obQuery->builder()->sort('ie.ACTIVE_FROM', 'DESC');
	foreach ($arParams['SECTIONS_ID'] as $val){
		$obQuery->builder()->filter()
			->_eq('ie.IBLOCK_ID', $arParams['IBLOCK_ID'])
			->_eq('ie.IBLOCK_SECTION_ID', $val)
			->_eq('ie.ACTIVE', 'Y');
		$obQuery->builder()->union();
	}

	$arElements = $obQuery->select()->all();

	if (!empty($arElements)){
		foreach ($arElements as $val){
			$val['DETAIL_URL'] = '/'.$arParams['PREFIX'].$val['ID'].'/';
			$val['SECTION_URL'] = '/'.$arParams['MAIN_URL'].'/'.$val['SECTION_CODE'].'/';
			$val['IMG'] = CFile::GetFileArray($val['VALUE']);
			$val['IMG']=$val['IMG']['SRC'];
			$val['PREVIEW_TEXT'] = \core\types\VString::truncate($val['PREVIEW_TEXT'], 80, '...');
			$val['ACTIVE_FROM'] = date('d.m.Y',strtotime($val['ACTIVE_FROM']));

			$nSectionId=$val['SECTION_ID'];
			$arResult['iblock'][$nSectionId]['section'] = $val['SECTION_URL'];

			unset(
				$val['CODE'],
				$val['ID'],
				$val['SECTION_CODE'],
				$val['SECTION_ID'],
				$val['SECTION_NAME'],
				$val['SECTION_URL'],
				$val['VALUE']
			);

			$arResult['iblock'][$nSectionId]['articles'][] = $val;
		}
	}

	$arResult['first_section'] = $arResult['iblock'][$arResult['first']]['section'];

}catch(VException $e){
	echo $e->getMessage(), "\n";
}
$this->IncludeComponentTemplate();