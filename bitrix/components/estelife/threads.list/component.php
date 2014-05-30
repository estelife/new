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

//Получение списка препаратов
$obQuery = $obPills->createQuery();
$obQuery->builder()->from('estelife_threads', 'ep');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ep', 'id')
	->_to('estelife_threads_type', 'thread_id', 'ept');
$obJoin->_left()
	->_from('ep', 'company_id')
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
	->field('ep.id','id')
	->field('ep.name','name')
	->field('ep.type_id', 'type_id')
	->field('ep.translit','translit')
	->field('ep.logo_id','logo_id')
	->field('ep.preview_text', 'preview_text')
	->field('ec.name','company_name')
	->field('ec.id','company_id')
	->field('ec.translit','company_translit')
	->field('ec.id','company_id')
	->field('ect.name','type_company_name')
	->field('ect.id','type_company_id')
	->field('ect.translit','type_company_translit')
	->field('ect.id','type_company_id');

$obQuery->builder()->group('ep.id');
$obQuery->builder()->sort('ep.name', 'asc');

if ($sComponent=='list'){

	$obFilter = $obQuery->builder()->filter();

	$session = new \filters\decorators\VThreads();
	$arFilterParams = $session->getParams();

	if(!empty($arFilterParams['country']) && $arFilterParams['country'] !='all'){
		$obFilter->_or()->_eq('ecg.country_id', intval($arFilterParams['country']));
		$obFilter->_or()->_eq('ectg.country_id', intval($arFilterParams['country']));
	}else if(!$obGet->blank('country') && $obGet->one('country')!=='all'){
		$obFilter->_or()->_eq('ecg.country_id', intval($obGet->one('country')));
		$obFilter->_or()->_eq('ectg.country_id', intval($obGet->one('country')));
	}

	if(!empty($arFilterParams['name'])){
		$obFilter->_like('ep.name',$arFilterParams['name'],VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);
	}

	if(!empty($arFilterParams['company_name'])){
		$obFilter->_like('ec.name',$arFilterParams['company_name'],VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);
	}

	if(!empty($arFilterParams['type'])){
		$obFilter->_eq('ept.type_id', intval($arFilterParams['type']));
	}


	$obResult = $obQuery->select();
	$obResult = $obResult->bxResult();
	$nCount = $obResult->SelectedRowsCount();

	//Получение типов аппаратов
	$obQuery = $obPills->createQuery();
	$obQuery
		->builder()
		->from('estelife_threads_typename');
	$arTypes=$obQuery->select()->all();
	foreach ($arTypes as $val){
		$arTypes[$val['id']]=$val;
	}

	$arResult['title']='Нити';
	$sPrefix='th';
	$arResult['count'] = 'Найден'.VString::spellAmount($nCount, ',о,о'). ' '.$nCount.' нит'.VString::spellAmount($nCount, 'ь,и,ей');
	$arSEOTitle = 'Список и база данных нитей в эстетической медицине';
	$arSEODescription = 'Большая база данных нитей для процедур и различных видов терапий в эстетической медицине. Мы собрали для Вас всю информацию';

	\bitrix\ERESULT::$DATA['count'] = $arResult['count'];

	$obResult->NavStart($arPageCount);
	$arResult['pills'] = array();
	$arDescription=array();

	while($arData=$obResult->Fetch()){
		$arData['link'] = '/'.$sPrefix.$arData['id'].'/';
		$arData['preview_text'] = \core\types\VString::truncate(htmlspecialchars_decode($arData['preview_text'],ENT_NOQUOTES), 250, '...');

		if(!empty($arData['logo_id'])){
			$file=CFile::ShowImage($arData["logo_id"], 180, 180,'alt="'.$arData['name'].'"');
			$arData['logo']=$file;
		}

		if (!empty($arData['type_country_name'])){
			$arData['country_name'] = $arData['type_country_name'];
			$arData['country_id'] = $arData['type_country_id'];
		}
		unset($arData['type_country_name']);
		unset($arData['type_country_id']);

		if (!empty($arData['type_company_name'])){
			$arData['company_name'] = $arData['type_company_name'];
			$arData['company_translit'] = $arData['type_company_translit'];
		}
		unset($arData['type_company_name']);
		unset($arData['type_company_translit']);

		if (!empty($arData['type_company_id'])){
			$arData['company_id'] = $arData['type_company_id'];
		}
		unset($arData['type_company_id']);

		$arData['company_link'] = '/pm'.$arData['company_id'].'/';

		$arResult['pills'][]=$arData;
		$arDescription[]=mb_strtolower($arData['name']);
	}



	if (isset($_GET['PAGEN_1']) && intval($_GET['PAGEN_1'])>0){
		$_GET['PAGEN_1'] = intval($_GET['PAGEN_1']);
		$arSEOTitle.=' - '.$_GET['PAGEN_1'].' страница';
		$arSEONav=' - '.$_GET['PAGEN_1'].' страница';
	}

	$APPLICATION->SetPageProperty("title", $arSEOTitle);
	$APPLICATION->SetPageProperty("description", VString::truncate($arSEODescription,'145', '').$arSEONav);
	$APPLICATION->SetPageProperty("keywords", $arResult['title'].", ".$arSEODescription);

	$sTemplate=$this->getTemplateName();
	$obNav=new \bitrix\VNavigation($obResult,($sTemplate=='ajax'));
	$arResult['nav']=$obNav->getNav();

}elseif ($sComponent=='similar_list'){
	$obQuery->builder()->filter()
		->_eq('ep.company_id', $nMaker)
		->_ne('ep.id', $nPrepId);
	$obQuery->builder()->slice(0,3);
	$arProductions = $obQuery->select()->all();

	foreach ($arProductions as $val){
		$val['img'] = CFile::ShowImage($val['logo_id'],150, 140, 'alt='.$val['name']);
		$val['preview_text'] = \core\types\VString::truncate($val['preview_text'], 100, '...');
		$sPrefix='th';

		$val['link'] = '/'.$sPrefix.$val['id'].'/';
		$arResult['similar_pills']['production'][] = $val;
	}
	$arResult['similar_pills']['company_link'] = $sMakerLink;
}elseif ($sComponent=='maker_list'){
	$obQuery->builder()->filter()
		->_eq('ep.company_id', $nMaker);
	$arProductions = $obQuery->select()->all();

	foreach ($arProductions as $val){
		$val['img'] = CFile::ShowImage($val['logo_id'],180, 180, 'alt='.$val['name']);
		$val['preview_text'] = strip_tags(html_entity_decode($val['preview_text'],ENT_QUOTES,'utf-8'));
		$val['preview_text'] = \core\types\VString::truncate($val['preview_text'], 90, '...');
		$sPrefix='th';
		$val['link'] = '/'.$sPrefix.$val['id'].'/';
		$arResult['company']['production'][] = $val;
	}
}

$this->IncludeComponentTemplate();