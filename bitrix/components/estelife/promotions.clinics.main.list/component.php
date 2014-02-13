<?php
use core\database\VDatabase;
use core\types\VArray;
use core\types\VString;
use core\database\mysql\VFilter;
use geo\VGeo;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");
CModule::IncludeModule("estelife");

$obClinics = VDatabase::driver();
$obGet=new VArray($_GET);
$arNow=time();
$nCityId=0;


if (isset($arParams['COUNT']) && $arParams['COUNT']>0)
	$arCount = $arParams['COUNT'];

if (isset($arParams['PAGE_COUNT']) && $arParams['PAGE_COUNT']>0)
	$arPageCount = $arParams['PAGE_COUNT'];
else
	$arPageCount = 10;

if(!$obGet->blank('city'))
	$nCityId=intval($obGet->one('city'));
elseif (isset($arParams['CITY_ID']) && $arParams['CITY_ID']>0)
	$nCityId=intval($arParams['CITY_ID']);
elseif(isset($_COOKIE['estelife_city']))
	$nCityId=intval($_COOKIE['estelife_city']);

if($nCityId>0){
	//Получаем имя города по его ID
	$obCity=CIBlockElement::GetList(
		array("NAME"=>"ASC"),
		array("IBLOCK_ID"=>16, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "ID" => $nCityId),
		false,
		false,
		array("ID", "NAME", 'PROPERTY_CITY')
	);
	$arResult['city']=$obCity->Fetch();
	$arResult['city']['R_NAME']=(!empty($arResult['city']['PROPERTY_CITY_VALUE'])) ?
		$arResult['city']['PROPERTY_CITY_VALUE'] :
		$arResult['city']['NAME'];
}

if (!empty($arResult['city']['ID'])){
	$arResult['akzii_link'] = '/promotions/?city='.$arResult['city']['ID'];
	$arResult['clinics_link'] = '/clinics/?city='.$arResult['city']['ID'];
}else{
	$arResult['akzii_link'] = '/promotions/';
	$arResult['clinics_link'] = '/clinics/';
}

//Получение списка акций
$obQuery = $obClinics->createQuery();
$obQuery->builder()->from('estelife_akzii', 'ea');
$obQuery->builder()->sort($obQuery->builder()->_rand());
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ea','id')
	->_to('estelife_clinic_akzii','akzii_id','eca');
$obJoin->_left()
	->_from('eca','clinic_id')
	->_to('estelife_clinics','id','ec');
$obJoin->_left()
	->_from('ec','city_id')
	->_to('iblock_element','ID','ct')
	->_cond()->_eq('ct.IBLOCK_ID',16);
$obJoin->_left()
	->_from('ea', 'id')
	->_to('estelife_akzii_types', 'akzii_id', 'eat');
$obQuery->builder()
	->field('ea.id','id')
	->field('ea.name','name')
	->field('ea.end_date','end_date')
	->field('ea.base_old_price','old_price')
	->field('ea.base_new_price','new_price')
	->field('ea.base_sale','sale')
	->field('ea.small_photo','logo_id')
	->field('ea.view_type','view_type')
	->field('ct.CODE', 'city_code')
	->field('ea.small_photo','s_logo_id')
	->field('ec.name','clinic_name')
	->field('ec.id','clinic_id');
$obFilter=$obQuery->builder()->filter();
$obFilter->_gte('ea.end_date', $arNow);
$obFilter->_eq('ea.active', 1);

$obQuery->builder()->sort('ea.end_date', 'desc');



if(!empty($arResult['city'])){
	$obFilter->_eq('ec.city_id', $arResult['city']['ID']);
}

if(!empty($arCount))
	$obQuery->builder()->slice(0,$arCount);

$obQuery->builder()->group('ea.id');
$obResult=$obQuery->select();
$arResult['akzii']=array();

$obResult=$obResult->bxResult();
$nCount = $obResult->SelectedRowsCount();
$arResult['count'] = 'Найден'.VString::spellAmount($nCount, 'а,о,о'). ' '.$nCount.' акц'.VString::spellAmount($nCount, 'ия,ии,ий');
\bitrix\ERESULT::$DATA['count'] = $arResult['count'];
$obResult->NavStart($arPageCount);

while($arData=$obResult->Fetch()){
	$arData['time']=ceil(($arData['end_date']-$arNow)/(60*60*24));
	$arData['day']=\core\types\VString::spellAmount($arData['time'], 'день,дня,дней');
	$arData['link'] = '/pr'.$arData['id'].'/';

	if(!empty($arData['logo_id'])){
		$arData['img'] = CFile::GetFileArray($arData["logo_id"]);
		$arData['src']=$arData['img']['SRC'];
	}

	$arData['new_price'] = number_format(intval($arData['new_price']),0,'.',' ');
	$arData['old_price'] = number_format(intval($arData['old_price']),0,'.',' ');

	$arResult['akzii'][]=$arData;
}

$nCountElement = count($arResult['akzii']);
$arResult['active'] = 0;

