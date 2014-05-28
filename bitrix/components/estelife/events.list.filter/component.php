<?php
use core\database\VDatabase;
use core\types\VArray;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

CModule::IncludeModule("iblock");
CModule::IncludeModule("estelife");

$obClinics = VDatabase::driver();

//Получение списка стран
$obQuery=VDatabase::driver()
	->createQuery();

$obQuery->builder()
	->from('estelife_events','ee')
	->field('ct.NAME','NAME')
	->field('ct.ID','ID')
	->group('ct.ID')
	->sort('ct.NAME','asc')
	->join()
	->_left()
	->_from('ee','country_id')
	->_to('iblock_element','ID','ct')
	->_cond()->_eq('ct.IBLOCK_ID',15);

$obQuery->builder()
	->filter()
	->_ne('ee.country_id',0);

$arCountries=$obQuery
	->select()
	->all();

$obCounties=new VArray($arCountries);
$obCounties->sortByPriorities(array(357),'ID');
$arResult['countries']=$obCounties->all();
$obGet=new VArray($_GET);

$session = new \filters\decorators\VEvents();
$arFilterParams = $session->getParams();


if(!$obGet->blank('country')){
	$nCountry=intval($obGet->one('country',0));
}else{
	$nCountry=intval($arFilterParams['country']);
}
	//получаем города по стране
	$obCity=CIBlockElement::GetList(
		array("NAME"=>"ASC"),
		array("IBLOCK_ID"=>16, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "PROPERTY_COUNTRY" => $nCountry),
		false,
		false,
		array("ID", "NAME")
	);

	while($res = $obCity->Fetch()) {
		$arResult['cities'][] = $res;
	}

$arDirections=array();
$arTypes=array();

//foreach($arFilterParams['direction'] as &$nDirection)
//	settype($nDirection, 'int');
//
//foreach($arFilterParams['type'] as &$nType)
//	settype($nType, 'int');

/*$arResult['filter']=array(
	'country'=>intval($obGet->one('country',0)),
	'city'=>intval($obGet->one('city',0)),
	'direction'=>$arDirections,
	'type'=>$arTypes,
	'date_from'=>$obGet->one('date_from', date('d.m.y',time())),
	'date_to'=>$obGet->one('date_to',''),
	'name'=>strip_tags(trim($obGet->one('name',''))),
);*/

$arResult['filter'] = $arFilterParams;

$arResult['count']=\bitrix\ERESULT::$DATA['count'];
$arResult['empty']=false;

foreach ($arResult['filter'] as $key=>$val){
	if (($val=='' && $val==0) || $val=='all')
		continue;
	if ($key=='date_from' && $val==date('d.m.y',time()))
		continue;
	$arResult['empty']=true;
}

$this->IncludeComponentTemplate();