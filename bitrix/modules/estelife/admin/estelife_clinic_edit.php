<?php
use clinics as cl;
use core\exceptions as ex;
use core\types\VArray;
use core\types\VString;
use reference\services as rs;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/estelife/prolog.php");

ClearVars();
$FORM_RIGHT = $APPLICATION->GetGroupRight("estelife");

if($FORM_RIGHT<="D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

CModule::IncludeModule("estelife");
CModule::IncludeModule('iblock');

IncludeModuleLangFile(__FILE__);

define("HELP_FILE","estelife_list.php");
$ID=isset($_REQUEST['ID']) ?
	intval($_REQUEST['ID']) : 0;


$obClinics= \core\database\VDatabase::driver();

$obElements=new CIBlockElement();

if(!empty($ID) || !empty($CLINIC_ID)){
	if(empty($ID))
		$ID=$CLINIC_ID;

	$obQuery=$obClinics->createQuery();
	$obQuery->builder()->from('estelife_clinics','ec');
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
		->_from('ec','clinic_id')
		->_to('estelife_clinics','id','ecc');
	$obQuery->builder()
		->field('ct.ID','city_id')
		->field('mt.ID','metro_id')
		->field('ct.NAME','city_name')
		->field('mt.NAME','metro_name')
		->field('ec.id','id')
		->field('ec.active', 'active')
		->field('ec.dop_text','dop_text')
		->field('ec.recomended', 'recomended')
		->field('ec.address','address')
		->field('ec.latitude','latitude')
		->field('ec.longitude','longitude')
		->field('ec.name','name')
		->field('ec.preview_text','preview_text')
		->field('ec.detail_text','detail_text')
		->field('ec.clinic_id','clinic_id')
		->field('ec.logo_id','logo_id')
		->field('ecc.name', 'clinic_name')
		->field('ecc.id', 'clinic_id');

	$obQuery->builder()->filter()->_eq('ec.id',$ID);
	$obResult=$obQuery->select();

	$arResult['clinic']=$obResult->assoc();


	$obQuery=$obClinics->createQuery();
	$obQuery->builder()->from('estelife_clinic_services','ecs');
	$obJoin=$obQuery->builder()
		->sort('ecs.id','asc')
		->join();
	$obJoin->_left()
		->_from('ecs','specialization_id')
		->_to('estelife_specializations','id','es');
	$obJoin->_left()
		->_from('ecs','service_id')
		->_to('estelife_services','id','esr');

	$obJoin->_left()
		->_from('ecs','service_concreate_id')
		->_to('estelife_service_concreate','id','esc');

	$obQuery->builder()->filter()->_eq('ecs.clinic_id',$arResult['clinic']['id']);
	$obQuery->builder()
		->field('es.id','specialization_id')
		->field('es.name','specialization_name')
		->field('esr.id','service_id')
		->field('esr.name','service_name')
		->field('esc.id','service_concreate_id')
		->field('esc.name','service_concreate_name')
		->field('ecs.price_from','price_from');
	$obResult=$obQuery->select();
	$arResult['clinic']['services']=$obResult->all();

	$obQuery=$obClinics->createQuery();
	$obQuery->builder()->from('estelife_clinic_contacts');
	$obQuery->builder()->filter()->_eq('clinic_id',$arResult['clinic']['id']);
	$obResult=$obQuery->select();
	$arContacts=$obResult->all();

	foreach($arContacts as $arContact){
		if($arContact['type']=='web')
			$arResult['clinic']['site']=$arContact['value'];
		else if($arContact['type']=='email')
			$arResult['clinic']['email']=$arContact['value'];
		else if($arContact['type']=='phone')
			$arResult['clinic']['phones'][]=$arContact['value'];
	}

	$obQuery=$obClinics->createQuery();
	$obQuery->builder()->from('estelife_busy_hours');
	$obQuery->builder()->filter()->_eq('clinic_id',$arResult['clinic']['id']);
	$obResult=$obQuery->select();
	$arHours=$obResult->all();
	$arTemp=array(
		1=>array(),
		2=>array(),
		3=>array(),
		4=>array(),
		5=>array(),
		6=>array(),
		7=>array()
	);

	if(!empty($arHours)){
		foreach($arHours as $arHour){
			if($arHour['day_off']==1){
				continue;
			}
			$arHour['from']+=1;
			$arTemp[$arHour['day']]=range($arHour['from'],$arHour['to']);
		}
	}

	$arResult['clinic']['busy_hours']=$arTemp;

	$obQuery=$obClinics->createQuery();
	$obQuery->builder()->from('estelife_clinic_photos');
	$obQuery->builder()->filter()->_eq('clinic_id',$arResult['clinic']['id']);
	$obResult=$obQuery->select();
	$arResult['clinic']['photos']=$obResult->all();


	$obQuery=$obClinics->createQuery();
	$obQuery->builder()->from('estelife_clinic_akzii','eca');
	$obJoin=$obQuery->builder()->join();
	$obJoin->_left()
		->_from('eca','akzii_id')
		->_to('estelife_akzii','id','ea');
	$obQuery->builder()->filter()
		->_eq('eca.clinic_id', $arResult['clinic']['id']);
	$obQuery->builder()
		->field('ea.id','id')
		->field('ea.name','name');
	$obResult=$obQuery->select();
	$arResult['clinic']['akzii']=$obResult->all();

	if(empty($CLINIC_ID)){
		$obQuery=$obClinics->createQuery();
		$obQuery->builder()
			->from('estelife_clinics')
			->field('id')
			->field('name')
			->filter()
			->_eq('clinic_id',$arResult['clinic']['id']);
		$obResult=$obQuery->select();
		$arResult['clinic']['fill']=$obResult->all();
	}

	$obQuery=$obClinics->createQuery();
	$obQuery->builder()
		->from('estelife_clinic_pays')
		->field('id')
		->field('name')
		->filter()
		->_eq('clinic_id',$arResult['clinic']['id']);
	$obResult=$obQuery->select();
	$arResult['clinic']['pays']=$obResult->all();


	//Получение статей
	$obQuery=$obClinics->createQuery();
	$obQuery->builder()->from('estelife_clinic_articles', 'eca');
	$obJoin=$obQuery->builder()->join();
	$obJoin->_left()
		->_from('eca','article_id')
		->_to('iblock_element','ID','ie');
	$obQuery->builder()
		->field('ie.ID', 'article_id')
		->field('ie.NAME', 'article_name')
		->filter()
		->_eq('clinic_id',$arResult['clinic']['id']);
	$obResult=$obQuery->select();
	$arResult['clinic']['articles']=$obResult->all();
}

if(empty($ID) || !empty($CLINIC_ID)){
	if(empty($CLINIC_ID)){
		if(!empty($_COOKIE['el_sel_city'])){
			$nCity=intval($_COOKIE['el_sel_city']);
			$obResult=$obElements->GetByID($nCity);

			if($arCity=$obResult->Fetch()){
				$arResult['clinic']['city_id']=$arCity['ID'];
				$arResult['clinic']['city_name']=$arCity['NAME'];
			}
		}

		if(!empty($_COOKIE['el_sel_metro'])){
			$nMetro=intval($_COOKIE['el_sel_metro']);
			$obResult=$obElements->GetByID($nMetro);

			if($arMetro=$obResult->Fetch()){
				$arResult['clinic']['metro_id']=$arMetro['ID'];
				$arResult['clinic']['metro_name']=$arMetro['NAME'];
			}
		}
	}else{
		$_COOKIE['el_sel_clinic']=$CLINIC_ID;
	}

	if(!empty($_COOKIE['el_sel_clinic'])){
		$obQuery=$obClinics->createQuery();
		$obQuery->builder()
			->from('estelife_clinics')
			->field('id')
			->field('name')
			->filter()
			->_eq('id',intval($_COOKIE['el_sel_clinic']));
		$obResult=$obQuery->select();
		$arClinic=$obResult->assoc();

		if(!empty($arClinic)){

			if(empty($CLINIC_ID)){
				$arResult['clinic']['sel_clinic']=array(
					'name'=>$arClinic['name'],
					'id'=>$arClinic['id']
				);
			}else{
				$arResult['clinic']['clinic_name']=$arClinic['name'];
				$arResult['clinic']['clinic_id']=$arClinic['id'];
			}
		}
	}
}

if($_SERVER['REQUEST_METHOD']=='POST'){
	$obPost=new VArray($_POST);
	$obError=new ex\VFormException();

	try{
		if($obPost->blank('name'))
			$obError->setFieldError('NAME_NOT_FILL','name');

		if($obPost->blank('city_id'))
			$obError->setFieldError('CITY_NOT_FILL','city_id');
		else{
			$obCity=$obElements->GetByID($obPost->one('city_id'));

			if($obCity->SelectedRowsCount()<=0)
				$obError->setFieldError('CITY_NOT_FOUND','city_id');
		}

		if(!$obPost->blank('metro_id')){
			$obMetro=$obElements->GetByID($obPost->one('metro_id'));

			if($obMetro->SelectedRowsCount()<=0)
				$obError->setFieldError('METRO_NOT_FOUND','metro_id');
		}

		if($obPost->blank('address'))
			$obError->setFieldError('ADDRESS_NOT_FILL', 'address');

		if($obPost->blank('phones'))
			$obError->setFieldError('PHONE_NOT_FILL','phones');
		else{
			$arPhones=$obPost->one('phones');
			$bFound=false;

			foreach($arPhones as $sPhone){
				if(!empty($sPhone)){
					$bFound=true;
					break;
				}
			}

			if(!$bFound)
				$obError->setFieldError('PHONE_NOT_FILL','phones');
		}


		$obError->raise();

		$obQueryClinic = $obClinics->createQuery();
		$obQueryClinic->builder()->from('estelife_clinics')
			->value('name', trim(htmlentities($obPost->one('name'),ENT_QUOTES,'utf-8')))
			->value('detail_text', htmlentities($obPost->one('detail_text'),ENT_QUOTES,'utf-8'))
			->value('preview_text', htmlentities($obPost->one('preview_text'),ENT_QUOTES,'utf-8'))
			->value('dop_text', htmlentities($obPost->one('dop_text'),ENT_QUOTES,'utf-8'))
			->value('active', $obPost->one('active'))
			->value('recomended', $obPost->one('recomended'))
			->value('city_id', intval($obPost->one('city_id')))
			->value('metro_id', intval($obPost->one('metro_id')))
			->value('address', htmlentities($obPost->one('address'),ENT_QUOTES,'utf-8'))
			->value('latitude', doubleval($obPost->one('latitude')))
			->value('longitude', doubleval($obPost->one('longitude')))
			->value('clinic_id', intval($obPost->one('clinic_id',0)));

		if(!empty($_FILES['logo'])){
			$arImage=$_FILES['logo'];
			$arImage['old_file']=$obRecord['logo_id'];
			$arImage['module']='estelife';
			$arImage['del']=$logo_del;

			if(strlen($arImage["name"])>0 || strlen($arImage["del"])>0){
				$nImageId=CFile::SaveFile($arImage, "estelife");
				$obQueryClinic->builder()
					->value('logo_id', intval($nImageId));
			}
		}


		if (!empty($ID)){
			$obQueryClinic->builder()->filter()
				->_eq('id',$ID);
			$obQueryClinic->update();
			$idClinic = $ID;
		}else{
			$idClinic = $obQueryClinic->insert()->insertId();
			$ID =$idClinic;

		}


		setcookie('el_sel_city',$obPost->one('city_id'),time()+86400,'/');

		if(!empty($obRecord['metro_id'])){
			setcookie('el_sel_metro',$obPost->one('metro_id'),time()+86400,'/');
		}else{
			setcookie('el_sel_metro',0,time()-86400,'/');
		}

		if(!empty($ID)){
			$obQuery = $obClinics->createQuery();
			$obQuery->builder()->from('estelife_clinic_services');
			$obQuery->builder()->filter()->_eq('clinic_id',$idClinic);
			$obQuery->delete();

			$obQuery = $obClinics->createQuery();
			$obQuery->builder()->from('estelife_clinic_contacts');
			$obQuery->builder()->filter()->_eq('clinic_id',$idClinic);
			$obQuery->delete();

			$obQuery = $obClinics->createQuery();
			$obQuery->builder()->from('estelife_busy_hours');
			$obQuery->builder()->filter()->_eq('clinic_id',$idClinic);
			$obQuery->delete();

			$obQuery = $obClinics->createQuery();
			$obQuery->builder()->from('estelife_clinic_articles');
			$obQuery->builder()->filter()->_eq('clinic_id',$idClinic);
			$obQuery->delete();

			$obQuery=$obClinics->createQuery();
			$obQuery->builder()
				->from('estelife_clinic_pays')
				->filter()
				->_eq('clinic_id',$idClinic);
			$obQuery->delete();
		}

		//Пишем ссылки на статьи
		if($arArticles=$obPost->one('articles')){
			$arArticles=$arArticles['article_id'];

			foreach($arArticles as $nArticle){
				$nArticle=intval($nArticle);

				if(empty($nArticle))
					continue;

				$obQuery->builder()
					->from('estelife_clinic_articles')
					->value('article_id',$nArticle)
					->value('clinic_id',$ID);
				$obQuery->insert();
			}
		}



		// Пишем ссылки на услуги
		if(!$obPost->blank('services')){
			$arServices=$obPost->one('services');
			$obQuery=$obClinics->createQuery();
			$obQuery->builder()
				->from('estelife_service_concreate')
				->filter()->_in('id',$arServices);
			$obResult=$obQuery->select();
			$arServices=$obResult->all();
			$arPrices=$obPost->one('service_price',array());

			foreach($arServices as $arService){
				$obQuery=$obClinics->createQuery();
				$obQuery->builder()->from('estelife_clinic_services')
					->value('specialization_id', $arService['specialization_id'])
					->value('service_id', $arService['service_id'])
					->value('service_concreate_id', $arService['id'])
					->value('method_id', $arService['method_id'])
					->value('clinic_id', $idClinic);

				if(!empty($arPrices[$arService['id']]))
					$obQuery->builder()->value('price_from', $arPrices[$arService['id']]);

				$idServiceClinic = $obQuery->insert();

			}
		}

		if(!$obPost->blank('site')){
			$obQuery=$obClinics->createQuery();
			$obQuery->builder()->from('estelife_clinic_contacts')
				->value('type', 'web')
				->value('value', $obPost->one('site'))
				->value('clinic_id', $idClinic);
			$idWebClinic = $obQuery->insert();
		}

		if(!$obPost->blank('email')){
			$obQuery=$obClinics->createQuery();
			$obQuery->builder()->from('estelife_clinic_contacts')
				->value('type', 'email')
				->value('value', $obPost->one('email'))
				->value('clinic_id', $idClinic);
			$idEmailClinic = $obQuery->insert();

		}

		if(!$obPost->blank('phones')){
			$arPhones=$obPost->one('phones');
			foreach($arPhones as $sPhone){
				if(empty($sPhone))
					continue;

				$sPhone=preg_replace('#[^\d]*#','',$sPhone);

				if(strlen($sPhone)<11)
					continue;

				$obQuery=$obClinics->createQuery();
				$obQuery->builder()->from('estelife_clinic_contacts')
					->value('type', 'phone')
					->value('value', $sPhone)
					->value('clinic_id', $idClinic);
				$idPhoneClinic = $obQuery->insert();
			}
		}

		//Часы работы
		if(!$obPost->blank('busy_hours')){
			$arHours=$obPost->one('busy_hours');
			$arDefaultDays=range(1,7);

			foreach($arHours as $nDay=>$arHour){
				$obQuery=$obClinics->createQuery();
				$obQuery->builder()->from('estelife_busy_hours')
					->value('day', $nDay)
					->value('from', min($arHour)-1)
					->value('to',max($arHour))
					->value('clinic_id', $idClinic);
				$idHourClinic = $obQuery->insert();

				if($nKey=array_search($nDay,$arDefaultDays))
					unset($arDefaultDays[$nKey]);
			}

			if(!empty($arDefaultDays)){
				foreach($arDefaultDays as $nDay){

					$obQuery=$obClinics->createQuery();
					$obQuery->builder()->from('estelife_busy_hours')
						->value('day', $nDay)
						->value('from', 0)
						->value('to', 49)
						->value('day_off', 1)
						->value('clinic_id', $idClinic);
					$idHourClinic = $obQuery->insert();
				}
			}
		}

		$arPost=$obPost->all();
		foreach($arPost as $sKey=>$mValue){
			if(preg_match('#^photo_descriptions_([0-9]+)$#i',$sKey,$arMatches)){
				try{

					$obQuery=$obClinics->createQuery();
					$obQuery->builder()->from('estelife_clinic_photos')
						->filter()
						->_eq('id', $arMatches[1]);
					$arPhoto = $obQuery->select()->assoc();

					$obQuery=$obClinics->createQuery();
					$obQuery->builder()->from('estelife_clinic_photos')
						->value('description', htmlentities($mValue,ENT_QUOTES,'utf-8'));
					$obQuery->builder()->filter()
						->_eq('id',$arPhoto['id']);
					$obQuery->update();

				}catch(\core\database\exceptions\VCollectionException $e){}
			}
		}

		if(!$obPost->blank('photo_deleted')){
			$arDeleted=$obPost->one('photo_deleted');
			foreach($arDeleted as $nDelete){
				try{
					$obQuery=$obClinics->createQuery();
					$obQuery->builder()->from('estelife_clinic_photos')
						->filter()
						->_eq('id',$nDelete);
					$arPhoto = $obQuery->select()->assoc();
					CFile::Delete($obPhoto['original']);

					$obQuery=$obClinics->createQuery();
					$obQuery->builder()->from('estelife_clinic_photos');
					$obQuery->builder()->filter()
						->_eq('id',$arPhoto['id']);
					$obQuery->delete();

				}catch(\core\database\exceptions\VCollectionException $e){}
			}
		}

		if(!empty($_FILES['gallery'])){
			$arFiles=$_FILES['gallery'];
			foreach($arFiles['name'] as $nKey=>$sName){
				if(empty($arFiles['tmp_name'][$nKey]))
					continue;

				$arImage=array(
					'name'=>$sName,
					'tmp_name'=>$arFiles['tmp_name'][$nKey],
					'type'=>$arFiles['type'][$nKey],
					'error'=>$arFiles['error'][$nKey],
					'size'=>$arFiles['size'][$nKey]
				);

				$nImageId=CFile::SaveFile($arImage, "estelife");
				$nImageId=intval($nImageId);

				if(empty($nImageId))
					continue;


				$obQuery=$obClinics->createQuery();
				$obQuery->builder()->from('estelife_clinic_photos')
					->value('original', $nImageId)
					->value('clinic_id', $idClinic);
				$idgalleryClinic = $obQuery->insert();

			}
		}

		if(!$obPost->blank('pays')){
			$arPays=$obPost->one('pays');
			foreach($arPays as $sPay){
				if (empty($sPay))
					continue;

				$obQuery=$obClinics->createQuery();
				$obQuery->builder()
					->from('estelife_clinic_pays')
					->value('name',$sPay)
					->value('clinic_id',$idClinic);
				$obQuery->insert();
			}
		}

		if(!$obPost->blank('delete_akzii') && !$obPost->blank('delete_akzii_proccess')){
			$arDeleted=$obPost->one('delete_akzii');

			$obQuery=$obClinics->createQuery();
			$obQuery->builder()
				->from('estelife_akzii')
				->filter()
				->_in('id',$arDeleted);
			$obQuery->delete();
		}

		if(!$obPost->blank('delete_fill') && !$obPost->blank('delete_fill_proccess')){
			$arDeleted=$obPost->one('delete_fill');

			$obQuery=$obClinics->createQuery();
			$obQuery->builder()
				->from('estelife_clinics')
				->filter()
				->_in('id',$arDeleted);
			$obQuery->delete();
		}


		if(!empty($idClinic)){
			if(!empty($idClinic)){
				setcookie('el_sel_clinic',$idClinic,time()+86400,'/');
			}else{
				setcookie('el_sel_clinic',$idClinic,time()+86400,'/');
			}

			if(!$obPost->blank('save')){
				if(!isset($REDIRECT_TO_CLINIC))
					LocalRedirect('/bitrix/admin/estelife_clinic_list.php?lang='.LANGUAGE_ID);
				else
					LocalRedirect('/bitrix/admin/estelife_clinic_edit.php?lang='.LANGUAGE_ID.'&ID='.$REDIRECT_TO_CLINIC.'#tab10');
			}else
				LocalRedirect('/bitrix/admin/estelife_clinic_edit.php?lang='.LANGUAGE_ID.'&ID='.$idClinic);
		}
	}catch(ex\VFormException $e){
		$arResult['error']=array(
			'text'=>$e->getMessage(),
			'code'=>11
		);
		$arResult['error']['fields']=$e->getFieldErrors();
	}catch(ex\VException $e){
		$arResult['error']=array(
			'text'=>$e->getMessage(),
			'code'=>$e->getCode()
		);
	}
}

$arResult['reference']=array(
	'sel_spec'=>0,
	'sel_service'=>0
);
$obQuery = $obClinics->createQuery();
$obQuery->builder()->from('estelife_specializations');

$arResult['reference']['specs']=$obQuery->select()->all();

if(isset($_COOKIE['el_sel_spec'])){
	$obQuery = $obClinics->createQuery();
	$obQuery->builder()->from('estelife_services')
		->filter()
		->_eq('specialization_id',$_COOKIE['el_sel_spec']);
	$arResult['reference']['services']=$obQuery->select()->all();
	$arResult['reference']['sel_spec']=$_COOKIE['el_sel_spec'];


	$obQuery = $obClinics->createQuery();
	$obQuery->builder()->from('estelife_methods')
		->filter()
		->_eq('specialization_id',$_COOKIE['el_sel_spec']);
	$obCServicesFilter=null;

	if(isset($_COOKIE['el_sel_serv'])){

		$obServicesFilter = $obClinics->createQuery();
		$obServicesFilter->builder()->from('estelife_services')
			->filter()
			->_eq('service_id',$_COOKIE['el_sel_serv'])
			->_eq('specialization_id',$_COOKIE['el_sel_spec']);

		$obQuery->builder()->filter()
			->_eq('service_id',$_COOKIE['el_sel_serv']);
	}

	if(isset($_COOKIE['el_sel_method'])){
		$obServicesFilter=(!$obServicesFilter) ?
			$obServicesFilter=$obClinics->createQuery() :
			$obServicesFilter;

		$obServicesFilter->builder()->from('estelife_services')->filter()
			->_eq('method_id',$_COOKIE['el_sel_method']);
		$arResult['reference']['sel_method']=$_COOKIE['el_sel_method'];
	}

	if($obCServicesFilter){
		$arResult['reference']['cservices']=$obServicesFilter->select()->all();
		$arResult['reference']['sel_service']=$_COOKIE['el_sel_serv'];
	}

	$arMethods=$obQuery->select()->all();

	if(!empty($arMethods))
		$arResult['reference']['methods']=$arMethods;
}

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("ESTELIFE_T_BASE"), "ICON" => "estelife_r_base", "TITLE" => GetMessage("ESTELIFE_T_BASE")),
	array("DIV" => "edit2", "TAB" => GetMessage("ESTELIFE_T_PREVIEW_TEXT"), "ICON" => "estelife_r_base", "TITLE" => GetMessage("ESTELIFE_T_PREVIEW_TEXT")),
	array("DIV" => "edit3", "TAB" => GetMessage("ESTELIFE_T_DETAIL_TEXT"), "ICON" => "estelife_r_base", "TITLE" => GetMessage("ESTELIFE_T_DETAIL_TEXT")),
	array("DIV" => "edit4", "TAB" => GetMessage("ESTELIFE_T_SERVICES"), "ICON" => "estelife_r_base", "TITLE" => GetMessage("ESTELIFE_T_SERVICES")),
	array("DIV" => "edit11", "TAB" => GetMessage("ESTELIFE_T_PAY"), "ICON" => "estelife_r_base", "TITLE" => GetMessage("ESTELIFE_T_PAY")),
	array("DIV" => "edit5", "TAB" => GetMessage("ESTELIFE_T_BUSY_HOURS"), "ICON" => "estelife_r_base", "TITLE" => GetMessage("ESTELIFE_T_BUSY_HOURS")),
	array("DIV" => "edit6", "TAB" => GetMessage("ESTELIFE_T_CONTACTS"), "ICON" => "estelife_r_base", "TITLE" => GetMessage("ESTELIFE_T_CONTACTS")),
	array("DIV" => "edit8", "TAB" => GetMessage("ESTELIFE_T_GALLERIES"), "ICON" => "estelife_r_base", "TITLE" => GetMessage("ESTELIFE_T_GALLERIES")),
	array("DIV" => "edit9", "TAB" => GetMessage("ESTELIFE_T_AKZII"), "ICON" => "estelife_r_base", "TITLE" => GetMessage("ESTELIFE_T_AKZII")),
	array("DIV" => "edit12", "TAB" => GetMessage("ESTELIFE_T_ARTICLES"), "ICON" => "estelife_r_base", "TITLE" => GetMessage("ESTELIFE_T_ARTICLES"))
);

