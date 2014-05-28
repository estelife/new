<?php
use core\database\mysql\VFilter;
use core\database\VDatabase;
use core\types\VArray;
use core\types\VString;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

CModule::IncludeModule("iblock");
CModule::IncludeModule("estelife");

$obPills = VDatabase::driver();
$obGet=new VArray($_GET);

if (isset($arParams['PAGE_COUNT']) && $arParams['PAGE_COUNT']>0)
	$arPageCount = $arParams['PAGE_COUNT'];
else
	$arPageCount = 10;

if (isset($arParams['MAKER']) && $arParams['MAKER']>0)
	$nMaker=intval($arParams['MAKER']);
else
	$nMaker=0;

if (isset($arParams['MAKER_LINK']) && !empty($arParams['COMPONENT']))
	$sMakerLink=trim(strip_tags($arParams['MAKER_LINK']));
else
	$sMakerLink=0;

if (isset($arParams['COMPONENT']) && !empty($arParams['COMPONENT']))
	$arParams['COMPONENT']=$sComponent=trim(strip_tags($arParams['COMPONENT']));
else
	$arParams['COMPONENT']=$sComponent='';

if (isset($arParams['PREP_ID']) && $arParams['PREP_ID']>0)
	$nPrepId=intval($arParams['PREP_ID']);

//Получение списка аппаратов
$obQuery = $obPills->createQuery();
$obQuery->builder()->from('estelife_apparatus', 'ap');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ap', 'id')
	->_to('estelife_apparatus_type', 'apparatus_id', 'apt');
$obJoin->_left()
	->_from('ap', 'company_id')
	->_to('estelife_companies', 'id', 'ec');
$obJoin->_left()
	->_from('ec', 'id')
	->_to('estelife_company_geo', 'company_id', 'ecg');
$obJoin->_left()
	->_from('ecg','country_id')
	->_to('iblock_element','ID','ct')
	->_cond()->_eq('ct.IBLOCK_ID',15);
$obJoin->_left()
	->_from('ec', 'id')
	->_to('estelife_company_types', 'company_id', 'ect')
	->_cond()->_eq('type', 3);
$obJoin->_left()
	->_from('ect', 'id')
	->_to('estelife_company_type_geo', 'company_id', 'ectg');
$obJoin->_left()
	->_from('ectg','country_id')
	->_to('iblock_element','ID','cttype')
	->_cond()->_eq('ct.IBLOCK_ID',15);
$obQuery->builder()
	->field('ct.ID','country_id')
	->field('ct.NAME','country_name')
	->field('cttype.ID','type_country_id')
	->field('cttype.NAME','type_country_name')
	->field('ap.id','id')
	->field('ap.name','name')
	->field('ap.translit','translit')
	->field('ap.logo_id','logo_id')
	->field('ap.preview_text', 'preview_text')
	->field('ec.name','company_name')
	->field('ec.id','company_id')
	->field('ec.translit','company_translit')
	->field('ec.id','company_id')
	->field('ect.name','type_company_name')
	->field('ect.id','type_company_id')
	->field('ect.translit','type_company_translit')
	->field('ect.id','type_company_id');
$obQuery->builder()->group('ap.id');
$obQuery->builder()->sort('ap.name', 'asc');

