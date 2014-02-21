<?php
use core\database\mysql\VFilter;
use core\database\VDatabase;
use core\exceptions\VException;
use core\exceptions\VFormException;
use core\http\VHttp;
use core\types\VArray;
use reference\services as rs;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/estelife/prolog.php");

global $USER;

if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
	$_SERVER['HTTP_X_REQUESTED_WITH']!='XMLHttpRequest')
	die('Только Ajax запросы!');

CModule::IncludeModule('estelife');

$arResult=array();
$arData=($_SERVER['REQUEST_METHOD']=='POST') ? $_POST : $_GET;
$sAction=(isset($arData['action'])) ?
	$arData['action'] :
	false;

try{
	$obFormError=new VFormException();

	switch($sAction){
		case 'clinic':
			if(!empty($arData['term'])){
				$sName=trim(strip_tags($arData['term']));
				$obClinics=VDatabase::driver();
				$obQuery=$obClinics->createQuery();
				$obQuery->builder()
					->from('estelife_clinics','ec')
					->join()
					->_left()
					->_from('ec','city_id')
					->_to('iblock_element','ID','ct');
				$obQuery->builder()
					->field('ec.name')
					->field('ec.id')
					->field('ct.NAME','city')
					->filter()
					->_like('ec.name',$sName,VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);

				$obRecords=$obQuery->select()->all();
				$arResult['list']=array();

				if(count($obRecords)>0){
					foreach($obRecords as $obRecord){
						$arRecord=$obRecord;
						$arTerm=array($obRecord['city']);

						if($arRecord['clinic_id']!=0)
							$arTerm[]='филиал';

						$arRecord['name']=html_entity_decode($arRecord['name'],ENT_QUOTES,'utf-8').' ('.implode($arTerm).')';
						$arResult['list'][]=$arRecord;
					}
				}
			}else{
				$arResult['list']=array();
			}
			break;
		case 'service_concreate':
			if(!empty($arData['term'])){
				$sTypeId=intval($arData['term']);

				//получение списка типов услуг
				$obPrices= VDatabase::driver();

				$obQuery = $obPrices->createQuery();
				$obQuery->builder()->from('estelife_service_concreate', 'esc');
				$obJoin=$obQuery->builder()->join();
				$obJoin->_left()
					->_from('esc','id')
					->_to('estelife_clinic_services','service_concreate_id','ec');
				$obQuery->builder()->filter()
					->_eq('clinic_id', $sTypeId);
				$arResult['list'] = $obQuery->select()->all();
			}else{
				$arResult['list']=array();
			}
			break;
		case 'company':
			if(!empty($arData['term'])){
				$arCompanyType=array();
				$sName=trim(strip_tags($arData['term']));
				$sTypeId=intval($arData['type_id']);

				//получение списка типов компании
				$obCompanies= VDatabase::driver();

				$obQuery = $obCompanies->CreateQuery();
				$obFilter=$obQuery->builder()->from('estelife_company_types')
					->field('name')
					->field('company_id')
					->filter()
					->_like('name',$sName,VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);

				if(!empty($sTypeId))
					$obFilter->_eq('type', $sTypeId);

				foreach ($obQuery->select()->all() as $val){
					$arCType['id'] = $val['company_id'];
					$arCType['name'] = $val['name'];
					$arCompanyType[] = $arCType;
				}

				$obQuery = $obCompanies->CreateQuery();
				$obQuery->builder()->from('estelife_companies')
					->field('id')
					->field('name')
					->filter()
					->_like('name',$sName,VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);

				$arCompanies= $obQuery->select()->all();

				$arCompanies = array_merge($arCompanyType, $arCompanies);
				if (!empty($arCompanies)){
					foreach ($arCompanies as $val){
						$val['name'] =  html_entity_decode($val['name'], ENT_QUOTES, 'utf-8');
						$arResult['list'][] = $val;
					}
				}
			}else{
				$arResult['list']=array();
			}
			break;
		case 'activity':
			if(!empty($arData['term'])){
				$sName=trim(strip_tags($arData['term']));

				//получение списка типов компании
				$obActivity= VDatabase::driver();

				$obQuery = $obActivity->CreateQuery();
				$obFilter=$obQuery->builder()->from('estelife_events')
					->field('short_name')
					->field('id')
					->filter()
					->_like('short_name',$sName,VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);

				$arActivity= $obQuery->select()->all();

				if (!empty($arActivity)){
					foreach ($arActivity as $val){
						$val['name'] =  html_entity_decode($val['short_name'], ENT_QUOTES, 'utf-8');
						$arResult['list'][] = $val;
					}
				}

			}else{
				$arResult['list']=array();
			}
			break;
		case 'halls':
			$arResult['list']=array();

			if(!empty($arData['term'])){
				$nId=trim(strip_tags($arData['term']));

				//получение списка залов
				$obHalls= VDatabase::driver();

				$obQuery = $obHalls->CreateQuery();
				$obFilter=$obQuery->builder()->from('estelife_event_halls')
					->field('name')
					->field('id')
					->filter()
					->_eq('event_id',$nId);

				$arHalls= $obQuery->select()->all();


				if (!empty($arHalls))
					$arResult['list'] = $arHalls;
			}
			break;
		case 'sections':
			$arResult['list']=array();

			if(!empty($arData['term'])){
				$nId=trim(strip_tags($arData['term']));

				//получение списка залов
				$obHalls= VDatabase::driver();

				$obQuery = $obHalls->CreateQuery();
				$obFilter=$obQuery->builder()->from('estelife_event_sections')
					->field('name')
					->field('id')
					->filter()
					->_eq('event_id',$nId);

				$arHalls= $obQuery->select()->all();


				if (!empty($arHalls))
					$arResult['list'] = $arHalls;
			}
			break;
		case 'spec':
			if(!empty($arData['term'])){
				$sName=trim(strip_tags($arData['term']));

				$obApp= VDatabase::driver();

				$obQuery = $obApp->CreateQuery();
				$obFilter=$obQuery->builder()->from('user')
					->field('NAME')
					->field('ID')
					->filter()
					->_like('NAME',$sName,VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);

				$arResult['list'] = $obQuery->select()->all();
			}else{
				$arResult['list']=array();
			}
			break;
		case 'articles':
			if(!empty($arData['term'])){
				$sName=trim(strip_tags($arData['term']));

				$obApp= VDatabase::driver();

				$obQuery = $obApp->CreateQuery();
				$obFilter=$obQuery->builder()->from('iblock_element')
					->field('NAME')
					->field('ID')
					->filter()
					->_eq('IBLOCK_ID', 14)
					->_eq('ACTIVE', 'Y')
					->_like('NAME',$sName,VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);

				$arResult['list'] = $obQuery->select()->all();
			}else{
				$arResult['list']=array();
			}
			break;
		case 'apparatus':
			if(!empty($arData['term'])){
				$arCompanyType=array();
				$sName=trim(strip_tags($arData['term']));

				$obApp= VDatabase::driver();

				$obQuery = $obApp->CreateQuery();
				$obFilter=$obQuery->builder()->from('estelife_apparatus')
					->field('name')
					->field('id')
					->filter()
					->_like('name',$sName,VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);

				$arResult['list'] = $obQuery->select()->all();
			}else{
				$arResult['list']=array();
			}
			break;
		case 'pills':
			if(!empty($arData['term'])){
				$arCompanyType=array();
				$sName=trim(strip_tags($arData['term']));

				$obApp= VDatabase::driver();

				$obQuery = $obApp->CreateQuery();
				$obFilter=$obQuery->builder()->from('estelife_pills')
					->field('name')
					->field('id')
					->filter()
					->_like('name',$sName,VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);

				$arResult['list'] = $obQuery->select()->all();
			}else{
				$arResult['list']=array();
			}
			break;
		case 'add_gallery':
			if(!empty($arData['name'])){
				$sName=trim(strip_tags($arData['name']));
				$nEventId=intval($arData['event_id']);
				$obApp= VDatabase::driver();

				$obQuery=$obApp->CreateQuery();
				$obQuery->builder()
					->from('estelife_galleries')
					->value('name', $sName)
					->value('date_add',time());

				$nGallery = $obQuery->insert()->insertId();
				$bEvent=false;

				if(!empty($nEventId)){
					$obQuery->builder()
						->from('estelife_events')
						->filter()
						->_eq('id',$nEventId);
					$obEvent=$obQuery->select();

					if($obEvent->count()>0){
						$arEvent=$obEvent->assoc();
						$bEvent=$arEvent['id'];
					}
				}

				if(!$bEvent)
					$_SESSION['temp_gallery'][] = $nGallery;
				else{
					$obQuery->builder()
						->from('estelife_event_galleries')
						->value('event_id',$bEvent)
						->value('gallery_id',$nGallery);
					$obQuery->insert();
				}

				$arResult['item']=$nGallery;
			}else{
				$arResult['item']=0;
			}
			break;
		case 'delete_gallery':
			$nGallery=(isset($arData['id'])) ?
				intval($arData['id']) : 0;

			if(!empty($nGallery)){
				$obQuery=VDatabase::driver()->createQuery();
				$obQuery->builder()
					->from('estelife_galleries')
					->filter()
					->_eq('id',$nGallery);
				$obQuery->delete();

				if(!empty($_SESSION['temp_gallery']) &&
					($nKey=array_search($nGallery,$_SESSION['temp_gallery']))){
					unset($_SESSION['temp_gallery'][$nKey]);
				}

				$arResult['complete']=1;
			}else{
				$arResult['error']='Не передан идетификатор галлереи';
			}
			break;
		case 'upload_photo':
			$arPhoto=(isset($_FILES['photo'])) ?
				$_FILES['photo'] : array();
			$nGallery=(isset($arData['gallery_id'])) ?
				intval($arData['gallery_id']) : 0;

			try{
				if(empty($arPhoto['tmp_name']))
					throw new VException('Файл не был загружен',911);

				if(empty($nGallery))
					throw new VException('Не определена галлерея',912);

				$arPhoto['old_file']=0;
				$arPhoto['module']='estelife';
				$arPhoto['del']=0;

				if(($nPhotoId=CFile::SaveFile($arPhoto, "estelife"))==false)
					throw new VException('Не удалось записать файл');

				$obQuery=VDatabase::driver()->createQuery();
				$obQuery->builder()
					->from('estelife_photos')
					->value('gallery_id',$nGallery)
					->value('image_id',$nPhotoId);
				$obResult=$obQuery->insert();
				$nPhotoId=$obResult->insertId();

				if(empty($nPhotoId))
					throw new VException('Не удалось сохранить данные о фото');

				$arResult['photo']=$nPhotoId;
			}catch(VException $e){
				$arResult['error']=array(
					'text'=>$e->getMessage(),
					'code'=>$e->getCode()
				);
			}
			break;
		case 'delete_photo':
			try{
				$nId=(isset($arData['id'])) ?
					intval($arData['id']) : 0;

				if(empty($nId))
					throw new VException('Не найден идентификатор');

				$obQuery=VDatabase::driver()->createQuery();
				$obQuery->builder()
					->from('estelife_photos')
					->filter()
					->_eq('id',$nId);
				$arPhoto=$obQuery->select()->assoc();

				if(empty($arPhoto))
					throw new VException('Такая фотография не зарегана в базе');

				CFile::Delete($arPhoto['image_id']);
				$obQuery->builder()
					->from('estelife_photos')
					->filter()
					->_eq('id',$nId);
				$obQuery->delete();
				$arResult['complete']=1;
			}catch(VException $e){
				$arResult['error']=array(
					'text'=>$e->getMessage(),
					'code'=>$e->getCode()
				);
			}
			break;
		case 'save_service_concreate':
		case 'save_methods':
			$nServiceId=(isset($arData['service_id'])) ?
				intval($arData['service_id']) : 0;

			if(empty($nServiceId))
				$obFormError->setFieldError('undefined service id',31);
		case 'save_service':
			$nSpecId=(isset($arData['spec_id'])) ?
				intval($arData['spec_id']) :
				0;

			if(empty($nSpecId))
				$obFormError->setFieldError('undefined specialization id',31);
		case 'save_specialization':
			$sName=isset($arData['name']) ?
				strip_tags($arData['name']) : false;

			if(empty($sName))
				$obFormError->setFieldError('undefined name',31);

			$obFormError->raise();
			$obCurrent=null;

			if($sAction=='save_specialization'){
				$obCurrent=new rs\VSpecs();
			}else if($sAction=='save_service'){
				$obCurrent=new rs\VServices();
			}else if($sAction=='save_methods'){
				$obCurrent=new rs\VMethods();
			}else{
				$obCurrent=new rs\VCServices();
			}

			$obRecord=$obCurrent->create();
			$obRecord['name']=$sName;

			if(!empty($nSpecId))
				$obRecord['specialization_id']=$nSpecId;

			if(!empty($nServiceId))
				$obRecord['service_id']=$nServiceId;

			$obRecord=$obCurrent->write($obRecord);
			$arResult[str_replace('save_','',$sAction)]=$obRecord->toArray();
			break;
		case 'get_service':
		case 'get_service_concreate':
			$nParentId=(isset($arData['id'])) ?
				intval($arData['id']) : 0;
			$bByMethod=!empty($arData['by_method']);

			if(empty($nParentId))
				throw new VException('server error');

			$obQuery=VDatabase::driver()->createQuery();
			$obQuery->builder()
				->from(($sAction=='get_service' ? 'estelife_services' : 'estelife_service_concreate'))
				->filter()
				->_eq(
					($sAction=='get_service' ?
						'specialization_id' :
						($bByMethod ? 'method_id' : 'service_id')),
					$nParentId
				);

			$arResult['list']=$obQuery->select()->all();

			if(!$bByMethod){
				$obQuery=VDatabase::driver()->createQuery();
				$obQuery->builder()
					->from('estelife_methods')
					->filter()
					->_eq(($sAction=='get_service' ? 'specialization_id' : 'service_id'),$nParentId);

				$arResult['methods']=$obQuery->select()->all();
				setcookie('el_sel_'.($sAction=='get_service' ? 'spec' : 'serv'),$nParentId,time()+86400,'/');
			}else
				setcookie('el_sel_method',$nParentId,time()+86400,'/');
			break;
		case 'get_cities':
		case 'get_metros':
        case 'get_countries':
			CModule::IncludeModule('iblock');

			$sTerm=(isset($arData['term'])) ?
				trim(strip_tags($arData['term'])) : '';
			$nCityId=isset($arData['city_id']) ?
				intval($arData['city_id']) : 0;

			$nCountryId = intval($arData['country_id']);
			
			if(strlen($sTerm)>=3 || $nCityId>0 || $nCountryId>0){

				if(!empty($sTerm))
					$arFilter=array(
						'NAME'=>'%'.$sTerm.'%'
					);
				else
					$arFilter=array();

				if($sAction=='get_cities'){
					$arFilter['IBLOCK_ID']=16;
                    if ($nCountryId != 0){
                        $arFilter['PROPERTY_COUNTRY'] = $nCountryId;
                    }
                }elseif($sAction == 'get_countries'){
                    $arFilter['IBLOCK_ID']=15;
				}else{
					if(empty($nCityId))
						throw new VException('do not select city');

					$arFilter['IBLOCK_ID']=17;
					$arFilter['PROPERTY_CITY']=$nCityId;
				}

				$obElement=new CIBlockElement();
				$obElements=$obElement->GetList(array('NAME'=>'asc'),$arFilter,false,false,array('ID','NAME'));
				$arElements=array();

				while($arElement=$obElements->Fetch())
					$arElements[$arElement['ID']]=array(
						'id'=>$arElement['ID'],
						'name'=>$arElement['NAME']
					);

				$arResult['list']=array_values($arElements);
			}else{
				$arResult['list']=array();
			}
			break;
		case 'address':
			$sTerm=(isset($arData['term'])) ?
				trim(strip_tags($arData['term'])) : '';
			$sCity=(isset($arData['city'])) ?
				trim(strip_tags($arData['city'])) : '';

			if(!empty($sTerm)){
				$arTerms=explode(' ',$sTerm);
				foreach($arTerms as $nKey=>&$sTerm){
					$sTerm=trim($sTerm);
					$sTerm=preg_replace('#^(дом|д|корпус|корп|к|строение|стр|литера|лит|л|ул|улица|аллея|проспект|пр|пр-т)\.?$#iu','',$sTerm);
					$sTerm=mb_strtolower($sTerm,'utf-8');

					if(empty($sTerm))
						unset($arTerms[$nKey]);
				}
				$sTerm=implode(' ',$arTerms);
			}

			if(strlen($sTerm)>3 && strlen($sCity)>3){
				$sApiKey='AIzaSyAZfcZn-KLKm52_chZk22TGMdooeDvMYfI';
				$obHttp=new VHttp('https://maps.googleapis.com/maps/api/place/autocomplete/json');
				$obHttp->setMethod(VHttp::GET);
				$obHttp->query(array(
					'input'=>'Россия, '.$sCity.', '.$sTerm,
					'sensor'=>'false',
					'key'=>$sApiKey,
					'types'=>'geocode',
					'language'=>'ru'
				));
				$arResponse=$obHttp->read();

				if(!empty($arResponse['body'])){
					$arData=json_decode($arResponse['body'],true);
					$arResult['list']=array();

					foreach($arData['predictions'] as $arValue){
						$arTemp=reset($arValue['terms']);

						if(empty($arTemp))
							continue;

						$sValue=$arTemp['value'];

						if(count($arValue['terms'])>4){
							$arTemp=$arValue['terms'][1];
							$sValue=$arTemp['value'].' '.$sValue;
						}

						$arResult['list'][]=array(
							'name'=>$sValue,
							'address'=>'Россия, '.$sCity.', '.$sValue
						);
					}
				}
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



echo json_encode($arResult);
