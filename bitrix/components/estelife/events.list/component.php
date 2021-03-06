<?php
use core\database\mysql\VFilter;
use core\database\VDatabase;
use core\types\VArray;
use core\types\VString;
use geo\VGeo;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

CModule::IncludeModule("iblock");
CModule::IncludeModule("estelife");

$obEvents = VDatabase::driver();
$obGet=new VArray($_GET);


if (isset($arParams['PAGE_COUNT']) && $arParams['PAGE_COUNT']>0)
	$arPageCount = $arParams['PAGE_COUNT'];
else
	$arPageCount = 10;

//if($obGet->blank('city') && $obGet->blank('country')){
//	if(isset($_COOKIE['estelife_city']))
//		$arResult['city'] = VGeo::getInstance()->getGeo();
//
//	$arResult['country']['COUNTRY_ID'] = $arResult['city']['COUNTRY_ID'];
//}else{
if(!$obGet->blank('city'))
	$arResult['city']['ID'] = intval($obGet->one('city'));

if(!$obGet->blank('country'))
	$arResult['country']['COUNTRY_ID'] = intval($obGet->one('country'));
//}

$arResult['events'] = array();

//Получение списка событий
$obQuery=$obEvents->createQuery();
$obQuery->builder()->from('estelife_events', 'ee');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ee', 'id')
	->_to('estelife_event_types', 'event_id', 'eet');
$obJoin->_left()
	->_from('ee','city_id')
	->_to('iblock_element','ID','ct')
	->_cond()->_eq('ct.IBLOCK_ID',16);
$obJoin->_left()
	->_from('ee','country_id')
	->_to('iblock_element','ID','cty')
	->_cond()->_eq('cty.IBLOCK_ID',15);
$obJoin->_left()
	->_from('ee', 'id')
	->_to('estelife_event_directions', 'event_id', 'eed');
$obJoin->_left()
	->_from('ee','id')
	->_to('estelife_event_contacts','event_id','ee_contacts_w')
	->_cond()->_eq('ee_contacts_w.type','web');
$obJoin->_left()
	->_from('ee', 'id')
	->_to('estelife_calendar', 'event_id', 'ecal');

$obQuery->builder()
	->field('ee.id','id')
	->field('ee.preview_text', 'preview_text')
	->field('ee.short_name','name')
	->field('ee.full_name','full_name')
	->field('ee.city_id','city_id')
	->field('ee.translit','translit')
	->field('ee.address','address')
	->field('ct.NAME','city_name')
	->field('cty.NAME','country_name')
	->field('cty.ID','country_id')
	->field('ee.web','web')
	->field('ee.logo_id','logo_id');

$obFilter=$obQuery->builder()->filter();
$obFilter->_ne('eet.type', 3);

$session = new \filters\decorators\VEvents();
$arFilterParams = $session->getParams();

if(!empty($arFilterParams['city']) && $arFilterParams['city'] !='all'){
	$obFilter->_eq('ee.city_id', $arFilterParams['city']);
}

if(!empty($arFilterParams['country'])&& $arFilterParams['country'] !='all'){
	$obFilter->_eq('ee.country_id', $arFilterParams['country']);
}

if(!empty($arFilterParams['name']))
	$obFilter->_like('ee.short_name',$arFilterParams['name'],VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);

if(!empty($arFilterParams['direction'])){
	if(is_array($arFilterParams['direction'])){
		foreach($arFilterParams['direction'] as $nKey=>$nValue){
			$nValue=intval($nValue);
			if($nValue<=0)
				unset($arFilterParams['direction'][$nKey]);
		}
		$obFilter->_in('eed.type', $arFilterParams['direction']);
	}else{
		$mDirections=intval($arFilterParams['direction']);
		$obFilter->_eq('eed.type', $arFilterParams['direction']);
	}
}

if($arFilterParams['type']){

	if(is_array($arFilterParams['type'])){
		foreach($arFilterParams['type'] as $nKey=>$nValue){
			$nValue=intval($nValue);
			if($nValue<=0)
				unset($arFilterParams['type'][$nKey]);
		}
		$obFilter->_in('eet.type', $arFilterParams['type']);
	}else{
		$mDirections=intval($arFilterParams['type']);
		$obFilter->_eq('eet.type', $arFilterParams['type']);
	}

}
 if(!empty($arFilterParams['date_from'])){
	$nDateFrom=preg_replace('/^(\d{2}).(\d{2}).(\d{2})$/','$1.$2.20$3 ',$arFilterParams['date_from']);
	$nDateFrom=\core\types\VDate::dateToTime($nDateFrom.' 00:00');
}

if(!empty($arFilterParams['date_to'])){
	$nDateTo = preg_replace('/^(\d{2}).(\d{2}).(\d{2})$/','$1.$2.20$3 ',$arFilterParams['date_to']);
	$nDateTo = \core\types\VDate::dateToTime($nDateTo. ' 23:59');
}else{
	$nDateTo = false;
}

$obFilter->_gte('ecal.date',$nDateFrom);

if ($nDateTo){
	$obFilter->_lte('ecal.date',$nDateTo);
}

$obQuery->builder()->sort('ecal.date', 'asc');
$obQuery->builder()->group('ee.id');
$obResult = $obQuery->select();

$obResult = $obResult->bxResult();
$nCount = $obResult->SelectedRowsCount();
$arResult['count'] = 'Найден'.VString::spellAmount($nCount, 'о,о,о'). ' '.$nCount.' событ'.VString::spellAmount($nCount, 'ие,ия,ий');
\bitrix\ERESULT::$DATA['count'] = $arResult['count'];
$obResult->NavStart($arPageCount);

$arResult['training'] = array();
$arIds = array();