if ($sComponent=='list'){
	$obFilter = $obQuery->builder()->filter();

	$session = new \filters\decorators\VApparatuses();
	$arFilterParams = $session->getParams();

	if(!empty($arFilterParams['country']) && $arFilterParams['country'] !='all'){
		$obFilter->_or()->_eq('ecg.country_id', intval($arFilterParams['country']));
		$obFilter->_or()->_eq('ectg.country_id', intval($arFilterParams['country']));
	}

	if(!empty($arFilterParams['name'])){
		$obFilter->_like('ap.name',$arFilterParams['name'],VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);
	}

	if(!empty($arFilterParams['company_name'])){
		$obFilter->_like('ec.name',$arFilterParams['company_name'],VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);
	}

	if(!empty($arFilterParams['type'])){
		$obFilter->_eq('apt.type_id', intval($arFilterParams['type']));
	}

	$obResult = $obQuery->select();
	$obResult = $obResult->bxResult();
	$nCount = $obResult->SelectedRowsCount();
	$arResult['count'] = 'Найден'.VString::spellAmount($nCount, ',о,о'). ' '.$nCount.' аппарат'.VString::spellAmount($nCount, ',а,ов');
	\bitrix\ERESULT::$DATA['count'] = $arResult['count'];

	$obResult->NavStart($arPageCount);
	$arResult['apps'] = array();

	$i = 0;
	$arResult['apps'] = array();
	while($arData=$obResult->Fetch()){
		$arData['link'] = '/ap'.$arData['id'].'/';
		$arData['preview_text'] = \core\types\VString::truncate(htmlspecialchars_decode($arData['preview_text'],ENT_NOQUOTES), 250, '...');

		if(!empty($arData['logo_id'])){
			$file=CFile::ShowImage($arData["logo_id"], 180, 180,'alt="'.$arData['name'].'"');
			$arData['logo']=$file;
		}

		if (!empty($arData['type_country_name'])){
			$arData['country_name'] = $arData['type_country_name'];
			$arData['country_id'] = $arData['type_country_id'];
		}

		if (!empty($arData['type_company_name'])){
			$arData['company_name'] = $arData['type_company_name'];
			$arData['company_translit'] = $arData['type_company_translit'];
		}

		if (!empty($arData['type_company_id'])){
			$arData['company_id'] = $arData['type_company_id'];
		}
		unset(
			$arData['type_company_id'],
			$arData['type_company_translit'],
			$arData['type_company_name'],
			$arData['type_country_id'],
			$arData['type_country_name']
		);

		$arData['company_link'] = '/am'.$arData['company_id'].'/';

		$arResult['apps'][]=$arData;

		if ($i<=5){
			$arDescription[]= mb_strtolower($arData['name']);
		}
		$i++;
	}

	//Получение типов аппаратов
	$obQuery = $obPills->createQuery();
	$obQuery
		->builder()
		->from('estelife_apparatus_typename')
		->filter()
		->_eq('type', 1);
	$arTypes=$obQuery->select()->all();
	foreach ($arTypes as $val){
		$arTypes[$val['id']]=$val;
	}

	if (empty($_GET['type'])){
		$arSEOTitle = 'Список и база данных аппартов в эстетической медицине.';
		$arSEODescription = 'Огромная база данных по аппаратам для всех процедур и видов терапий в эстетической медицине. Подробная информация только у нас.';
	}else{
		$arSEOTitle = $arTypes[$_GET['type']]['name'].' - все аппараты в нашей базе данных.';
		$arSEODescription = 'Вся информация по аппаратам для процедуры '.$arTypes[$_GET['type']]['name'].'. Весь список с подробным описанием в нашей базе данных.';
	}

	if (isset($_GET['PAGEN_1']) && intval($_GET['PAGEN_1'])>0){
		$_GET['PAGEN_1'] = intval($_GET['PAGEN_1']);
		$arSEOTitle.=' - '.$_GET['PAGEN_1'].' страница';
		$arSEODescription.=' - '.$_GET['PAGEN_1'].' страница';
	}


	$APPLICATION->SetPageProperty("title", $arSEOTitle);
	$APPLICATION->SetPageProperty("description", VString::truncate($arSEODescription, 160, ''));
	$APPLICATION->SetPageProperty("keywords", "Estelife, Аппараты, ". $arSEODescription);

	$sTemplate=$this->getTemplateName();
	$obNav=new \bitrix\VNavigation($obResult,($sTemplate=='ajax'));
	$arResult['nav']=$obNav->getNav();
}elseif ($sComponent=='similar_list'){
	$obQuery->builder()->filter()
		->_eq('ap.company_id',$nMaker)
		->_ne('ap.id', $nPrepId);
	$obQuery->builder()->slice(0,3);
	$arProductions = $obQuery->select()->all();

	foreach ($arProductions as $val){
		$val['img'] = CFile::ShowImage($val['logo_id'],150, 150, 'alt='.$val['name']);
		$val['link'] = '/ap'.$val['id'].'/';
		$val['preview_text'] = \core\types\VString::truncate($val['preview_text'], 90, '...');
		$arResult['similar_apps']['production'][] = $val;
	}
	$arResult['similar_apps']['company_link'] = $sMakerLink;
}elseif ($sComponent=='maker_list'){
	$obQuery->builder()->filter()
		->_eq('ap.company_id', $nMaker);
	$arProductions = $obQuery->select()->all();

	foreach ($arProductions as $val){
		$val['img'] = CFile::ShowImage($val['logo_id'],150, 150, 'alt='.$val['name']);
		$val['preview_text'] = \core\types\VString::truncate($val['preview_text'], 90, '...');
		$val['link'] = '/ap'.$val['id'].'/';
		$arResult['company']['production'][] = $val;
	}
}


$this->IncludeComponentTemplate();