if(empty($CLINIC_ID)){
	$aTabs[]=array("DIV" => "edit10", "TAB" => GetMessage("ESTELIFE_T_CLINICS"), "ICON" => "estelife_r_base", "TITLE" => GetMessage("ESTELIFE_T_CLINICS"));
}

$tabControl = new CAdminTabControl("estelife_service_concreate_".$ID, $aTabs, true, true);
$message = null;

//===== Тут будем делать сохрпанение и подготовку данных

$APPLICATION->SetTitle(GetMessage('ESTELIFE_CREATE_TITLE'));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

if(!empty($arResult['error']['text'])){
	$arMessages=array(
		$arResult['error']['text'].' ['.$arResult['error']['code'].']'
	);

	if(isset($arResult['error']['fields'])){
		foreach($arResult['error']['fields'] as $sField=>$sError)
			$arMessages[]=GetMessage('ERROR_FIELD_FILL').': '.GetMessage($sError);
	}

	CAdminMessage::ShowOldStyleError(implode('<br />',$arMessages));

	if(!empty($_POST)){
		foreach($_POST as $sKey=>$sValue)
			$arResult['clinic'][$sKey]=$sValue;
	}
}
?>
	<script type="text/javascript" src="/bitrix/js/estelife/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/jquery-ui-1.10.3.custom.min.js"></script>
	<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyAZfcZn-KLKm52_chZk22TGMdooeDvMYfI&sensor=false"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/vMapStyle.js"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/vMap.js"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/jquery.damnUploader.js"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/estelife.js"></script>

	<form name="estelife_spec" method="POST" action="<?echo $APPLICATION->GetCurPage()?>" enctype="multipart/form-data">
		<?=bitrix_sessid_post()?>
		<input type="hidden" name="ID" value=<?=(empty($CLINIC_ID)) ? $ID : 0?> />
		<?php if(!empty($CLINIC_ID) || !empty($arResult['clinic']['clinic_id']) || isset($REDIRECT_TO_CLINIC)): ?>
			<input type="hidden" name="REDIRECT_TO_CLINIC" value="<?=(isset($REDIRECT_TO_CLINIC)) ? $REDIRECT_TO_CLINIC : (!empty($arResult['clinic']['clinic_id']) ? $arResult['clinic']['clinic_id']  : $CLINIC_ID)?>" />
		<?php endif; ?>
		<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>" />
		<?php
		$tabControl->Begin();
		$tabControl->BeginNextTab()
		?>

		<tr class="adm-detail-required-field">
			<td width="40%"><?=GetMessage("ESTELIFE_F_TITLE")?></td>
			<td width="60%"><input type="text" name="name" size="60" maxlength="255" value="<?=$arResult['clinic']['name']?>"></td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_ACTIVE")?></td>
			<td width="60%">
				<ul class="estelife-checklist">
					<li>
						<label for="type_1"><input type="checkbox" name="active" id="type_1" value="1"<?=(($arResult['clinic']['active'] == 1) ? ' checked="true"' : '')?> /></label>
					</li>
				</ul>
			</td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_RECOMENDED")?></td>
			<td width="60%">
				<ul class="estelife-checklist">
					<li>
						<label for="type_2"><input type="checkbox" name="recomended" id="type_2" value="1"<?=(($arResult['clinic']['recomended'] == 1) ? ' checked="true"' : '')?> /></label>
					</li>
				</ul>
			</td>
		</tr>
		<tr class="adm-detail-required-field">
			<td width="40%"><?=GetMessage("ESTELIFE_F_CITY")?></td>
			<td width="60%">
				<input type="hidden" name="city_id" value="<?=$arResult['clinic']['city_id']?>" />
				<input type="text" name="city_name" data-input="city_id" value="<?=$arResult['clinic']['city_name']?>" />
			</td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_METRO")?></td>
			<td width="60%">
				<input type="hidden" name="metro_id" value="<?=$arResult['clinic']['metro_id']?>" />
				<input type="text"<?=(empty($arResult['clinic']['metro_id']) ? ' readonly="readonly"' : '')?> name="metro_name" data-input="metro_id" value="<?=$arResult['clinic']['metro_name']?>" />
			</td>
		</tr>
		<tr class="adm-detail-required-field">
			<td width="40%"><?=GetMessage("ESTELIFE_F_ADDRESS")?></td>
			<td width="60%">
				<input type="hidden" name="latitude" value="<?=$arResult['clinic']['latitude']?>" />
				<input type="hidden" name="longitude" value="<?=$arResult['clinic']['longitude']?>" />
				<input type="text"<?=(empty($arResult['clinic']['city_id']) ? ' readonly="readonly"' : '')?> name="address" size="60" maxlength="255" value="<?=$arResult['clinic']['address']?>">
				<div class="gmap"></div>
			</td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_CLINIC")?></td>
			<td width="60%" class="estelife-use">
				<input type="hidden" name="clinic_id" value="<?=$arResult['clinic']['clinic_id']?>" />
				<input type="text" name="clinic_name" data-input="clinic_id" value="<?=$arResult['clinic']['clinic_name']?>" />
				<?php if(!empty($arResult['clinic']['sel_clinic'])): ?>
					<div class="use">
						<span data-input="clinic_id"><?=$arResult['clinic']['sel_clinic']['id']?></span>
						<span data-input="clinic_name"><?=$arResult['clinic']['sel_clinic']['name']?></span>
						<a href="javascript:void(0)">Использовать: <?=$arResult['clinic']['sel_clinic']['name']?></a>
					</div>
				<?php endif; ?>
			</td>
		</tr>
		<tr id="tr_logo">
			<td width="40%"><?=GetMessage("ESTELIFE_F_LOGO")?></td>
			<td width="60%">
				<?echo CFileInput::Show("logo", $arResult['clinic']['logo_id'],
					array(
						"IMAGE" => "Y",
						"PATH" => "Y",
						"FILE_SIZE" => "Y",
						"DIMENSIONS" => "Y",
						"IMAGE_POPUP" => "Y",
						"MAX_SIZE" => array(
							"W" => 100,
							"H" => 100
						)
					), array(
						'upload' => true,
						'medialib' => true,
						'file_dialog' => true,
						'cloud' => true,
						'del' => true,
						'description' => false
					)
				);
				?>
			</td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_DOP")?></td>
			<td width="60%">
				<textarea name="dop_text" rows="12" style="width:70%"><?=$arResult['clinic']['dop_text']?></textarea>
			</td>
		</tr>

		<?
		$tabControl->BeginNextTab()
		?>

		<tr id="tr_preview_text_editor">
			<td colspan="2" align="center">
				<?CFileMan::AddHTMLEditorFrame(
					"preview_text",
					$arResult['clinic']['preview_text'],
					"preview_text_type",
					$str_preview_text_type,
					array(
						'height' => 450,
						'width' => '100%'
					),
					"N",
					0,
					"",
					"",
					$arIBlock["LID"],
					true,
					false,
					array(
						'toolbarConfig' => CFileman::GetEditorToolbarConfig("iblock_".(defined('BX_PUBLIC_MODE') && BX_PUBLIC_MODE == 1 ? 'public' : 'admin')),
						'saveEditorKey' => $IBLOCK_ID
					)
				);?>
			</td>
		</tr>

		<?
		$tabControl->BeginNextTab()
		?>

		<tr id="tr_detail_text_editor">
			<td colspan="2" align="center">
				<?CFileMan::AddHTMLEditorFrame(
					"detail_text",
					$arResult['clinic']['detail_text'],
					"detail_text_type",
					$str_detail_text_type,
					array(
						'height' => 450,
						'width' => '100%'
					),
					"N",
					0,
					"",
					"",
					$arIBlock["LID"],
					true,
					false,
					array(
						'toolbarConfig' => CFileman::GetEditorToolbarConfig("iblock_".(defined('BX_PUBLIC_MODE') && BX_PUBLIC_MODE == 1 ? 'public' : 'admin')),
						'saveEditorKey' => $IBLOCK_ID
					)
				);?>
			</td>
		</tr>

		<?
		$tabControl->BeginNextTab()
		?>

		<tr class="adm-detail-required-field estelife-services">
			<td colspan="2">
				<ul class="selected<?=(empty($arResult['clinic']['services'])) ? ' hidden': ''?>">
					<?php if(!empty($arResult['clinic']['services'])): ?>
						<?php foreach($arResult['clinic']['services'] as $arService): ?>
							<li data-spec="<?=$arService['specialization_id']?>" data-service="<?=$arService['service_id']?>" data-cservice="<?=$arService['service_concreate_id']?>">
								<span><?=$arService['specialization_name']?>, <?=$arService['service_name']?>, <?=$arService['service_concreate_name']?></span>
								<input type="text" name="service_price[<?=$arService['service_concreate_id']?>]" value="<?=$arService['price_from']?>" />
								<input type="hidden" name="services[]" value="<?=$arService['service_concreate_id']?>" />
								<a href="#">x</a>
							</li>
						<?php endforeach; ?>
					<?php endif; ?>
				</ul>
				<div class="lists">
					<ul class="specialization">
						<?php foreach($arResult['reference']['specs'] as $arSpecs): ?>
							<li><a href="#" data-id="<?=$arSpecs['id']?>"<?=$arResult['reference']['sel_spec']==$arSpecs['id'] ? ' class="active"' : ''?>><?=$arSpecs['name']?></a></li>
						<?php endforeach; ?>
					</ul>
					<ul class="service">
						<?php foreach($arResult['reference']['services'] as $arServ): ?>
							<li><a href="#" data-id="<?=$arServ['id']?>"<?=$arResult['reference']['sel_service']==$arServ['id'] ? ' class="active"' : ''?>><?=$arServ['name']?></a></li>
						<?php endforeach; ?>
					</ul>
					<ul class="methods">
						<?php foreach($arResult['reference']['methods'] as $arMethod): ?>
							<li><a href="#" data-id="<?=$arMethod['id']?>"<?=$arResult['reference']['sel_method']==$arMethod['id'] ? ' class="active"' : ''?>><?=$arMethod['name']?></a></li>
						<?php endforeach; ?>
					</ul>
					<ul class="service_concreate">
						<?php foreach($arResult['reference']['cservices'] as $arCServ): ?>
							<li><a href="#" data-id="<?=$arCServ['id']?>"><?=$arCServ['name']?><input type="text" name="service_price[<?=$arCServ['id']?>]" value=""></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
				<div class="forms">
					<div data-action="specialization">
						<a href="/bitrix/admin/estelife_specialization_edit.php?backurl=/bitrix/admin/estelife_clinic_edit.php&lang=<?=LANGUAGE_ID?>" class="adm-btn adm-btn-save adm-btn-add" target="_blank">Добавить специализацию</a>
					</div>
					<div data-action="service"<?=(!isset($arResult['reference']['services'])) ? ' class="hidden"' : ''?>>
						<a href="/bitrix/admin/estelife_service_edit.php?backurl=/bitrix/admin/estelife_clinic_edit.php&lang=<?=LANGUAGE_ID?>" class="adm-btn adm-btn-save adm-btn-add" target="_blank">Добавить тип услуги</a>
					</div>
					<div data-action="methods"<?=(!isset($arResult['reference']['methods'])) ? ' class="hidden"' : ''?>>
						<a href="/bitrix/admin/estelife_method_edit.php?backurl=/bitrix/admin/estelife_clinic_edit.php&lang=<?=LANGUAGE_ID?>" class="adm-btn adm-btn-save adm-btn-add" target="_blank">Добавить методику</a>
					</div>
					<div class="last<?=(!isset($arResult['reference']['cservices'])) ? ' hidden' : ''?>" data-action="service_concreate">
						<a href="/bitrix/admin/estelife_service_concreate_edit.php?backurl=/bitrix/admin/estelife_clinic_edit.php&lang=<?=LANGUAGE_ID?>" class="adm-btn adm-btn-save adm-btn-add" target="_blank">Добавить вид услуги</a>
					</div>
				</div>
				<div class="cl"></div>
			</td>
		</tr>

		<?
		$tabControl->BeginNextTab()
		?>

		<?php if(!empty($arResult['clinic']['pays'])): ?>
		<?php foreach($arResult['clinic']['pays'] as $arPay): ?>
			<tr>
				<td width="40%"><?=GetMessage("ESTELIFE_F_PAY_TYPE")?></td>
				<td width="60%"><input type="text" name="pays[]" size="60" maxlength="255" value="<?=$arPay['name']?>"><a href="#" class="estelife-more estelife-btn adm-btn adm-btn-delete estelife-delete"></a></td>
			</tr>
		<?php endforeach; ?>
		<?php endif; ?>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_PAY_TYPE")?></td>
			<td width="60%"><input type="text" name="pays[]" size="60" maxlength="255" value=""><a href="#" class="estelife-more estelife-btn adm-btn adm-btn-save">+</a></td>
		</tr>

		<?
		$tabControl->BeginNextTab()
		?>

		<tr>
			<td colspan="2">
				<!--<table class="estelife-busy-hours">
					<thead>
						<tr>
							<th class="all">Все</th>
							<th>00<sup>00</sup><br />01<sup>00</sup></th>
							<th>01<sup>00</sup><br />02<sup>00</sup></th>
							<th>02<sup>00</sup><br />03<sup>00</sup></th>
							<th>03<sup>00</sup><br />04<sup>00</sup></th>
							<th>04<sup>00</sup><br />05<sup>00</sup></th>
							<th>05<sup>00</sup><br />06<sup>00</sup></th>
							<th>06<sup>00</sup><br />07<sup>00</sup></th>
							<th>07<sup>00</sup><br />08<sup>00</sup></th>
							<th>08<sup>00</sup><br />09<sup>00</sup></th>
							<th>09<sup>00</sup><br />10<sup>00</sup></th>
							<th>10<sup>00</sup><br />11<sup>00</sup></th>
							<th>11<sup>00</sup><br />12<sup>00</sup></th>
							<th>12<sup>00</sup><br />13<sup>00</sup></th>
							<th>13<sup>00</sup><br />14<sup>00</sup></th>
							<th>14<sup>00</sup><br />15<sup>00</sup></th>
							<th>15<sup>00</sup><br />16<sup>00</sup></th>
							<th>16<sup>00</sup><br />17<sup>00</sup></th>
							<th>17<sup>00</sup><br />18<sup>00</sup></th>
							<th>18<sup>00</sup><br />19<sup>00</sup></th>
							<th>19<sup>00</sup><br />20<sup>00</sup></th>
							<th>20<sup>00</sup><br />21<sup>00</sup></th>
							<th>21<sup>00</sup><br />22<sup>00</sup></th>
							<th>22<sup>00</sup><br />23<sup>00</sup></th>
							<th>23<sup>00</sup><br />00<sup>00</sup></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="h">Пн</td>
							<td><input type="checkbox" name="busy_hours[1][]"<?/*=(in_array(1,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''*/?> value="1" /></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?/*=(in_array(2,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''*/?> value="2" /></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?/*=(in_array(3,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''*/?> value="3" /></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?/*=(in_array(4,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''*/?> value="4" /></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?/*=(in_array(5,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''*/?> value="5" /></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?/*=(in_array(6,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''*/?> value="6" /></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?/*=(in_array(7,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''*/?> value="7" /></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?/*=(in_array(8,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''*/?> value="8" /></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?/*=(in_array(9,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''*/?> value="9" /></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?/*=(in_array(10,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''*/?> value="10" /></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?/*=(in_array(11,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''*/?> value="11" /></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?/*=(in_array(12,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''*/?> value="12" /></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?/*=(in_array(13,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''*/?> value="13" /></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?/*=(in_array(14,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''*/?> value="14" /></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?/*=(in_array(15,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''*/?> value="15" /></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?/*=(in_array(16,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''*/?> value="16" /></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?/*=(in_array(17,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''*/?> value="17" /></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?/*=(in_array(18,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''*/?> value="18" /></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?/*=(in_array(19,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''*/?> value="19" /></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?/*=(in_array(20,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''*/?> value="20" /></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?/*=(in_array(21,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''*/?> value="21" /></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?/*=(in_array(22,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''*/?> value="22" /></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?/*=(in_array(23,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''*/?> value="23" /></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?/*=(in_array(24,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''*/?> value="24" /></td>
						</tr>
						<tr>
							<td class="h">Вт</td>
							<td><input type="checkbox" name="busy_hours[2][]"<?/*=(in_array(1,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''*/?> value="1" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?/*=(in_array(2,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''*/?> value="2" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?/*=(in_array(3,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''*/?> value="3" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?/*=(in_array(4,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''*/?> value="4" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?/*=(in_array(5,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''*/?> value="5" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?/*=(in_array(6,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''*/?> value="6" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?/*=(in_array(7,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''*/?> value="7" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?/*=(in_array(8,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''*/?> value="8" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?/*=(in_array(9,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''*/?> value="9" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?/*=(in_array(10,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''*/?> value="10" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?/*=(in_array(11,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''*/?> value="11" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?/*=(in_array(12,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''*/?> value="12" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?/*=(in_array(13,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''*/?> value="13" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?/*=(in_array(14,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''*/?> value="14" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?/*=(in_array(15,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''*/?> value="15" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?/*=(in_array(16,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''*/?> value="16" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?/*=(in_array(17,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''*/?> value="17" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?/*=(in_array(18,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''*/?> value="18" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?/*=(in_array(19,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''*/?> value="19" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?/*=(in_array(20,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''*/?> value="20" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?/*=(in_array(21,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''*/?> value="21" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?/*=(in_array(22,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''*/?> value="22" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?/*=(in_array(23,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''*/?> value="23" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?/*=(in_array(24,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''*/?> value="24" /></td>
						</tr>
						<tr>
							<td class="h">Ср</td>
							<td><input type="checkbox" name="busy_hours[3][]"<?/*=(in_array(1,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''*/?> value="1" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?/*=(in_array(2,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''*/?> value="2" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?/*=(in_array(3,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''*/?> value="3" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?/*=(in_array(4,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''*/?> value="4" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?/*=(in_array(5,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''*/?> value="5" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?/*=(in_array(6,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''*/?> value="6" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?/*=(in_array(7,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''*/?> value="7" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?/*=(in_array(8,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''*/?> value="8" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?/*=(in_array(9,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''*/?> value="9" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?/*=(in_array(10,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''*/?> value="10" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?/*=(in_array(11,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''*/?> value="11" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?/*=(in_array(12,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''*/?> value="12" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?/*=(in_array(13,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''*/?> value="13" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?/*=(in_array(14,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''*/?> value="14" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?/*=(in_array(15,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''*/?> value="15" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?/*=(in_array(16,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''*/?> value="16" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?/*=(in_array(17,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''*/?> value="17" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?/*=(in_array(18,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''*/?> value="18" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?/*=(in_array(19,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''*/?> value="19" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?/*=(in_array(20,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''*/?> value="20" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?/*=(in_array(21,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''*/?> value="21" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?/*=(in_array(22,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''*/?> value="22" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?/*=(in_array(23,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''*/?> value="23" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?/*=(in_array(24,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''*/?> value="24" /></td>
						</tr>
						<tr>
							<td class="h">Чт</td>
							<td><input type="checkbox" name="busy_hours[4][]"<?/*=(in_array(1,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''*/?> value="1" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?/*=(in_array(2,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''*/?> value="2" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?/*=(in_array(3,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''*/?> value="3" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?/*=(in_array(4,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''*/?> value="4" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?/*=(in_array(5,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''*/?> value="5" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?/*=(in_array(6,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''*/?> value="6" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?/*=(in_array(7,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''*/?> value="7" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?/*=(in_array(8,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''*/?> value="8" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?/*=(in_array(9,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''*/?> value="9" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?/*=(in_array(10,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''*/?> value="10" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?/*=(in_array(11,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''*/?> value="11" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?/*=(in_array(12,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''*/?> value="12" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?/*=(in_array(13,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''*/?> value="13" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?/*=(in_array(14,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''*/?> value="14" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?/*=(in_array(15,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''*/?> value="15" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?/*=(in_array(16,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''*/?> value="16" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?/*=(in_array(17,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''*/?> value="17" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?/*=(in_array(18,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''*/?> value="18" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?/*=(in_array(19,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''*/?> value="19" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?/*=(in_array(20,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''*/?> value="20" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?/*=(in_array(21,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''*/?> value="21" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?/*=(in_array(22,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''*/?> value="22" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?/*=(in_array(23,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''*/?> value="23" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?/*=(in_array(24,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''*/?> value="24" /></td>
						</tr>
						<tr>
							<td class="h">Пт</td>
							<td><input type="checkbox" name="busy_hours[5][]"<?/*=(in_array(1,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''*/?> value="1" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?/*=(in_array(2,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''*/?> value="2" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?/*=(in_array(3,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''*/?> value="3" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?/*=(in_array(4,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''*/?> value="4" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?/*=(in_array(5,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''*/?> value="5" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?/*=(in_array(6,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''*/?> value="6" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?/*=(in_array(7,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''*/?> value="7" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?/*=(in_array(8,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''*/?> value="8" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?/*=(in_array(9,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''*/?> value="9" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?/*=(in_array(10,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''*/?> value="10" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?/*=(in_array(11,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''*/?> value="11" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?/*=(in_array(12,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''*/?> value="12" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?/*=(in_array(13,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''*/?> value="13" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?/*=(in_array(14,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''*/?> value="14" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?/*=(in_array(15,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''*/?> value="15" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?/*=(in_array(16,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''*/?> value="16" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?/*=(in_array(17,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''*/?> value="17" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?/*=(in_array(18,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''*/?> value="18" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?/*=(in_array(19,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''*/?> value="19" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?/*=(in_array(20,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''*/?> value="20" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?/*=(in_array(21,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''*/?> value="21" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?/*=(in_array(22,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''*/?> value="22" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?/*=(in_array(23,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''*/?> value="23" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?/*=(in_array(24,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''*/?> value="24" /></td>
						</tr>
						<tr>
							<td class="h">Сб</td>
							<td><input type="checkbox" name="busy_hours[6][]"<?/*=(in_array(1,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''*/?> value="1" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?/*=(in_array(2,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''*/?> value="2" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?/*=(in_array(3,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''*/?> value="3" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?/*=(in_array(4,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''*/?> value="4" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?/*=(in_array(5,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''*/?> value="5" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?/*=(in_array(6,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''*/?> value="6" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?/*=(in_array(7,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''*/?> value="7" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?/*=(in_array(8,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''*/?> value="8" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?/*=(in_array(9,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''*/?> value="9" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?/*=(in_array(10,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''*/?> value="10" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?/*=(in_array(11,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''*/?> value="11" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?/*=(in_array(12,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''*/?> value="12" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?/*=(in_array(13,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''*/?> value="13" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?/*=(in_array(14,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''*/?> value="14" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?/*=(in_array(15,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''*/?> value="15" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?/*=(in_array(16,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''*/?> value="16" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?/*=(in_array(17,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''*/?> value="17" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?/*=(in_array(18,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''*/?> value="18" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?/*=(in_array(19,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''*/?> value="19" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?/*=(in_array(20,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''*/?> value="20" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?/*=(in_array(21,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''*/?> value="21" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?/*=(in_array(22,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''*/?> value="22" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?/*=(in_array(23,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''*/?> value="23" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?/*=(in_array(24,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''*/?> value="24" /></td>
						</tr>
						<tr>
							<td class="h">Вс</td>
							<td><input type="checkbox" name="busy_hours[7][]"<?/*=(in_array(1,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''*/?> value="1" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?/*=(in_array(2,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''*/?> value="2" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?/*=(in_array(3,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''*/?> value="3" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?/*=(in_array(4,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''*/?> value="4" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?/*=(in_array(5,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''*/?> value="5" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?/*=(in_array(6,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''*/?> value="6" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?/*=(in_array(7,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''*/?> value="7" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?/*=(in_array(8,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''*/?> value="8" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?/*=(in_array(9,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''*/?> value="9" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?/*=(in_array(10,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''*/?> value="10" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?/*=(in_array(11,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''*/?> value="11" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?/*=(in_array(12,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''*/?> value="12" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?/*=(in_array(13,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''*/?> value="13" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?/*=(in_array(14,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''*/?> value="14" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?/*=(in_array(15,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''*/?> value="15" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?/*=(in_array(16,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''*/?> value="16" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?/*=(in_array(17,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''*/?> value="17" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?/*=(in_array(18,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''*/?> value="18" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?/*=(in_array(19,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''*/?> value="19" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?/*=(in_array(20,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''*/?> value="20" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?/*=(in_array(21,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''*/?> value="21" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?/*=(in_array(22,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''*/?> value="22" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?/*=(in_array(23,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''*/?> value="23" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?/*=(in_array(24,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''*/?> value="24" /></td>
						</tr>
					</tbody>
				</table>-->
				<table class="estelife-busy-hours">
					<thead>
						<tr>
							<th class="all">Все</th>
							<th class="h">Пн</th>
							<th class="h">Вт</th>
							<th class="h">Ср</th>
							<th class="h">Чт</th>
							<th class="h">Пт</th>
							<th class="h">Сб</th>
							<th class="h">Вс</th>

						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="h">00<sup>00</sup><br />00<sup>30</sup></td>
							<td id="1" ><input type="checkbox" name="busy_hours[1][]"<?=(in_array(1,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="1" /></td>
							<td id="2"><input type="checkbox" name="busy_hours[2][]"<?=(in_array(1,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="1" /></td>
							<td id="3"><input type="checkbox" name="busy_hours[3][]"<?=(in_array(1,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="1" /></td>
							<td id="4"><input type="checkbox" name="busy_hours[4][]"<?=(in_array(1,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="1" /></td>
							<td id="5"><input type="checkbox" name="busy_hours[5][]"<?=(in_array(1,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="1" /></td>
							<td id="6"><input type="checkbox" name="busy_hours[6][]"<?=(in_array(1,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="1" /></td>
							<td id="7"><input type="checkbox" name="busy_hours[7][]"<?=(in_array(1,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="1" /></td>
						</tr>
						<tr>
							<td class="h">00<sup>30</sup><br />01<sup>00</sup></td>
							<td id="9"><input type="checkbox" name="busy_hours[1][]"<?=(in_array(2,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="2" /></td>
							<td id="10"><input type="checkbox" name="busy_hours[2][]"<?=(in_array(2,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="2" /></td>
							<td id="11"><input type="checkbox" name="busy_hours[3][]"<?=(in_array(2,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="2" /></td>
							<td id="12"><input type="checkbox" name="busy_hours[4][]"<?=(in_array(2,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="2" /></td>
							<td id="13"><input type="checkbox" name="busy_hours[5][]"<?=(in_array(2,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="2" /></td>
							<td id="14"><input type="checkbox" name="busy_hours[6][]"<?=(in_array(2,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="2" /></td>
							<td id="15"><input type="checkbox" name="busy_hours[7][]"<?=(in_array(2,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="2" /></td>
						</tr>
						<tr>
							<td class="h">01<sup>00</sup><br />01<sup>30</sup></td>
							<td id="16"><input type="checkbox" name="busy_hours[1][]"<?=(in_array(3,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="3" /></td>
							<td id="17"><input type="checkbox" name="busy_hours[2][]"<?=(in_array(3,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="3" /></td>
							<td id="18"><input type="checkbox" name="busy_hours[3][]"<?=(in_array(3,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="3" /></td>
							<td id="19"><input type="checkbox" name="busy_hours[4][]"<?=(in_array(3,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="3" /></td>
							<td id="20"><input type="checkbox" name="busy_hours[5][]"<?=(in_array(3,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="3" /></td>
							<td id="21"><input type="checkbox" name="busy_hours[6][]"<?=(in_array(3,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="3" /></td>
							<td id="22"><input type="checkbox" name="busy_hours[7][]"<?=(in_array(3,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="3" /></td>
						</tr>
						<tr>
							<td class="h">01<sup>30</sup><br />02<sup>00</sup></td>
							<td id="23"><input type="checkbox" name="busy_hours[1][]"<?=(in_array(4,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="4" /></td>
							<td id="24"><input type="checkbox" name="busy_hours[2][]"<?=(in_array(4,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="4" /></td>
							<td id="25"><input type="checkbox" name="busy_hours[3][]"<?=(in_array(4,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="4" /></td>
							<td id="26"><input type="checkbox" name="busy_hours[4][]"<?=(in_array(4,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="4" /></td>
							<td id="27"><input type="checkbox" name="busy_hours[5][]"<?=(in_array(4,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="4" /></td>
							<td id="28"><input type="checkbox" name="busy_hours[6][]"<?=(in_array(4,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="4" /></td>
							<td id="29"><input type="checkbox" name="busy_hours[7][]"<?=(in_array(4,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="4" /></td>
						</tr>
						<tr>
							<td class="h">02<sup>00</sup><br />02<sup>30</sup></td>
							<td id="30"><input type="checkbox" name="busy_hours[1][]"<?=(in_array(5,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="5" /></td>
							<td id="31"><input type="checkbox" name="busy_hours[2][]"<?=(in_array(5,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="5" /></td>
							<td id="32"><input type="checkbox" name="busy_hours[3][]"<?=(in_array(5,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="5" /></td>
							<td id="33"><input type="checkbox" name="busy_hours[4][]"<?=(in_array(5,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="5" /></td>
							<td id="34"><input type="checkbox" name="busy_hours[5][]"<?=(in_array(5,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="5" /></td>
							<td id="35"><input type="checkbox" name="busy_hours[6][]"<?=(in_array(5,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="5" /></td>
							<td id="36"><input type="checkbox" name="busy_hours[7][]"<?=(in_array(5,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="5" /></td>
						</tr>
						<tr>
							<td class="h">02<sup>30</sup><br />03<sup>00</sup></td>
							<td id="36"><input type="checkbox" name="busy_hours[1][]"<?=(in_array(6,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="6" /></td>
							<td id="37"><input type="checkbox" name="busy_hours[2][]"<?=(in_array(6,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="6" /></td>
							<td id="38"><input type="checkbox" name="busy_hours[3][]"<?=(in_array(6,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="6" /></td>
							<td id="39"><input type="checkbox" name="busy_hours[4][]"<?=(in_array(6,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="6" /></td>
							<td id="40"><input type="checkbox" name="busy_hours[5][]"<?=(in_array(6,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="6" /></td>
							<td id="41"><input type="checkbox" name="busy_hours[6][]"<?=(in_array(6,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="6" /></td>
							<td id="42"><input type="checkbox" name="busy_hours[7][]"<?=(in_array(6,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="6" /></td>
						</tr>
						<tr>
							<td class="h">03<sup>00</sup><br />03<sup>30</sup></td>
							<td id="43"><input type="checkbox" name="busy_hours[1][]"<?=(in_array(7,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="7" /></td>
							<td id="44"><input type="checkbox" name="busy_hours[2][]"<?=(in_array(7,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="7" /></td>
							<td id="45"><input type="checkbox" name="busy_hours[3][]"<?=(in_array(7,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="7" /></td>
							<td id="46"><input type="checkbox" name="busy_hours[4][]"<?=(in_array(7,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="7" /></td>
							<td id="47"><input type="checkbox" name="busy_hours[5][]"<?=(in_array(7,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="7" /></td>
							<td id="48"><input type="checkbox" name="busy_hours[6][]"<?=(in_array(7,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="7" /></td>
							<td id="49"><input type="checkbox" name="busy_hours[7][]"<?=(in_array(7,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="7" /></td>
						</tr>
						<tr>
							<td class="h">03<sup>00</sup><br />03<sup>30</sup></td>
							<td id="50"><input type="checkbox" name="busy_hours[1][]"<?=(in_array(8,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="8" /></td>
							<td id="51"><input type="checkbox" name="busy_hours[2][]"<?=(in_array(8,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="8" /></td>
							<td id="52"><input type="checkbox" name="busy_hours[3][]"<?=(in_array(8,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="8" /></td>
							<td id="53"><input type="checkbox" name="busy_hours[4][]"<?=(in_array(8,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="8" /></td>
							<td id="54"><input type="checkbox" name="busy_hours[5][]"<?=(in_array(8,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="8" /></td>
							<td id="55"><input type="checkbox" name="busy_hours[6][]"<?=(in_array(8,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="8" /></td>
							<td id="56"><input type="checkbox" name="busy_hours[7][]"<?=(in_array(8,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="8" /></td>
						</tr>
						<tr>
							<td class="h">03<sup>30</sup><br />04<sup>00</sup></td>
							<td id="57"><input type="checkbox" name="busy_hours[1][]"<?=(in_array(9,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="9" /></td>
							<td id="58"><input type="checkbox" name="busy_hours[2][]"<?=(in_array(9,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="9" /></td>
							<td id="59"><input type="checkbox" name="busy_hours[3][]"<?=(in_array(9,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="9" /></td>
							<td id="60"><input type="checkbox" name="busy_hours[4][]"<?=(in_array(9,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="9" /></td>
							<td id="61"><input type="checkbox" name="busy_hours[5][]"<?=(in_array(9,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="9" /></td>
							<td id="62"><input type="checkbox" name="busy_hours[6][]"<?=(in_array(9,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="9" /></td>
							<td id="63"><input type="checkbox" name="busy_hours[7][]"<?=(in_array(9,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="9" /></td>
						</tr>
						<tr>
							<td class="h">04<sup>00</sup><br />04<sup>30</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(10,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="10" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(10,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="10" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(10,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="10" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(10,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="10" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(10,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="10" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(10,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="10" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(10,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="10" /></td>
						</tr>
						<tr>
							<td class="h">04<sup>30</sup><br />05<sup>00</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(11,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="11" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(11,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="11" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(11,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="11" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(11,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="11" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(11,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="11" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(11,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="11" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(11,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="11" /></td>
						</tr>
						<tr>
							<td class="h">05<sup>00</sup><br />05<sup>30</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(12,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="12" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(12,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="12" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(12,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="12" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(12,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="12" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(12,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="12" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(12,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="12" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(12,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="12" /></td>
						</tr>
						<tr>
							<td class="h">05<sup>30</sup><br />06<sup>00</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(13,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="13" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(13,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="13" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(13,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="13" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(13,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="13" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(13,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="13" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(13,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="13" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(13,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="13" /></td>
						</tr>
						<tr>
							<td class="h">06<sup>00</sup><br />06<sup>30</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(14,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="14" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(14,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="14" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(14,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="14" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(14,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="14" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(14,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="14" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(14,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="14" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(14,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="14" /></td>
						</tr>
						<tr>
							<td class="h">06<sup>30</sup><br />07<sup>00</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(15,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="15" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(15,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="15" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(15,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="15" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(15,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="15" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(15,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="15" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(15,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="15" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(15,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="15" /></td>
						</tr>
						<tr>
							<td class="h">07<sup>00</sup><br />07<sup>30</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(16,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="16" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(16,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="16" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(16,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="16" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(16,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="16" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(16,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="16" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(16,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="16" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(16,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="16" /></td>
						</tr>
						<tr>
							<td class="h">07<sup>30</sup><br />08<sup>00</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(17,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="17" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(17,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="17" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(17,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="17" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(17,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="17" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(17,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="17" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(17,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="17" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(17,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="17" /></td>
						</tr>
						<tr>
							<td class="h">08<sup>00</sup><br />08<sup>30</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(18,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="18" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(18,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="18" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(18,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="18" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(18,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="18" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(18,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="18" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(18,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="18" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(18,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="18" /></td>
						</tr>
						<tr>
							<td class="h">08<sup>30</sup><br />09<sup>00</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(19,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="19" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(19,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="19" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(19,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="19" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(19,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="19" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(19,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="19" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(19,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="19" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(19,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="19" /></td>
						</tr>
						<tr>
							<td class="h">09<sup>00</sup><br />09<sup>30</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(20,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="20" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(20,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="20" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(20,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="20" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(20,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="20" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(20,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="20" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(20,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="20" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(20,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="20" /></td>
						</tr>
						<tr>
							<td class="h">09<sup>30</sup><br />10<sup>00</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(21,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="21" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(21,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="21" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(21,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="21" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(21,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="21" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(21,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="21" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(21,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="21" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(21,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="21" /></td>
						</tr>
						<tr>
							<td class="h">10<sup>00</sup><br />10<sup>30</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(22,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="22" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(22,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="22" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(22,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="22" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(22,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="22" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(22,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="22" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(22,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="22" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(22,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="22" /></td>
						</tr>
						<tr>
							<td class="h">10<sup>30</sup><br />11<sup>00</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(23,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="23" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(23,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="23" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(23,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="23" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(23,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="23" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(23,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="23" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(23,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="23" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(23,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="23" /></td>
						</tr>
						<tr>
							<td class="h">11<sup>00</sup><br />11<sup>30</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(24,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="24" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(24,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="24" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(24,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="24" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(24,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="24" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(24,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="24" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(24,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="24" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(24,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="24" /></td>
						</tr>
						<tr>
							<td class="h">11<sup>30</sup><br />12<sup>00</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(25,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="25" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(25,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="25" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(25,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="25" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(25,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="25" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(25,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="25" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(25,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="25" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(25,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="25" /></td>
						</tr>
						<tr>
							<td class="h">12<sup>00</sup><br />12<sup>30</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(26,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="26" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(26,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="26" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(26,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="26" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(26,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="26" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(26,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="26" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(26,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="26" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(26,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="26" /></td>
						</tr>
						<tr>
							<td class="h">12<sup>30</sup><br />13<sup>00</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(27,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="27" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(27,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="27" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(27,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="27" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(27,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="27" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(27,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="27" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(27,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="27" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(27,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="27" /></td>
						</tr>
						<tr>
							<td class="h">13<sup>00</sup><br />13<sup>30</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(28,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="28" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(28,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="28" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(28,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="28" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(28,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="28" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(28,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="28" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(28,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="28" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(28,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="28" /></td>
						</tr>
						<tr>
							<td class="h">13<sup>30</sup><br />14<sup>00</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(29,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="29" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(29,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="29" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(29,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="29" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(29,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="29" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(29,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="29" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(29,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="29" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(29,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="29" /></td>
						</tr>
						<tr>
							<td class="h">14<sup>00</sup><br />14<sup>30</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(30,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="30" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(30,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="30" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(30,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="30" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(30,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="30" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(30,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="30" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(30,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="30" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(30,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="30" /></td>
						</tr>
						<tr>
							<td class="h">14<sup>30</sup><br />15<sup>00</sup></td>
							<td ><input type="checkbox" name="busy_hours[1][]"<?=(in_array(31,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="31" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(31,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="31" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(31,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="31" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(31,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="31" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(31,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="31" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(31,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="31" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(31,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="31" /></td>
						</tr>
						<tr>
							<td class="h">15<sup>00</sup><br />15<sup>30</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(32,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="32" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(32,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="32" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(32,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="32" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(32,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="32" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(32,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="32" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(32,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="32" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(32,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="32" /></td>
						</tr>
						<tr>
							<td class="h">15<sup>30</sup><br />16<sup>00</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(33,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="33" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(33,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="33" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(33,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="33" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(33,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="33" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(33,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="33" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(33,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="33" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(33,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="33" /></td>
						</tr>
						<tr>
							<td class="h">16<sup>00</sup><br />16<sup>30</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(34,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="34" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(34,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="34" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(34,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="34" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(34,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="34" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(34,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="34" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(34,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="34" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(34,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="34" /></td>
						</tr>
						<tr>
							<td class="h">16<sup>30</sup><br />17<sup>00</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(35,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="35" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(35,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="35" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(35,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="35" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(35,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="35" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(35,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="35" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(35,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="35" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(35,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="35" /></td>
						</tr>
						<tr>
							<td class="h">17<sup>00</sup><br />17<sup>30</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(36,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="36" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(36,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="36" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(36,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="36" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(36,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="36" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(36,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="36" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(36,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="36" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(36,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="36" /></td>
						</tr>
						<tr>
							<td class="h">17<sup>30</sup><br />18<sup>00</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(37,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="37" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(37,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="37" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(37,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="37" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(37,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="37" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(37,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="37" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(37,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="37" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(37,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="37" /></td>
						</tr>
						<tr>
							<td class="h">18<sup>00</sup><br />18<sup>30</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(38,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="38" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(38,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="38" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(38,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="38" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(38,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="38" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(38,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="38" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(38,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="38" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(38,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="38" /></td>
						</tr>
						<tr>
							<td class="h">18<sup>30</sup><br />19<sup>00</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(39,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="39" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(39,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="39" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(39,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="39" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(39,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="39" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(39,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="39" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(39,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="39" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(39,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="39" /></td>
						</tr>
						<tr>
							<td class="h">19<sup>00</sup><br />19<sup>30</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(40,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="40" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(40,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="40" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(40,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="40" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(40,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="40" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(40,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="40" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(40,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="40" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(40,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="40" /></td>
						</tr>
						<tr>
							<td class="h">19<sup>30</sup><br />20<sup>00</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(41,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="41" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(41,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="41" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(41,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="41" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(41,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="41" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(41,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="41" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(41,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="41" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(41,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="41" /></td>
						</tr>
						<tr>
							<td class="h">20<sup>00</sup><br />20<sup>30</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(42,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="42" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(42,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="42" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(42,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="42" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(42,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="42" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(42,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="42" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(42,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="42" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(42,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="42" /></td>
						</tr>
						<tr>
							<td class="h">20<sup>30</sup><br />21<sup>00</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(43,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="43" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(43,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="43" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(43,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="43" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(43,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="43" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(43,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="43" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(43,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="43" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(43,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="43" /></td>
						</tr>
						<tr>
							<td class="h">21<sup>00</sup><br />21<sup>30</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(44,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="44" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(44,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="44" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(44,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="44" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(44,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="44" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(44,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="44" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(44,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="44" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(44,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="44" /></td>
						</tr>
						<tr>
							<td class="h">21<sup>30</sup><br />22<sup>00</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(45,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="45" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(45,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="45" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(45,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="45" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(45,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="45" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(45,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="45" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(45,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="45" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(45,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="45" /></td>
						</tr>
						<tr>
							<td class="h">22<sup>00</sup><br />22<sup>30</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(46,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="46" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(46,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="46" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(46,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="46" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(46,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="46" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(46,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="46" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(46,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="46" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(46,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="46" /></td>
						</tr>
						<tr>
							<td class="h">22<sup>30</sup><br />23<sup>00</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(47,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="47" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(47,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="47" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(47,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="47" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(47,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="47" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(47,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="47" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(47,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="47" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(47,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="47" /></td>
						</tr>
						<tr>
							<td class="h">23<sup>00</sup><br />23<sup>30</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(48,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="48" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(48,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="48" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(48,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="48" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(48,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="48" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(48,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="48" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(48,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="48" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(48,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="48" /></td>
						</tr>
						<tr>
							<td class="h">23<sup>30</sup><br />00<sup>00</sup></td>
							<td><input type="checkbox" name="busy_hours[1][]"<?=(in_array(49,$arResult['clinic']['busy_hours'][1])) ? ' checked="checked"' : ''?> value="49" /></td>
							<td><input type="checkbox" name="busy_hours[2][]"<?=(in_array(49,$arResult['clinic']['busy_hours'][2])) ? ' checked="checked"' : ''?> value="49" /></td>
							<td><input type="checkbox" name="busy_hours[3][]"<?=(in_array(49,$arResult['clinic']['busy_hours'][3])) ? ' checked="checked"' : ''?> value="49" /></td>
							<td><input type="checkbox" name="busy_hours[4][]"<?=(in_array(49,$arResult['clinic']['busy_hours'][4])) ? ' checked="checked"' : ''?> value="49" /></td>
							<td><input type="checkbox" name="busy_hours[5][]"<?=(in_array(49,$arResult['clinic']['busy_hours'][5])) ? ' checked="checked"' : ''?> value="49" /></td>
							<td><input type="checkbox" name="busy_hours[6][]"<?=(in_array(49,$arResult['clinic']['busy_hours'][6])) ? ' checked="checked"' : ''?> value="49" /></td>
							<td><input type="checkbox" name="busy_hours[7][]"<?=(in_array(49,$arResult['clinic']['busy_hours'][7])) ? ' checked="checked"' : ''?> value="49" /></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>

		<?
		$tabControl->BeginNextTab()
		?>

		<tr>
			<td width="40%" align="right"><?=GetMessage("ESTELIFE_F_SITE")?></td>
			<td width="60%"><input type="text" name="site" size="60" maxlength="255" value="<?=$arResult['clinic']['site']?>"></td>
		</tr>
		<tr>
			<td width="40%" align="right"><?=GetMessage("ESTELIFE_F_EMAIL")?></td>
			<td width="60%"><input type="text" name="email" size="60" maxlength="255" value="<?=$arResult['clinic']['email']?>"></td>
		</tr>
		<?php if(!empty($arResult['clinic']['phones'])): ?>
			<?php foreach($arResult['clinic']['phones'] as $sPhone): ?>
				<tr class="adm-detail-required-field">
					<td width="40%"><?=GetMessage("ESTELIFE_F_PHONE")?></td>
					<td width="60%">
						<input type="text" name="phones[]" size="60" maxlength="255" value="<?=VString::formatPhone($sPhone)?>">
						<a href="#" class="estelife-more estelife-btn adm-btn adm-btn-delete estelife-delete"></a>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<tr class="adm-detail-required-field">
			<td width="40%"><?=GetMessage("ESTELIFE_F_PHONE")?></td>
			<td width="60%"><input type="text" name="phones[]" size="60" maxlength="255" value=""><a href="#" class="estelife-more estelife-btn adm-btn adm-btn-save">+</a></td>
		</tr>

		<?
		$tabControl->BeginNextTab()
		?>

		<tr>
			<td colspan="2">
				<input type="file" name="gallery[]" id="gallery" />
				<?php if(!empty($arResult['clinic']['photos'])): ?>
					<div class="estelife-photos">
					<?php foreach($arResult['clinic']['photos'] as $arPhoto): ?>
						<div class="item">
							<div class="image">
							<?=CFile::ShowImage($arPhoto['original'],300,300)?>
							</div>
							<div class="desc" id="tr_photo_descriptions_<?=$arPhoto['id']?>_editor">
								<label for="phdl<?=$arPhoto['id']?>"><input type="checkbox" id="phdl<?=$arPhoto['id']?>" name="photo_deleted[]" value="<?=$arPhoto['id']?>">Удалить</label>
								<?CFileMan::AddHTMLEditorFrame(
									"photo_descriptions_".$arPhoto['id'],
									$arPhoto['description'],
									"photo_descriptions_".$arPhoto['id'],
									'',
									array(
										'height' => 200,
										'width' => 800
									),
									"N",
									0,
									"",
									"",
									$arIBlock["LID"],
									true,
									false,
									array(
										'toolbarConfig' => CFileman::GetEditorToolbarConfig("iblock_".(defined('BX_PUBLIC_MODE') && BX_PUBLIC_MODE == 1 ? 'public' : 'admin')),
										'saveEditorKey' => $IBLOCK_ID
									)
								);?>
							</div>
						</div>
					<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</td>
		</tr>

		<?php if(empty($CLINIC_ID)): ?>
		<?
		$tabControl->BeginNextTab()
		?>
		<div class="estelife-services">
			<div class="one-list">
				<ul>
					<?php if(!empty($arResult['clinic']['akzii'])): ?>
						<?php foreach($arResult['clinic']['akzii'] as $arAkzii): ?>
							<li>
								<input type="checkbox" name="delete_akzii[]" value="<?=$arAkzii['id']?>" />
								<a href="/bitrix/admin/estelife_akzii_edit.php?lang=<?=LANGUAGE_ID?>&ID=<?=$arAkzii['id']?>" target="blank"><?=$arAkzii['name']?></a>
								<a href="/bitrix/admin/estelife_akzii_edit.php?lang=<?=LANGUAGE_ID?>&ID=<?=$arAkzii['id']?>" target="blank" class="view"><?=$arAkzii['name']?></a>
							</li>
						<?php endforeach; ?>
					<?php endif; ?>
				</ul>
			</div>
			<div class="estelife-buttons estelife-send">
				<a href="/bitrix/admin/estelife_akzii_edit.php?lang=<?=LANGUAGE_ID?>&CLINIC_ID=<?=$arResult['clinic']['id']?>" class="adm-btn adm-btn-save adm-btn-add">Добавить акцию</a>
				<input type="submit" class="adm-btn" name="delete_akzii_proccess" value="Удалить выбранные">
			</div>
		</div>
		<?php endif; ?>

		<?
		$tabControl->BeginNextTab()
		?>
		<div class="estelife-services one-list">
			<?php if(!empty($arResult['clinic']['articles'])): ?>
				<?php foreach($arResult['clinic']['articles'] as $val): ?>
					<tr>
						<td width="30%"><?=GetMessage("ESTELIFE_F_ARTICLES")?></td>
						<td width="70%">
							<input type="hidden" name="articles[article_id][]" value="<?=$val['article_id']?>" />
							<input type="text" disabled="disabled" name="articles[article_name][]" data-input="article_id" class="estelife-need-clone" size="60" value="<?=$val['article_name']?>" />
							<a href="#" class="estelife-more estelife-btn adm-btn adm-btn-delete estelife-delete"></a>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
			<tr>
				<td width="30%"><?=GetMessage("ESTELIFE_F_ARTICLES")?></td>
				<td width="70%">
					<input type="hidden" name="articles[article_id][]" value="" />
					<input type="text" name="articles[article_name][]" data-input="article_id" class="estelife-need-clone" value=""size="60" />
					<a href="#" class="estelife-more estelife-btn adm-btn adm-btn-save">&crarr;</a>
				</td>
			</tr>
		</div>
		<?
		$tabControl->BeginNextTab()
		?>
		<div class="estelife-services one-list">
			<div class="one-list">
				<ul>
					<?php if(!empty($arResult['clinic']['fill'])): ?>
						<?php foreach($arResult['clinic']['fill'] as $arFill): ?>
							<li>
								<input type="checkbox" name="delete_fill[]" value="<?=$arFill['id']?>" /><a href="/bitrix/admin/estelife_clinic_edit.php?lang=<?=LANGUAGE_ID?>&ID=<?=$arFill['id']?>" target="blank"><?=$arFill['name']?></a>
								<a href="/bitrix/admin/estelife_clinic_edit.php?lang=<?=LANGUAGE_ID?>&ID=<?=$arFill['id']?>" target="_blank" class="view">Посмотреть</a>
							</li>
						<?php endforeach; ?>
					<?php endif; ?>
				</ul>
			</div>
			<div class="estelife-buttons estelife-send">
				<a href="/bitrix/admin/estelife_clinic_edit.php?lang=<?=LANGUAGE_ID?>&CLINIC_ID=<?=$arResult['clinic']['id']?>" class="adm-btn adm-btn-save adm-btn-add">Добавить филиал</a>
				<input type="submit" class="adm-btn" name="delete_fill_proccess" value="Удалить выбранные">
			</div>
		</div>
		<?php
		$tabControl->EndTab();
		$tabControl->Buttons(array("disabled"=>false, "back_url"=>(strlen($back_url) > 0 ? $back_url : "estelife_service_concreate_list.php?lang=".LANGUAGE_ID)));
		$tabControl->End();
		?>
	</form>
<?php
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");