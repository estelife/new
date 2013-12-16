<?php
use core\database\VDatabase;
use core\types\VArray;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");
CModule::IncludeModule("estelife");

global $APPLICATION;
$obCompanies = VDatabase::driver();

$arCompanyID =  (isset($arParams['ID'])) ?
	intval($arParams['ID']) : 0;

//Получаем данные по организаторам
$obQuery = $obCompanies->createQuery();
$obQuery->builder()->from('estelife_companies', 'ec');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ec', 'id')
	->_to('estelife_company_geo', 'company_id', 'ecg');
$obJoin->_left()
	->_from('ecg','country_id')
	->_to('iblock_element','ID','ct')
	->_cond()->_eq('ct.IBLOCK_ID',15);
$obJoin->_left()
	->_from('ecg','city_id')
	->_to('iblock_element','ID','cty')
	->_cond()->_eq('cty.IBLOCK_ID',16);
$obJoin->_left()
	->_from('ec','id')
	->_to('estelife_company_types','company_id','ect')
	->_cond()->_eq('ect.type', 4);
$obJoin->_left()
	->_from('ect', 'id')
	->_to('estelife_company_type_geo', 'company_id', 'ectg');
$obJoin->_left()
	->_from('ectg','country_id')
	->_to('iblock_element','ID','cttype')
	->_cond()->_eq('ct.IBLOCK_ID',15);
$obJoin->_left()
	->_from('ectg','city_id')
	->_to('iblock_element','ID','ctytype')
	->_cond()->_eq('ctytype.IBLOCK_ID',16);
$obQuery->builder()
	->field('ec.*')
	->field('ct.NAME', 'country_name')
	->field('ct.ID', 'country_id')
	->field('cttype.NAME', 'type_country_name')
	->field('cttype.ID', 'type_country_id')
	->field('cty.NAME', 'city_name')
	->field('ctytype.NAME', 'type_city_name')
	->field('ecg.address')
	->field('ectg.type_address')
	->field('ect.name', 'type_name')
	->field('ect.logo_id', 'type_logo_id')
	->field('ect.detail_text', 'type_detail_text')
	->field('ect.id', 'type_id');
$obQuery->builder()->filter()
	->_eq('ec.id', $arCompanyID);
$arResult['company'] = $obQuery->select()->assoc();

if (!empty($arResult['company']['type_name'])){
	$arResult['company']['name'] = $arResult['company']['type_name'];
}
unset($arResult['company']['type_name']);

if (!empty($arResult['company']['type_address'])){
	$arResult['company']['address'] = $arResult['company']['type_address'];
}

unset($arResult['company']['type_address']);

if (!empty($arResult['company']['type_logo_id'])){
	$arResult['company']['logo_id'] = $arResult['company']['type_logo_id'];
}
unset($arResult['company']['type_logo_id']);

if (!empty($arResult['company']['type_country_name'])){
	$arResult['company']['country_name'] = $arResult['company']['type_country_name'];
	$arResult['company']['country_id'] = $arResult['company']['type_country_id'];
}
unset($arResult['company']['type_country_id']);
unset($arResult['company']['type_country_name']);

if (!empty($arResult['company']['type_city_name'])){
	$arResult['company']['city_name'] = $arResult['company']['type_city_name'];
}
unset($arResult['company']['type_city_name']);

$arResult['company']['address'] = 'г. '.$arResult['company']['city_name'].', '.$arResult['company']['address'];

$arResult['company']['img'] = CFile::ShowImage($arResult['company']['logo_id'],200, 85, 'alt='.$arResult['company']['name']);

if (!empty($arResult['company']['type_detail_text'])){
	$arResult['company']['detail_text'] = $arResult['company']['type_detail_text'];
}
unset($arResult['company']['type_detail_text']);

$arResult['company']['detail_text'] = nl2br(htmlspecialchars_decode($arResult['company']['detail_text'], ENT_NOQUOTES));


//Получение контактов для компании
$obQuery = $obCompanies->createQuery();
$obQuery->builder()->from('estelife_company_contacts');
$obQuery->builder()->filter()
	->_eq('company_id', $arCompanyID);
$arContacts = $obQuery->select()->all();
if (!empty($arContacts)){
	foreach ($arContacts as $val){
		if ($val['type'] == 'web'){
			$arResult['company']['web'][] = $val['value'];
		}elseif ($val['type'] == 'phone'){
			$arResult['company']['phone'][] = $val['value'];
		}elseif ($val['type'] == 'fax'){
			$arResult['company']['fax'][] = $val['value'];
		}elseif ($val['type'] == 'email'){
			$arResult['company']['email'][] = $val['value'];
		}
	}
}
//Получение контактов для типа компании
if (!empty($arResult['company']['type_id'])){
	$obQuery = $obCompanies->createQuery();
	$obQuery->builder()->from('estelife_company_type_contacts');
	$obQuery->builder()->filter()
		->_eq('company_id', $arResult['company']['type_id']);
	$arContacts = $obQuery->select()->all();
	if (!empty($arContacts)){
		foreach ($arContacts as $val){
			if ($val['type'] == 'web'){
				$arResult['company']['type_web'][] = $val['value'];
			}elseif ($val['type'] == 'phone'){
				$arResult['company']['type_phone'][] = $val['value'];
			}elseif ($val['type'] == 'fax'){
				$arResult['company']['type_fax'][] = $val['value'];
			}elseif ($val['type'] == 'email'){
				$arResult['company']['type_email'][] = $val['value'];
			}
		}
	}
}


