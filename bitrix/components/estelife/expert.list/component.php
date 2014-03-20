<?php
use core\database\VDatabase;
use core\exceptions\VException;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

try{

	if (empty($arParams['IBLOCK_ID']))
		throw new VException("Ошибка в задании ID ИБ");

	CModule::IncludeModule("iblock");
	CModule::IncludeModule("estelife");

	$obIblock = VDatabase::driver();
	$obQuery = $obIblock->createQuery();
	$obJoin = $obQuery->builder()
		->sort('ie.DATE_CREATE', 'desc')
		->field('ie.ID', 'ID')
		->field('ie.NAME', 'NAME')
		->field('ie.ACTIVE_FROM', 'ACTIVE_FROM')
		->field('ie.CODE', 'CODE')
		->field('iepr.VALUE', 'PREVIEW_TEXT')
		->field('iepi.VALUE', 'IMG')
		->field('iepp.VALUE', 'PROFESSION')
		->field('iepa.VALUE', 'AUTHOR')
		->from('iblock_element', 'ie')
		->slice(0, $arParams['NEWS_COUNT'])
		->join();
	$obJoin->_left()
		->_from('ie','ID')
		->_to('iblock_element_property','IBLOCK_ELEMENT_ID','iepi')
		->_cond()->_eq('iepi.IBLOCK_PROPERTY_ID', $arParams['IMG']);
	$obJoin->_left()
		->_from('ie','ID')
		->_to('iblock_element_property','IBLOCK_ELEMENT_ID','iepp')
		->_cond()->_eq('iepp.IBLOCK_PROPERTY_ID', $arParams['PROFESSION']);
	$obJoin->_left()
		->_from('ie','ID')
		->_to('iblock_element_property','IBLOCK_ELEMENT_ID','iepa')
		->_cond()->_eq('iepa.IBLOCK_PROPERTY_ID', $arParams['AUTOR']);
	$obJoin->_left()
		->_from('ie','ID')
		->_to('iblock_element_property','IBLOCK_ELEMENT_ID','iepr')
		->_cond()->_eq('iepr.IBLOCK_PROPERTY_ID', $arParams['PREVIEW']);
	$obQuery->builder()->filter()
		->_eq('IBLOCK_ID', $arParams['IBLOCK_ID'])
		->_eq('ACTIVE', 'Y');

	$arElements = $obQuery->select()->all();
	$arResult['count'] = count($arElements);

	if ($arResult['count']) {
		foreach ($arElements as $val){
			$val['PREVIEW_TEXT'] = unserialize($val['PREVIEW_TEXT']);
			if (!empty($val['PREVIEW_TEXT']['TEXT'])){
				$val['PREVIEW_TEXT'] = $val['PREVIEW_TEXT']['TEXT'];
			}else{
				$val['PREVIEW_TEXT']='';
			}
			$val['IMG'] = CFile::GetFileArray($val['IMG']);
			$val['IMG']=$val['IMG']['SRC'];
			//$val['ACTIVE_FROM'] = date('d.m.Y',strtotime($val['ACTIVE_FROM']));

			unset($val['CODE'],$val['ACTIVE_FROM']);
			$arResult['iblock'][] = $val;
		}
	}
}catch(VException $e){
	echo $e->getMessage(), "\n";
}
$this->IncludeComponentTemplate();