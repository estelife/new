#!/usr/bin/php
<?php
$sFileAddr=dirname(__FILE__);
$sFileAddr=str_replace('/cron','',$sFileAddr);
$_SERVER["DOCUMENT_ROOT"] = $sFileAddr;
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
@set_time_limit(0);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/estelife/prolog.php");

CModule::IncludeModule('estelife');
CModule::IncludeModule('iblock');

$nDate = date('Y-m-d', time());
$arUrl = array(
	'http://estelife.ru',
	'http://estelife.ru/apparatuses/',
	'http://estelife.ru/apparatuses-makers/',
	'http://estelife.ru/preparations/',
	'http://estelife.ru/preparations-makers/',
	'http://estelife.ru/trainings/',
	'http://estelife.ru/training-centers/',
	'http://estelife.ru/events/',
	'http://estelife.ru/sponsors/',
	'http://estelife.ru/promotions/',
	'http://estelife.ru/clinics/',
);

$obData = \core\database\VDatabase::driver();

//Получение url для аппаратов
$obQuery = $obData->createQuery();
$obQuery->builder()
	->from('estelife_apparatus')
	->field('id');
$arData = $obQuery->select()->all();
if (!empty($arData)){
	foreach ($arData as $val){
		$arUrl[] = 'http://estelife.ru/ap'.$val['id'].'/';
	}
}

//Получение url для производителей аппаратов
$obQuery = $obData->createQuery();
$obQuery->builder()->from('estelife_companies','ec');
$obJoin=$obQuery->builder()->join();
$obJoin->_right()
	->_from('ec', 'id')
	->_to('estelife_apparatus', 'company_id', 'ep');
$obJoin->_left()
	->_from('ec', 'id')
	->_to('estelife_company_types', 'company_id', 'ect')
	->_cond()->_or()
	->_eq('ect.type', 3)
	->_isNull('ect.type');
$obQuery->builder()->group('ec.id');
$obQuery->builder()->field('ec.id');
$arData = $obQuery->select()->all();
if (!empty($arData)){
	foreach ($arData as $val){
		$arUrl[] = 'http://estelife.ru/am'.$val['id'].'/';
	}
}

//Получение url для препаратов
$obQuery = $obData->createQuery();
$obQuery->builder()
	->from('estelife_pills')
	->field('id');
$arData = $obQuery->select()->all();
if (!empty($arData)){
	foreach ($arData as $val){
		$arUrl[] = 'http://estelife.ru/ps'.$val['id'].'/';
	}
}

//Получение url для производителей препаратов
$obQuery = $obData->createQuery();
$obQuery->builder()->from('estelife_companies','ec');
$obJoin=$obQuery->builder()->join();
$obJoin->_right()
	->_from('ec', 'id')
	->_to('estelife_pills', 'company_id', 'ep');
$obJoin->_left()
	->_from('ec', 'id')
	->_to('estelife_company_types', 'company_id', 'ect')
	->_cond()->_or()
	->_eq('ect.type', 3)
	->_isNull('ect.type');
$obQuery->builder()->group('ec.id');
$obQuery->builder()->field('ec.id');
$arData = $obQuery->select()->all();
if (!empty($arData)){
	foreach ($arData as $val){
		$arUrl[] = 'http://estelife.ru/pm'.$val['id'].'/';
	}
}

//Получение url для семинаров
$obQuery = $obData->createQuery();
$obQuery->builder()->from('estelife_events', 'ee');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ee', 'id')
	->_to('estelife_event_types', 'event_id', 'eet');
$obJoin->_left()
	->_from('ee', 'id')
	->_to('estelife_company_events', 'event_id', 'ece')
	->_cond()->_eq('ece.is_owner', 1);
$obJoin->_left()
	->_from('ece','company_id')
	->_to('estelife_companies','id','ec');
$obFilter=$obQuery->builder()->filter();
$obFilter->_eq('eet.type', 3);
$obQuery->builder()->group('ee.id');
$obQuery->builder()->field('ee.id');
$arData = $obQuery->select()->all();
if (!empty($arData)){
	foreach ($arData as $val){
		$arUrl[] = 'http://estelife.ru/tr'.$val['id'].'/';
	}
}

//Получение url для учебных центров
$obQuery = $obData->createQuery();
$obQuery->builder()->from('estelife_company_events', 'ece');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ece', 'event_id')
	->_to('estelife_events', 'id', 'ee');
$obJoin->_left()
	->_from('ee', 'id')
	->_to('estelife_event_types', 'event_id', 'eet');
$obJoin->_left()
	->_from('ece', 'company_id')
	->_to('estelife_companies', 'id', 'ec');
$obFilter = $obQuery->builder()->filter()
	->_eq('eet.type', 3)
	->_eq('ece.is_owner', 1);
$obQuery->builder()->group('ece.company_id');
$obQuery->builder()->field('ec.id');
$arData = $obQuery->select()->all();
if (!empty($arData)){
	foreach ($arData as $val){
		$arUrl[] = 'http://estelife.ru/tc'.$val['id'].'/';
	}
}

//Получение url для событий
$obQuery = $obData->createQuery();
$obQuery->builder()->from('estelife_events', 'ee');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ee', 'id')
	->_to('estelife_event_types', 'event_id', 'eet');
$obJoin->_left()
	->_from('ee', 'id')
	->_to('estelife_company_events', 'event_id', 'ece');
