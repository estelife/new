<?php
use core\database\VDatabase;
use core\database\VFilter;
use core\types\VArray;
use core\types\VString;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");
CModule::IncludeModule("estelife");

$obClinics = VDatabase::driver();
$obGet=new VArray($_GET);


if (isset($arParams['PAGE_COUNT']) && $arParams['PAGE_COUNT']>0)
	$arPageCount = $arParams['PAGE_COUNT'];
else
	$arPageCount = 10;

if(!$obGet->blank('city')){
	$arResult['city'] = intval($obGet->one('city'));
}elseif(isset($_COOKIE['estelife_city'])){
	$arResult['city'] = intval($_COOKIE['estelife_city']);
}

//Получение списка обучений
$obQuery = $obClinics->createQuery();
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
$obJoin->_left()
	->_from('ec','id')
	->_to('estelife_company_geo','company_id','ecg');
$obJoin->_left()
	->_from('ee','city_id')
	->_to('iblock_element','ID','ct')
	->_cond()->_eq('ct.IBLOCK_ID',16);
$obJoin->_left()
	->_from('ecg','city_id')
	->_to('iblock_element','ID','company_ct')
	->_cond()->_eq('company_ct.IBLOCK_ID',16);
$obJoin->_left()
	->_from('ec','id')
	->_to('estelife_company_contacts','company_id','ec_contacts')
	->_cond()->_eq('ec_contacts.type','phone');
$obJoin->_left()
	->_from('ee','id')
	->_to('estelife_event_contacts','event_id','ee_contacts')
	->_cond()->_eq('ee_contacts.type','phone');
$obJoin->_left()
	->_from('ec','id')
	->_to('estelife_company_contacts','company_id','ec_contacts_w')
	->_cond()->_eq('ec_contacts_w.type','web');
$obJoin->_left()
	->_from('ee', 'id')
	->_to('estelife_event_directions', 'event_id', 'eed');
$obJoin->_left()
	->_from('ee','id')
	->_to('estelife_event_contacts','event_id','ee_contacts_w')
	->_cond()->_eq('ee_contacts_w.type','web');
$obJoin->_left()
	->_from('ee','id')
	->_to('estelife_calendar','event_id','ecal');

$obQuery->builder()
	//->slice(0,1)
	->field('ee.id','id')
	->field('ee.short_name','name')
	->field('ee.full_name', 'full_name')
	->field('ee.preview_text', 'preview_text')
	->field('ee.city_id','city_id')
	->field('ecg.city_id','company_city_id')
	->field('ee.address','address')
	->field('ee.translit','translit')
	->field('ecg.address','company_address')
	->field('ct.NAME','city_name')
	->field('company_ct.NAME','company_city_name')
	->field('ec_contacts.value','company_phone')
	->field('ee_contacts.value','phone')
	->field('ec_contacts_w.value','company_web')
	->field('ee_contacts_w.value','web')
	->field('ec.logo_id','logo_id')
	->field('ec.name','company_name')
	->field('ec.id','company_id');

$obFilter=$obQuery->builder()->filter();
$obFilter->_eq('eet.type', 3);

if (!empty($arResult['city']) && $obGet->one('direction')!='all'){
	$obFilter->_or()
		->_eq('ecg.city_id', $arResult['city']);
	$obFilter->_or()
		->_eq('ee.city_id', $arResult['city']);
}

if(!$obGet->blank('direction'))
	$obFilter->_eq('eed.type', intval($obGet->one('direction')));

$nDateFrom=preg_replace('/^(\d{2}).(\d{2}).(\d{2})$/','$1.$2.20$3 ',$obGet->one('date_from'));
$nDateFrom=\core\types\VDate::dateToTime($nDateFrom.' 00:00');

if (!$obGet->blank('date_to')){
	$nDateTo = preg_replace('/^(\d{2}).(\d{2}).(\d{2})$/','$1.$2.20$3 ',$obGet->one('date_to'));
	$nDateTo = \core\types\VDate::dateToTime($nDateTo. ' 23:59');
}else{
	$nDateTo = false;
}

$obFilter->_gte('ecal.date',$nDateFrom);

