<?php
use core\database\VDatabase;
use core\types\VArray;
use geo\VGeo;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

CModule::IncludeModule("iblock");
CModule::IncludeModule("estelife");

$obQuery = VDatabase::driver()->createQuery();
$obQuery->builder()
	->from('estelife_company_events', 'ece')
	->group('ct.ID')
	->field('ct.ID','ID')
	->field('ct.NAME','NAME');
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
$obJoin->_left()
	->_from('ec', 'id')
	->_to('estelife_company_geo', 'company_id', 'ecg');
$obJoin->_left()
	->_from('ecg', 'country_id')
	->_to('iblock_element', 'ID', 'ct')
	->_cond()->_eq('ct.IBLOCK_ID',15);
$obFilter = $obQuery->builder()->filter()
	->_ne('eet.type', 3);
$arCountries=$obQuery->select()->all();

$obCounties=new VArray($arCountries);
$obCounties->sortByPriorities(array(357),'ID');
$arResult['countries']=$obCounties->all();

$obGet=new VArray($_GET);

if (!$obGet->blank('country') || intval($_COOKIE['estelife_country'])>0){
	$nCountry = intval($obGet->one('country',$_COOKIE['estelife_country']));
	//получаем города по стране
	$arSelect = Array("ID", "NAME");
	$arFilter = Array("IBLOCK_ID"=>16, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "PROPERTY_COUNTRY" => $nCountry);
	$obCity= CIBlockElement::GetList(Array("NAME"=>"ASC"), $arFilter, false, false, $arSelect);

	while($res = $obCity->Fetch()) {
		$arResult['cities'][] = $res;
	}
}

$arResult['filter']=array(
	'country'=>intval($obGet->one('country',$_COOKIE['estelife_country'])),
	'city'=>intval($obGet->one('city',$_COOKIE['estelife_city'])),
	'name'=>strip_tags(trim($obGet->one('name',''))),
);

$arResult['count'] = \bitrix\ERESULT::$DATA['count'];

$arResult['empty']=false;
foreach ($arResult['filter'] as $val){
	if (($val=='' && $val==0) || $val=='all')
		continue;
	$arResult['empty']=true;
}

$this->IncludeComponentTemplate();