$obJoin->_left()
	->_from('ece','company_id')
	->_to('estelife_companies','id','ec');
$obFilter=$obQuery->builder()->filter();
$obFilter->_ne('eet.type', 3);
$obQuery->builder()->group('ee.id');
$obQuery->builder()->field('ee.id');
$arData = $obQuery->select()->all();
if (!empty($arData)){
	foreach ($arData as $val){
		$arUrl[] = 'http://estelife.ru/ev'.$val['id'].'/';
	}
}

//Получение url для организаторов
$obQuery = $obData->createQuery();
$obQuery->builder()->from('estelife_company_events', 'ece');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ece', 'event_id')
	->_to('estelife_events', 'id', 'ee');
$obJoin->_left()
	->_from('ee', 'id')
	->_to('estelife_event_types', 'event_id', 'eet');
$obJoin->_left()
	->_from('ece', 'company_id')
	->_to('estelife_companies', 'id', 'ec');
$obQuery->builder()->filter()
	->_ne('eet.type', 3);
$obQuery->builder()->group('ece.company_id');
$obQuery->builder()->field('ec.id');
$arData = $obQuery->select()->all();
if (!empty($arData)){
	foreach ($arData as $val){
		$arUrl[] = 'http://estelife.ru/sp'.$val['id'].'/';
	}
}

//Получение url для акций
$obQuery = $obData->createQuery();
$obQuery->builder()
	->from('estelife_akzii')
	->field('id');
$arData = $obQuery->select()->all();
if (!empty($arData)){
	foreach ($arData as $val){
		$arUrl[] = 'http://estelife.ru/pr'.$val['id'].'/';
	}
}

//Получение url для клиник
$obQuery = $obData->createQuery();
$obQuery->builder()
	->from('estelife_clinics')
	->field('id')
	->filter()
	->_eq('clinic_id',0);
$arData = $obQuery
	->select()
	->all();

if (!empty($arData)){
	foreach ($arData as $val){
		$arUrl[] = 'http://estelife.ru/cl'.$val['id'].'/';
	}
}

/*РАБОТА С ИНФОБЛОКАМИ*/

//Список подкастов
$obRes = CIBlockSection::GetList(
	array('created'=>'desc'),
	array('ACTIVE'=>'Y', 'IBLOCK_ID'=>14, 'SECTION_ID'=>'208'),
	false,
	array('CODE', 'NAME')
);

while ($res=$obRes->Fetch()){
	$arUrl[] = 'http://estelife.ru/podcast/'.$res['CODE'].'/';
}

//Список статей для подкастов
$obRes = CIBlockElement::GetList(
	array('SORT'=>'ASC'),
	array('ACTIVE'=>'Y', 'IBLOCK_ID'=>14, 'SECTION_ID'=>'208','INCLUDE_SUBSECTIONS' => "Y"),
	false,
	false
);

while ($res=$obRes->Fetch()){
	$arUrl[] = 'http://estelife.ru/pt'.$res['ID'].'/';
}

//Список новостей
$obRes = CIBlockSection::GetList(
	array('created'=>'desc'),
	array('ACTIVE'=>'Y', 'IBLOCK_ID'=>3),
	false,
	array('CODE', 'NAME')
);

while ($res=$obRes->Fetch()){
	$arUrl[] = 'http://estelife.ru/novosti/'.$res['CODE'].'/';
}

//Список статей для новостей
$obRes = CIBlockElement::GetList(
	array('SORT'=>'ASC'),
	array('ACTIVE'=>'Y', 'IBLOCK_ID'=>3),
	false,
	false
);

while ($res=$obRes->Fetch()){
	$arUrl[] = 'http://estelife.ru/ns'.$res['ID'].'/';
}

//Список cтатей
$obRes = CIBlockSection::GetList(
	array('created'=>'desc'),
	array('ACTIVE'=>'Y', 'IBLOCK_ID'=>14, 'SECTION_ID'=>'193'),
	false,
	array('CODE', 'NAME')
);

while ($res=$obRes->Fetch()){
	$arUrl[] = 'http://estelife.ru/articles/'.$res['CODE'].'/';
}

//Список статей для cтатей
$obRes = CIBlockElement::GetList(
	array('SORT'=>'ASC'),
	array('ACTIVE'=>'Y', 'IBLOCK_ID'=>14, 'SECTION_ID'=>'193', 'INCLUDE_SUBSECTIONS' => "Y"),
	false,
	false
);

while ($res=$obRes->Fetch()){
	$arUrl[] = 'http://estelife.ru/ar'.$res['ID'].'/';
}

$sText = '<?xml version="1.0" encoding="UTF-8"?>';
$sText.= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
if (!empty($arUrl)){
	foreach ($arUrl as $key=>$val){
		$sText.= '<url>';
			$sText.= ' <loc>'.$val.'</loc>';
			$sText.= '<lastmod>'.$nDate.'</lastmod>';
			if ($key == 0)
				$sText.= '<changefreq>daily</changefreq>';
			else
				$sText.= '<changefreq>weekly</changefreq>';
			if ($key == 0)
				$sText.= '<priority>1</priority>';
			else
				$sText.= '<priority>0.8</priority>';
		$sText.= '</url>';
	}
}
$sText.= '</urlset>';

if (strlen($sText)>0)
	file_put_contents($sFileAddr.'/sitemap.xml', $sText);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");