if (!empty($arResult['company']['type_web'])){
	$arResult['company']['web'] = $arResult['company']['type_web'];
	unset($arResult['company']['type_web']);
}


if(!empty($arResult['company']['web']))
	$arResult['company']['web'] = current($arResult['company']['web']);
	$arResult['company']['web_short']=\core\types\VString::checkUrl($arResult['company']['web']);

if (!empty($arResult['company']['type_phone'])){
	$arResult['company']['phone'] = $arResult['company']['type_phone'];
	unset($arResult['company']['type_phone']);
}
if (!empty($arResult['company']['type_fax'])){
	$arResult['company']['fax'] = $arResult['company']['type_fax'];
	unset($arResult['company']['type_fax']);
}
if (!empty($arResult['company']['type_email'])){
	$arResult['company']['email'] = $arResult['company']['type_email'];
	unset($arResult['company']['type_email']);
}


$arResult['company']['contacts']['web'] = $arResult['company']['web'];
$arResult['company']['contacts']['web_short'] = (!empty($arResult['company']['web_short'])) ? $arResult['company']['web_short'] : '';
$arResult['company']['contacts']['phone'] = implode(', ', $arResult['company']['phone']);
$arResult['company']['contacts']['fax'] = implode(', ', $arResult['company']['fax']);
$arResult['company']['contacts']['email'] = implode(', ', $arResult['company']['email']);


//получение мероприятий компании
$obQuery = $obCompanies->createQuery();
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
	->_from('ee','id')
	->_to('estelife_calendar','event_id','ecal');

$obQuery->builder()
	->field('ee.id','id')
	->field('ee.short_name','name')
	->field('ee.preview_text', 'preview_text');
$obFilter=$obQuery->builder()->filter()
	->_eq('eet.type', 3)
	->_eq('ece.company_id', $arResult['company']['id']);

$nDateFrom=\core\types\VDate::dateToTime(date('d.m.Y', time()).' 00:00');
$obFilter->_gte('ecal.date',$nDateFrom);
$obQuery->builder()->sort('ecal.date','asc');
$obQuery->builder()->group('ee.id');
$obQuery->builder()->slice(0,10);
$arEvents = $obQuery->select()->all();

foreach ($arEvents as $val){
	$val['link'] = '/tr'.$val['id'].'/';
	$arResult['events'][$val['id']] = $val;
	$arIds[] = $val['id'];
}

if (!empty($arIds)){
	//Получение календаря
	$obQuery=$obCompanies->createQuery();
	$obFilter=$obQuery->builder()
		->from('estelife_calendar')
		->sort('date','asc')
		->filter()
		->_in('event_id', $arIds);
//		->_gte('date',$nDateFrom);

	$arCalendar=$obQuery->select()->all();


	foreach ($arCalendar as $val)
		$arResult['events'][$val['event_id']]['calendar'][]=$val['date'];


	foreach($arResult['events'] as $nKey=>&$arTraining){
		if (!empty($arTraining['calendar'])){
			$arTraining['calendar']=\core\types\VDate::createDiapasons($arTraining['calendar'],function(&$nFrom,&$nTo){
				$arNowTo = strtotime(date('d.m.Y', time()).' 00:00:00');
				$arNowFrom =strtotime(date('d.m.Y', time()).' 23:59:59');

				if (($arNowTo<=$nTo && $arNowFrom>=$nFrom) || ($arNowTo<=$nFrom) || ($arNowTo<=$nFrom && $arNowFrom>=$nFrom)){
					if($nTo==0){
						$nFrom=\core\types\VDate::date($nFrom, 'j F');
					}else{
						$arFrom=explode('.',date('n',$nFrom));
						$arTo=explode('.',date('n',$nTo));
						$sPattern='j F';

						if($arFrom[1]==$arTo[1])
							$sPattern=($arFrom[0]==$arTo[0]) ? 'j' : 'j F';

						$nFrom=\core\types\VDate::date($nFrom,$sPattern);
						$nTo=\core\types\VDate::date($nTo,'j F');
					}
					return false;
				}

				return true;
			});
			$arTraining['first_period'] = reset($arTraining['calendar']);
			$arD = preg_match("/^[0-9]+/", $arTraining['first_period']['from'], $mathes);
			$arTraining['first_date'] =  $mathes[0]. ' <i>';

			if (preg_match("/[а-я]+/u" ,$arTraining['first_period']['from'], $mathes)){
				$arTraining['first_date'] .= mb_substr($mathes[0], 0, 3, 'utf-8').'</i>';
			}else{
				$arM = preg_match("/[а-я]+/u", $arTraining['first_period']['to'], $mathes);
				$arTraining['first_date'] .= mb_substr($mathes[0], 0, 3, 'utf-8').'</i>';
			}
		}
	}

}
$APPLICATION->SetPageProperty("title", 'Estelife - '.$arResult['company']['name']);
$APPLICATION->SetPageProperty("description", mb_substr(trim(strip_tags($arResult['company']['preview_text'])),0,140,'utf-8'));
$APPLICATION->SetPageProperty("keywords", "Estelife, учебный центр, ".mb_strtolower(trim(preg_replace('#[^\w\d\s\.\,\-а-я]+#iu','',$arResult['company']['name'])),'utf-8'));

$this->IncludeComponentTemplate();