if ($nDateTo){
	$obFilter->_lte('ecal.date',$nDateTo);
}

$obQuery->builder()->sort('ecal.date','asc');
$obQuery->builder()->group('ee.id');

$obResult = $obQuery->select();

$obResult = $obResult->bxResult();
$nCount = $obResult->SelectedRowsCount();
$arResult['count'] = 'Найден'.VString::spellAmount($nCount, ',о,о'). ' '.$nCount.' семинар'.VString::spellAmount($nCount, ',а,ов');
\bitrix\ERESULT::$DATA['count'] = $arResult['count'];
$obResult->NavStart($arPageCount);

$arResult['training'] = array();
$arIds = array();

while($arData=$obResult->Fetch()){
	$arIds[] = $arData['id'];

	$arData['link'] = '/tr'.$arData['id'].'/';

	if(empty($arData["phone"]) && !empty($arData["company_phone"]))
		$arData['phone']=\core\types\VString::formatPhone($arData["company_phone"]);

	if(empty($arData['web']) && !empty($arData['company_web']))
		$arData['web']=$arData['company_web'];

	if(!empty($arData['web']))
		$arData['web_short']=\core\types\VString::checkUrl($arData['web']);

	if(empty($arData['address']) && !empty($arData['company_address'])){
		$arData['address']=$arData['company_address'];
		$arData['city_name']=$arData['company_city_name'];
	}

	$arData['company_link'] = '/tc'.$arData['company_id'].'/';

	if(!empty($arData['logo_id'])){
		$file = CFile::ShowImage($arData["logo_id"], 190, 100, 'alt="'.$arData['name'].'"');
		$arData['logo']=$file;
	}else{
		$arData['logo']="<img src='/img/icon/unlogo.png' />";
	}

	$arData['preview_text'] = \core\types\VString::truncate(nl2br(htmlspecialchars_decode($arData['preview_text'],ENT_NOQUOTES)), 80, '...');
	$arData['phone']=\core\types\VString::formatPhone($arData["company_phone"]);
	$arResult['training'][$arData['id']]=$arData;
}

if (!empty($arIds)){
	//Получение календаря
	$obQuery=$obClinics->createQuery();
	$obFilter=$obQuery->builder()
		->from('estelife_calendar')
		->sort('date','asc')
		->filter()
			->_in('event_id', $arIds);
//			->_gte('date',$nDateFrom);

	if($nDateTo)
		$obFilter->_lte('date',$nDateTo);

	$arCalendar=$obQuery->select()->all();


	foreach ($arCalendar as $val)
		$arResult['training'][$val['event_id']]['calendar'][]=$val['date'];


	$nNow=(!empty($nDateFrom)) ?
		$nDateFrom :
		time();

	foreach($arResult['training'] as $nKey=>&$arTraining){
		$arTraining['calendar']=\core\types\VDate::createDiapasons($arTraining['calendar'],function(&$nFrom,&$nTo) use($nNow){
			$nNowTo=strtotime(date('d.m.Y', $nNow).' 00:00:00');
			$nNowFrom=strtotime(date('d.m.Y', $nNow).' 23:59:59');
			$nTempTo=($nTo==0) ? $nFrom : $nTo;
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

		$arTraining['first_period'] = end($arTraining['calendar']);
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

$sTitle='Семинары, курсы и обучения - косметология и пластическая хирургия.';
$sDescription='Расписание семинаров, обучений и курсов в учебных центрах по косметологии и платисческой хирургии.';

if (isset($_GET['PAGEN_1']) && intval($_GET['PAGEN_1'])>0){
	$_GET['PAGEN_1'] = intval($_GET['PAGEN_1']);
	$sTitle.=' - '.$_GET['PAGEN_1'].' страница';
	$sDescription.=' - '.$_GET['PAGEN_1'].' страница';
}

$APPLICATION->SetPageProperty("title", $sTitle);
$APPLICATION->SetPageProperty("description", $sDescription);

$sTemplate=$this->getTemplateName();
$obNav=new \bitrix\VNavigation($obResult,($sTemplate=='ajax'));
$arResult['nav']=$obNav->getNav();

$this->IncludeComponentTemplate();