$i=0;
while($arData=$obResult->Fetch()){
	$arIds[] = $arData['id'];

	$arData['link'] = '/ev'.$arData['id'].'/';

	if(!empty($arData['web']))
		$arData['web_short']=\core\types\VString::checkUrl($arData['web']);

	if(!empty($arData['logo_id'])){
		$file = CFile::GetFileArray($arData["logo_id"]);
		$arData['logo']=$file['SRC'];
	}

	$arData['preview_text'] = htmlspecialchars_decode($arData['preview_text'],ENT_NOQUOTES);
	$arData['phone']=\core\types\VString::formatPhone($arData["company_phone"]);
	$arResult['events'][$arData['id']]=$arData;

	if ($i<=5){
		$arDescription[]= mb_strtolower(trim(preg_replace('#[^\w\d\s\.\,\-а-я]+#iu','',$arData['name'])),'utf-8');
	}
	$i++;
}


if (!empty($arIds)){
	//Получение направлений
	$obQuery = $obEvents->createQuery();
	$obFilter=$obQuery->builder()
		->from('estelife_event_directions')
		->filter()
		->_in('event_id', $arIds);
	$arDirections = $obQuery->select()->all();

	$arDirectionsName = array(
		'1'=>'Пластическая хирургия',
		'2'=>'Косметология',
		'3'=>'Косметика',
		'4'=>'Дерматология',
		'11'=>'Менеджмент',
	);

	foreach ($arDirections as $key=>$val){
		$val['name'] = strtolower($arDirectionsName[$val['type']]);
		$arDirectionsString[$val['event_id']][] = strtolower($val['name']);
	}

	foreach ($arDirectionsString as $key=>$val){
		$arResult['events'][$key]['directions']=implode(', ', $val);
	}

	//Получение формата
	$obQuery = $obEvents->createQuery();
	$obFilter=$obQuery->builder()
		->from('estelife_event_types')
		->filter()
		->_in('event_id', $arIds);
	$arTypes = $obQuery->select()->all();

	$arTypesName = array(
		'1'=>'форум',
		'2'=>'выставка',
		'4'=>'тренинг',
	);

	foreach ($arTypes as $key=>$val){
		$val['name'] = strtolower($arTypesName[$val['type']]);
		$arTypesString[$val['event_id']][] = strtolower($val['name']);;
	}

	foreach ($arTypesString as $key=>$val){
		$arResult['events'][$key]['types']=implode(', ', $val);
	}

	//Получение календаря
	$obQuery = $obEvents->createQuery();
	$obFilter=$obQuery->builder()
		->from('estelife_calendar')
		->filter()
		->_in('event_id', $arIds);
//		->_gte('date',$nDateFrom);

	if($nDateTo){
		$obFilter->_lte('date',$nDateTo);
	}

	$arCalendar = $obQuery->select()->all();

	foreach ($arCalendar as $val)
		$arResult['events'][$val['event_id']]['calendar'][]=$val['date'];

	$nNow = (!empty($nDateFrom)) ? $nDateFrom : time();

	foreach($arResult['events'] as $nKey=>&$arEvent){
		$arEvent['calendar']=\core\types\VDate::createDiapasons($arEvent['calendar'],function(&$nFrom,&$nTo) use($nNow){
			$nNowTo=strtotime(date('d.m.Y', $nNow).' 00:00:00');
			$nNowFrom=strtotime(date('d.m.Y', $nNow).' 23:59:59');
			$nTempTo=$nTo;
			$nTempFrom=$nFrom;

			if($nTo==0){
				$nFrom=\core\types\VDate::date($nFrom, 'j F Y');
			}else{
				$arFrom=explode('.',date('n',$nFrom));
				$arTo=explode('.',date('n',$nTo));
				$sPattern='j F';

				if($arFrom[1]==$arTo[1])
					$sPattern=($arFrom[0]==$arTo[0]) ? 'j' : 'j F';

				$nFrom=\core\types\VDate::date($nFrom,$sPattern);
				$nTo=\core\types\VDate::date($nTo,'j F Y');
			}

			if(($nNowTo<=$nTempTo && $nNowFrom>=$nTempFrom) || ($nNowTo<=$nTempFrom) || ($nNowTo<=$nTempFrom && $nNowFrom>=$nTempFrom))
				return false;

			return true;
		});
		$arEvent['first_period'] = current($arEvent['calendar']);
		$arD = preg_match("/^[0-9]+/", $arEvent['first_period']['from'], $mathes);
		$arEvent['first_date'] =  $mathes[0]. ' <i>';

		if (preg_match("/[а-я]+/u" ,$arEvent['first_period']['from'], $mathes)){
			$arEvent['first_date'] .= mb_substr($mathes[0], 0, 3, 'utf-8').'</i>';
		}else{
			$arM = preg_match("/[а-я]+/u", $arEvent['first_period']['to'], $mathes);
			$arEvent['first_date'] .= mb_substr($mathes[0], 0, 3, 'utf-8').'</i>';
		}
	}
}


$sTemplate=$this->getTemplateName();
$obNav=new \bitrix\VNavigation($obResult,($sTemplate=='ajax'));
$arResult['nav']=$obNav->getNav();

$sTitle='Календарь событий в сфере эстетической медицины';
$sDescription='Все важные события в мире косметологии и пластической хирургиив одном месте';

if (isset($_GET['PAGEN_1']) && intval($_GET['PAGEN_1'])>0){
	$_GET['PAGEN_1'] = intval($_GET['PAGEN_1']);
	$sTitle.=' - '.$_GET['PAGEN_1'].' страница';
	$sDescription.=' - '.$_GET['PAGEN_1'].' страница';
}

$APPLICATION->SetPageProperty("title", $sTitle);
$APPLICATION->SetPageProperty("description", $sDescription);
$this->IncludeComponentTemplate();