if($nCountElement < 3){
	$arResult['active'] = 1;
	//Получение списка клиник
	$obQuery = $obClinics->createQuery();
	$obQuery->builder()->from('estelife_clinics', 'ec');
	$obQuery->builder()->sort($obQuery->builder()->_rand());
	$obJoin=$obQuery->builder()->join();
	$obJoin->_left()
		->_from('ec','city_id')
		->_to('iblock_element','ID','ct')
		->_cond()->_eq('ct.IBLOCK_ID',16);
	$obJoin->_left()
		->_from('ec','metro_id')
		->_to('iblock_element','ID','mt')
		->_cond()->_eq('mt.IBLOCK_ID',17);
	$obJoin->_left()
		->_from('ec', 'id')
		->_to('estelife_clinic_contacts', 'clinic_id', 'eccp')
		->_cond()->_eq('eccp.type', 'phone');
	$obJoin->_left()
		->_from('ec', 'id')
		->_to('estelife_clinic_contacts', 'clinic_id', 'eccw')
		->_cond()->_eq('eccw.type', 'web');
	$obJoin->_left()
		->_from('ec', 'id')
		->_to('estelife_clinic_services', 'clinic_id', 'ecs');
	$obQuery->builder()
		->field('mt.ID','metro_id')
		->field('mt.NAME','metro_name')
		->field('ct.ID','city_id')
		->field('ct.NAME','city_name')
		->field('ct.CODE','city_code')
		->field('ec.id','id')
		->field('ec.dop_text','dop_text')
		->field('ec.recomended', 'recomended')
		->field('ec.address','address')
		->field('ec.name','name')
		->field('ec.logo_id','logo_id')
		->field('eccp.value', 'phone')
		->field('eccw.value', 'web');

	$obQuery->builder()->slice(0,3);
	$obFilter = $obQuery->builder()->filter();
	$obFilter->_eq('ec.active', 1);
	$obFilter->_eq('ec.clinic_id', 0);
	$obFilter->_eq('ec.recomended', 1);


	if(!empty($arResult['city'])){
		$obFilter->_eq('ec.city_id', $arResult['city']['ID']);
	}

	$obQuery->builder()->group('ec.id');
	$obQuery->builder()->sort('ec.name', 'asc');
	$obResult = $obQuery->select();

	$obResult = $obResult->bxResult();
	$nCount = $obResult->SelectedRowsCount();
	$arResult['count_elements'] = 'Найден'.VString::spellAmount($nCount, 'а,о,о'). ' '.$nCount.' клиник'.VString::spellAmount($nCount, 'а,и,');
	\bitrix\ERESULT::$DATA['count'] = $arResult['count'];

	$obResult->NavStart($arPageCount);
	$arResult['clinics']=array();

	while($arData=$obResult->Fetch()){
		$arClinics[]=$arData['id'];
		$arData['name']=trim($arData['name']);
		$arData['link'] = '/cl'.$arData['id'].'/';

		if(!empty($arData['logo_id'])){
			$file=CFile::ShowImage($arData["logo_id"], 160, 80,'alt="'.$arData['name'].'"');
			$arData['logo']=$file;
		}

		if(!empty($arData['phone']))
			$arData['phone']=\core\types\VString::formatPhone($arData['phone']);

		if(!empty($arData['web']))
			$arData['web_short']=\core\types\VString::checkUrl($arData['web']);

		$arResult['clinics'][$arData['id']]=$arData;
	}

	if (!empty($arClinics)){
		//получаем услуги
		$obQuery=$obClinics->createQuery();
		$obQuery->builder()
			->from('estelife_clinic_services', 'ecs');
		$obJoin=$obQuery->builder()
			->join();

		$obJoin->_left()
			->_from('ecs','specialization_id')
			->_to('estelife_specializations','id','es');
		$obQuery->builder()
			->field('es.name','s_name')
			->field('es.id','s_id')
			->field('ecs.clinic_id');
		$obQuery->builder()
			->filter()
			->_in('ecs.clinic_id', $arClinics);

		$arClinicSpecialization = $obQuery->select()->all();
		$arSpecialization = array();

		if (!empty($arClinicSpecialization)){
			foreach ($arClinicSpecialization as $val){
				$arSpecialization[$val['clinic_id']][] = $val['s_name'];
			}
			foreach ($arSpecialization as $key=>$val){
				$val = array_unique($val);
				$arResult['clinics'][$key]['specialization'] =  mb_strtolower(implode(', ', $val), 'utf-8');
			}
		}
	}

}
$arResult['city']['T_NAME'] = ($arResult['active']==0 ? 'Акции ' : 'Клиники ').(($arResult['city']['ID']==359) ? 'Москвы' : (($arResult['city']['ID']==358) ? 'Санкт-Петербурга' : ''));

$sTemplate=$this->getTemplateName();
$obNav=new \bitrix\VNavigation($obResult,($sTemplate=='ajax'));
$arResult['nav']=$obNav->getNav();

$sPage=(isset($_GET['PAGEN_1']) && $_GET['PAGEN_1'] > 1) ?
	' '.\core\types\VString::spellAmount($_GET['PAGEN_1'],'страница,страницы,страниц') : '';

if (empty($arResult['city']['R_NAME']))
	$arResult['city']['R_NAME'] = '';
else
	$arResult['city']['R_NAME'] = ' в '.$arResult['city']['R_NAME'];

$this->IncludeComponentTemplate();


