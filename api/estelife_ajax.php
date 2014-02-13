<?php
use core\database\mysql\VFilter;
use core\database\VDatabase;
use core\exceptions\VException;
use core\exceptions\VFormException;
use core\http\VHttp;
use core\types\VArray;
use like\VLike;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/estelife/prolog.php");

global $USER,$APPLICATION;

if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
	$_SERVER['HTTP_X_REQUESTED_WITH']!='XMLHttpRequest')
	die('Только Ajax запросы!');

CModule::IncludeModule('estelife');
CModule::IncludeModule('iblock');

$obData = VDatabase::driver();
$arResult=array();
$arData=($_SERVER['REQUEST_METHOD']=='POST') ? $_POST : $_GET;
$sAction=(isset($arData['action'])) ?
	$arData['action'] :
	false;

try{
	$obFormError=new VFormException();

	switch($sAction){
		case 'get_system_settings':
			$arResult=$arData=$APPLICATION->IncludeComponent(
				"estelife:system-settings",
				"ajax",
				array(),
				false
			);
			break;
		case 'get_template':
			if(!empty($arData['get_template_time'])){
				$sFile=(isset($arData['template'])) ?
					trim(strip_tags($arData['template'])) : '';
				$sFile=$_SERVER['DOCUMENT_ROOT'].'/api/templates/'.basename($sFile).'.tpl';

				$arResult=array(
					'time'=>($nTime=@filemtime($sFile)) ?
						$nTime : 0
				);
			}else{
				$sFile=(isset($arData['template'])) ?
					trim(strip_tags($arData['template'])) : '';
				$sFile=$_SERVER['DOCUMENT_ROOT'].'/api/templates/'.basename($sFile).'.tpl';

				if(file_exists($sFile)){
					$sTemplate=file_get_contents($sFile);
					$nTime=filemtime($sFile);
				}else{
					$sTemplate='';
					$nTime=0;
				}

				$arResult=array(
					'template'=>$sTemplate,
					'time'=>$nTime
				);
			}
			break;
		case 'get_filter_data':
			$sFilter=VArray::get($arData,'filter','');

			if(empty($sFilter))
				throw new VException('undefined filter name');

			if(in_array($sFilter,array(
				'preparations_makers',
				'apparatuses_makers',
				'sponsors'
			)))
				throw new VException('unsupported filter');

			$APPLICATION->IncludeComponent(
				"estelife:".$sFilter.".list.filter",
				"ajax",
				array(),
				false
			);
			$arResult=false;
			break;
		case 'training_centers_detail':
			$sKey=(is_numeric($arData['id'])) ?
				'ORG_ID' : 'ORG_NAME';

			$APPLICATION->IncludeComponent(
				"estelife:training_centers.detail",
				"ajax",
				array(
					'ORG_NAME'=>$arData['id'],
				),
				false
			);
			$arResult=false;
			break;
		case 'sponsors_detail':
			$sKey=(is_numeric($arData['id'])) ?
				'ORG_ID' : 'ORG_NAME';

			$APPLICATION->IncludeComponent(
				"estelife:sponsors.detail",
				"ajax",
				array(
					'ORG_NAME'=>$arData['id'],
				),
				false
			);
			$arResult=false;
			break;
		case 'preparations_detail':
			$sKey=(is_numeric($arData['id'])) ?
				'PILL_ID' : 'PILL_NAME';

			$APPLICATION->IncludeComponent(
				"estelife:preparations.detail",
				"ajax",
				array(
					$sKey=>$arData['id']
				),
				false
			);
			$arResult=false;
			break;
		case 'apparatuses_detail':
			$sKey=(is_numeric($arData['id'])) ?
				'APP_ID' : 'APP_NAME';

			$APPLICATION->IncludeComponent(
				"estelife:apparatuses.detail",
				"ajax",
				array(
					$sKey=>$arData['id']
				),
				false
			);
			$arResult=false;
			break;
		case 'apparatuses_makers_detail':
			$sKey=(is_numeric($arData['id'])) ?
				'APP_ID' : 'APP_NAME';

			$APPLICATION->IncludeComponent(
				"estelife:apparatuses_makers.detail",
				"ajax",
				array(
					$sKey=>$arData['id'],
				),
				false
			);
			$arResult=false;
			break;
		case 'preparations_makers_detail':
			$sKey=(is_numeric($arData['id'])) ?
				'PILL_ID' : 'PILL_NAME';

			$APPLICATION->IncludeComponent(
				"estelife:preparations_makers.detail",
				"ajax",
				array(
					$sKey=>$arData['id'],
				),
				false
			);
			$arResult=false;
			break;
		case 'trainings_detail':
			$sKey=(is_numeric($arData['id'])) ?
				'TRAIN_ID' : 'TRAIN_NAME';

			$APPLICATION->IncludeComponent(
				"estelife:trainings.detail",
				"ajax",
				array(
					$sKey=>$arData['id'],
				),
				false
			);
			$arResult=false;
			break;
		case 'events_detail':
			$sKey=(is_numeric($arData['id'])) ?
				'EVENT_ID' : 'EVENT_NAME';

			$APPLICATION->IncludeComponent(
				"estelife:events.detail",
				"ajax",
				array(
					$sKey=>$arData['id'],
				),
				false
			);
			$arResult=false;
			break;
		case 'clinics_detail':
			$sKey=(is_numeric($arData['id'])) ?
				'CLINIC_ID' : 'CLINIC_NAME';

			$APPLICATION->IncludeComponent(
				"estelife:clinics.detail",
				"ajax",
				array(
					$sKey=>$arData['id']
				),
				false
			);
			$arResult=false;
			break;
		case 'promotions_detail':
			$sKey=(is_numeric($arData['id'])) ?
				'ACTION_ID' : 'ACTION_NAME';

			$APPLICATION->IncludeComponent(
				"estelife:promotions.detail",
				"ajax",
				array(
					$sKey=>$arData['id']
				),
				false
			);
			$arResult=false;
			break;
		case 'apparatuses_makers_list':
			$_GET=array_merge($_GET,$arData);
			$APPLICATION->IncludeComponent(
				"estelife:apparatuses_makers.list",
				"ajax",
				array(
					"PAGE_COUNT" => 10
				),
				false
			);
			$arResult=false;
			break;
		case 'preparations_makers_list':
			$_GET=array_merge($_GET,$arData);
			$APPLICATION->IncludeComponent(
				"estelife:preparations_makers.list",
				"ajax",
				array(
					"PAGE_COUNT" => 10
				),
				false
			);
			$arResult=false;
			break;
		case 'training_centers_list':
			$_GET=array_merge($_GET,$arData);
			$APPLICATION->IncludeComponent(
				"estelife:training_centers.list",
				"ajax",
				array("PAGE_COUNT" => 10),
				false
			);
			$arResult=false;
			break;
		case 'sponsors_list':
			$_GET=array_merge($_GET,$arData);
			$APPLICATION->IncludeComponent(
				"estelife:sponsors.list",
				"ajax",
				array("PAGE_COUNT" => 10),
				false
			);
			$arResult=false;
			break;
		case 'trainings_list':
			$_GET=array_merge($_GET,$arData);
			$APPLICATION->IncludeComponent(
				"estelife:trainings.list",
				"ajax",
				array("PAGE_COUNT"=>10),
				false
			);
			$arResult=false;
			break;
		case 'events_list':
			$_GET=array_merge($_GET,$arData);
			$APPLICATION->IncludeComponent(
				"estelife:events.list",
				"ajax",
				array("PAGE_COUNT"=>10),
				false
			);
			$arResult=false;
			break;
		case 'uchebnie_centry_list':
			$_GET=array_merge($_GET,$arData);
			$APPLICATION->IncludeComponent(
				"estelife:uchebniecentry.list",
				"ajax",
				array("PAGE_COUNT"=>10),
				false
			);
			$arResult=false;
			break;
		case 'clinics_list':
			$_GET=array_merge($_GET,$arData);
			$APPLICATION->IncludeComponent(
				"estelife:clinics.list",
				"ajax",
				array("PAGE_COUNT"=>10),
				false
			);
			$arResult=false;
			break;
		case 'preparations_list':
			$_GET=array_merge($_GET,$arData);
			$APPLICATION->IncludeComponent(
				"estelife:preparations.list",
				"ajax",
				array("PAGE_COUNT"=>10),
				false
			);
			$arResult=false;
			break;
		case 'apparatuses_list':
			$_GET=array_merge($_GET,$arData);
			$APPLICATION->IncludeComponent(
				"estelife:apparatuses.list",
				"ajax",
				array("PAGE_COUNT"=>10),
				false
			);
			$arResult=false;
			break;
// TODO: удалить через 2 недели после 13.02.2014
//		case 'promotions_list':
//			$_GET=array_merge($_GET,$arData);
//			$APPLICATION->IncludeComponent(
//				"estelife:promotions.list",
//				"ajax",
//				array("PAGE_COUNT"=>20),
//				false
//			);
//			$arResult=false;
//			break;
		case 'subscribe':
			$_GET=array_merge($_GET,$arData);
			$APPLICATION->IncludeComponent(
				"estelife:subscribe",
				"ajax",
				array(
					"RUB_ID" => $arData['term'],
					"ACTION" => 'update',
					"EMAIL" => $arData['email']
				),
				false
			);
			$arResult=false;
			break;
		case 'get_cities':
			$_GET=array_merge($_GET,$arData);
			$APPLICATION->IncludeComponent(
				"estelife:user.cities",
				"ajax",
				array(),
				false
			);
			$arResult=false;
			break;
		case 'get_promotions_index':
			$_GET=array_merge($_GET,$arData);
			$APPLICATION->IncludeComponent(
				"estelife:promotions.clinics.main.list",
				"ajax",
				array(
					"COUNT" => 3,
					"CITY_ID" =>intval($_GET['city'])
				),
				false
			);
			$arResult=false;
			break;
		case 'set_subscribe':
			$_POST=array_merge($_POST,$arData);
			$APPLICATION->IncludeComponent(
				"estelife:subscribe",
				"ajax",
				array(),
				false
			);
			$arResult=false;
			break;
		case 'set_likes':
			$obLike = new VLike(intval($_POST['type']));
			if (!empty($_POST['md5']))
				$sMd5 = $_POST['md5'];
			else
				$sMd5=false;

			$arResult = $obLike->like(intval($_POST['elementId']), intval($_POST['typeLike']),$sMd5);
			break;
		case 'set_city':
			$_GET=array_merge($_GET,$arData);
			$APPLICATION->IncludeComponent(
				"estelife:user.geo",
				"ajax",
				array("SET"=>$_GET['city']),
				false
			);
			$arResult=false;
			break;
		case 'get_media':
			$_GET=array_merge($_GET,$arData);

			$APPLICATION->IncludeComponent(
				"estelife:photogallery",
				"ajax",
				array(
					"COUNT" => 18,
					"ONLY_VIDEO"=>$_GET['params']['video'],
					"ONLY_PHOTO"=>$_GET['params']['photo'],
				),
				false
			);
			$arResult=false;
			break;
		case 'get_search_history':
			$arResult['list']=array();

			if(!empty($arData['term'])){
				$obQuery=$obData->createQuery();
				$obQuery->builder()
					->from('estelife_search_history')
					->sort('date','desc')
					->field('text', 'name')
					->field('id')
					->filter()
					->_like(
						'text_search',
						$arData['term'],
						VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE
					);

				$arResult['list']=$obQuery
					->select()
					->all();
			}
			break;
		case 'set_search_history':
			$arResult['save']=true;

			if(!empty($arData['term'])){
				$arData['term']=trim(strip_tags($arData['term']));
				$obQuery=$obData->createQuery();
				$obQuery->builder()
					->from('estelife_search_history')
					->field('id')
					->filter()
					->_eq('text', $arData['term']);
				$arSearchs=$obQuery
					->select()
					->assoc();

				$obQuery=$obData->createQuery();
				$obQuery->builder()
					->from('estelife_search_history')
					->value('text', $arData['term'])
					->value('date', time());
				if ($arSearchs['id']>0){
					$obQuery->builder()->filter()
						->_eq('id',$arSearchs['id']);
					$obQuery->update();
					$nId=$arSearchs['id'];
				}else
					$nId=$obQuery->insert()->insertId();
				if (!$nId)
					$arResult['save']=false;
			}
			break;
		case 'get_cities_by_term':
			$arResult['list']=array();

			if(!empty($arData['term'])){
				$obQuery=$obData->createQuery();
				$obQuery->builder()
					->from('iblock_element')
					->sort('NAME','asc')
					->field('NAME','name')
					->field('ID','id')
					->slice(0,5)
					->filter()
					->_eq('IBLOCK_ID',16)
					->_like(
						'NAME',
						$arData['term'],
						VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE
					);

				$arResult['list']=$obQuery
					->select()
					->all();
			}
			break;
		case 'get_city':
			$arResult['list']=array();
			$nCountry=intval(VArray::get($arData,'country',0));

			if($nCountry>0){
				$arSelect=array("ID", "NAME");
				$arFilter=array(
					"IBLOCK_ID"=>16,
					"ACTIVE_DATE"=>"Y",
					"ACTIVE"=>"Y",
					"PROPERTY_COUNTRY" => $nCountry
				);
				$obMetro=CIBlockElement::GetList(
					array("NAME"=>"ASC"),
					$arFilter,
					false,
					false,
					$arSelect
				);

				while($res=$obMetro->Fetch()) {
					$arResult['list'][] = array(
						'value'=>$res['ID'],
						'label'=>$res['NAME']
					);
				}
			}
			break;
		case 'get_metro':
			$arResult['list']=array();
			$nCity=intval(VArray::get($arData,'city',0));

			if($nCity>0){
				$arSelect=array("ID", "NAME");
				$arFilter=array(
					"IBLOCK_ID"=>17,
					"ACTIVE_DATE"=>"Y",
					"ACTIVE"=>"Y",
					"PROPERTY_CITY"=>$nCity
				);
				$obMetro=CIBlockElement::GetList(
					array("NAME"=>"ASC"),
					$arFilter,
					false,
					false,
					$arSelect
				);

				while($res = $obMetro->Fetch()) {
					$arResult['list'][] = array(
						'value'=>$res['ID'],
						'label'=>$res['NAME']
					);
				}
			}
			break;
		case 'get_method':
			$arResult['list']=array();
			$nSpec=intval(VArray::get($arData,'spec',0));
			$nService=intval(VArray::get($arData,'service',0));

			if($nSpec>0 || $nService>0){
				$obQuery=$obData->createQuery();
				$obFilter=$obQuery->builder()
					->from('estelife_methods')
					->field('id')
					->field('name')
					->filter();

				if($nSpec>0)
					$obFilter->_eq('specialization_id',$nSpec);
				else
					$obFilter->_eq('service_id',$nService);

				$arTemp=$obQuery->select()->all();

				if(!empty($arTemp)){
					foreach($arTemp as $arValue)
						$arResult['list'][]=array(
							'value'=>$arValue['id'],
							'label'=>$arValue['name']
						);
				}
			}
			break;
		case 'get_service':
			$arResult['list']=array();
			$nSpec=intval(VArray::get($arData,'spec',0));

			if($nSpec>0){
				$obQuery=$obData->createQuery();
				$obQuery->builder()
					->from('estelife_services')
					->field('id')
					->field('name')
					->filter()
						->_eq('specialization_id', $nSpec);
				$arTemp=$obQuery->select()->all();

				if(!empty($arTemp))
					foreach($arTemp as $arValue)
						$arResult['list'][]=array(
							'value'=>$arValue['id'],
							'label'=>$arValue['name']
						);
			}
			break;
		case 'get_concreate':
			$arResult['list']=array();
			$nService=intval(VArray::get($arData,'service',0));
			$nMethod=intval(VArray::get($arData,'method',0));

			if($nService>0 || $nMethod>0){
				$obQuery=$obData->createQuery();
				$obFilter=$obQuery->builder()
					->from('estelife_service_concreate')
					->field('id')
					->field('name')
					->filter();

				if($nService>0)
					$obFilter->_eq('service_id',$nService);
				else
					$obFilter->_eq('method_id',$nMethod);

				$arTemp=$obQuery->select()->all();

				if(!empty($arTemp))
					foreach($arTemp as $arValue)
						$arResult['list'][]=array(
							'value'=>$arValue['id'],
							'label'=>$arValue['name']
						);
			}
			break;
		case 'get_pills':
			if(!empty($arData['term'])){
				$obQuery = $obData->createQuery();
				$obQuery->builder()->from('estelife_pills')->filter()
					->_like('name',$arData['term'],VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);
				$obQuery->builder()
					->field('name')
					->field('translit');
				$arResult['list'] = $obQuery->select()->all();
			}else{
				$arResult['list']=array();
			}
			break;
		case 'get_apparatus':
			if(!empty($arData['term'])){
				$obQuery = $obData->createQuery();
				$obQuery->builder()->from('estelife_apparatus')->filter()
					->_like('name',$arData['term'],VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);
				$obQuery->builder()
					->field('name')
					->field('translit');
				$arResult['list'] = $obQuery->select()->all();
			}else{
				$arResult['list']=array();
			}
			break;
		case 'get_uch':
			if(!empty($arData['term'])){
				$obQuery = $obData->createQuery();
				$obQuery->builder()->from('estelife_events', 'ee');
				$obJoin = $obQuery->builder()->join();
				$obJoin->_left()
					->_from('ee', 'id')
					->_to('estelife_company_events', 'event_id', 'ece');
				$obJoin->_left()
					->_from('ee', 'id')
					->_to('estelife_event_types', 'event_id', 'eet');
				$obJoin->_left()
					->_from('ece', 'company_id')
					->_to('estelife_companies', 'id', 'ec');
				$obQuery->builder()
					->field('ec.name')
					->field('ec.id');
				$obFilter=$obQuery->builder()->filter()
					->_eq('eet.type', 3)
					->_like('ec.name',$arData['term'],VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);
				$obQuery->builder()->group('ec.id');
				$obQuery->builder()->sort('ec.name', 'asc');
				$arCompanies = $obQuery->select()->all();
				foreach ($arCompanies as $val){
					$val['translit'] = \core\types\VString::translit($val['name']).'-'.$val['id'];
					unlink($val['id']);
					$arResult['list'][] = $val;
				}
			}else{
				$arResult['list']=array();
			}
			break;
		case 'geolocation':
			if(!empty($arData['city'])){
				//Получение ID города
				$obCity = VDatabase::driver();
				$obQuery = $obCity->createQuery();
				$obQuery->builder()->from('iblock_element');
				$obQuery->builder()->filter()
					->_eq('IBLOCK_ID', 16)
					->_like('NAME',$arData['city'],VFilter::LIKE_BEFORE|VFilter::LIKE_AFTER);
				$arCities = $obQuery->select()->all();
				if (!empty($arCities)){
					$arCity = reset($arCities);
					$arPhpCity = $_COOKIE['estelife_city'];
					if ($arPhpCity != $arCity['ID']){
						$arResult['list']['match'] = 0;
						$arResult['list']['phpcity'] = $arPhpCity;
						$arResult['list']['city'] = $arCity['ID'];
					}else{
						$arResult['list']['match'] = 1;
						$arResult['list']['phpcity'] = $arResult['list']['city'] = $arPhpCity;
					}
				}

			}else{
				$arResult['list']=array();
			}
			break;
		case 'get_media_content':
			$nId=(!empty($arData['id'])) ?
				intval($arData['id']) : 0;
			$bVideo=(!empty($arData['video']));

			if(!$bVideo){
				$APPLICATION->IncludeComponent(
					"estelife:photogallery.photos",
					"ajax",
					array("ID"=>$nId),
					false
				);
			}else{
				$APPLICATION->IncludeComponent(
					"estelife:photogallery.video",
					"ajax",
					array("ID"=>$nId),
					false
				);
			}

			$arResult=false;
			break;
		case 'add_request':
			$APPLICATION->IncludeComponent(
				"estelife:clinics.request",
				"ajax"
			);
			$arResult=false;
			break;
		case 'get_clinics':
			$arResult['list']=array();

			if(!empty($arData['term'])){
				$obQuery = $obData->createQuery();
				$obQuery->builder()
					->from('estelife_clinics')
					->sort('name','asc')
					->field('name')
					->field('id')
					->slice(0,5)
					->filter()
					->_like(
						'name',
						$arData['term'],
						VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE
					);
				$arResult['list']=$obQuery
					->select()
					->all();
			}
			break;
		default:
			throw new VException('unsupported action',21);
	}
}catch(VFormException $e){
	$arResult['error']=array(
		'text'=>$e->getMessage(),
		'code'=>$e->getCode(),
		'fields'=>$e->getFieldErrors()
	);
}catch(VException $e){
	$arResult['error']=array(
		'text'=>$e->getMessage(),
		'code'=>$e->getCode()
	);
}

if($arResult)
	echo json_encode($arResult);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");