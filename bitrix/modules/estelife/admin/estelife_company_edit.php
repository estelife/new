 <?php
use companies\VCompanies;
use core\database\VDatabase;
use core\exceptions as ex;
use core\types\VArray;
use core\types\VString;
use reference\services as rs;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/estelife/prolog.php");

ClearVars();
GLOBAL $APPLICATION;
$FORM_RIGHT = $APPLICATION->GetGroupRight("estelife");

if($FORM_RIGHT<="D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));


CModule::IncludeModule("estelife");
CModule::IncludeModule('iblock');
IncludeModuleLangFile(__FILE__);

define("HELP_FILE","estelife_list.php");
$ID=isset($_REQUEST['ID']) ?
	intval($_REQUEST['ID']) : 0;

$obCompanies= VDatabase::driver();
$obElements=new CIBlockElement();
$obCompaniesColl=new VCompanies();

//Получение списка компаний
$obQuery = $obCompanies->createQuery();
$obQuery->builder()->from('estelife_companies')
	->field('id')
	->field('name');
$arResult['companies_list'] = $obQuery->select()->all();

if(!empty($ID)){
	$obQuery = $obCompanies->createQuery();
	$obQuery->builder()->from('estelife_companies', 'ec');
	$obJoin = $obQuery->builder()->join();
	$obJoin->_left()
		->_from('ec', 'id')
		->_to('estelife_company_geo', 'company_id', 'cgeo');
	$obJoin->_left()
		->_from('cgeo', 'city_id')
		->_to('iblock_element', 'ID', 'ct')
		->_cond()->_eq('ct.IBLOCK_ID', 16);
	$obJoin->_left()
		->_from('cgeo', 'country_id')
		->_to('iblock_element', 'ID', 'cntr')
		->_cond()->_eq('cntr.IBLOCK_ID', 15);
	$obJoin->_left()
		->_from('cgeo', 'metro_id')
		->_to('iblock_element', 'ID', 'mt')
		->_cond()->_eq('mt.IBLOCK_ID', 17);
	$obQuery->builder()
		->field('ct.ID','city_id')
		->field('mt.ID','metro_id')
		->field('cntr.ID', 'country_id')
		->field('ct.NAME','city_name')
		->field('mt.NAME','metro_name')
		->field('cntr.NAME','country_name')
		->field('ec.id','id')
		->field('cgeo.address','address')
		->field('cgeo.latitude','latitude')
		->field('cgeo.longitude','longitude')
		->field('ec.name','name')
		->field('ec.translit','translit')
		->field('ec.preview_text','preview_text')
		->field('ec.detail_text','detail_text')
		->field('ec.company_id','company_id')
		->field('ec.logo_id','logo_id');
	$obQuery->builder()->filter()
		->_eq('ec.id', $ID);
	$obResult = $obQuery->select();
	$arResult['company']=$obResult->assoc();

	//Получение контактов
	$obQuery = $obCompanies->createQuery();
	$obQuery->builder()->from('estelife_company_contacts')->filter()
		->_eq('company_id', $ID);
	foreach($obQuery->select()->all() as $val){
		if($val['type']=='web')
			$arResult['company']['web'][]=$val['value'];
		else if($val['type']=='email')
			$arResult['company']['email'][]=$val['value'];
		else if($val['type']=='phone')
			$arResult['company']['phone'][]=$val['value'];
		else if ($val['type']=='fax')
			$arResult['company']['fax'][]=$val['value'];
	}

	//Получение типов компаний
	$obQuery = $obCompanies->createQuery();
	$obQuery->builder()->from('estelife_company_types', 'ec');
	$obJoin = $obQuery->builder()->join();
	$obJoin->_left()
		->_from('ec', 'id')
		->_to('estelife_company_type_geo', 'company_id', 'cgeo');
	$obJoin->_left()
		->_from('cgeo', 'city_id')
		->_to('iblock_element', 'ID', 'ct')
		->_cond()->_eq('ct.IBLOCK_ID', 16);
	$obJoin->_left()
		->_from('cgeo', 'country_id')
		->_to('iblock_element', 'ID', 'cntr')
		->_cond()->_eq('cntr.IBLOCK_ID', 15);
	$obJoin->_left()
		->_from('cgeo', 'metro_id')
		->_to('iblock_element', 'ID', 'mt')
		->_cond()->_eq('mt.IBLOCK_ID', 17);
	$obQuery->builder()
		->field('ct.ID','city_id')
		->field('mt.ID','metro_id')
		->field('cntr.ID', 'country_id')
		->field('ct.NAME','city_name')
		->field('mt.NAME','metro_name')
		->field('cntr.NAME','country_name')
		->field('ec.id','id')
		->field('cgeo.address','address')
		->field('cgeo.latitude','latitude')
		->field('cgeo.longitude','longitude')
		->field('ec.name','name')
		->field('ec.translit','translit')
		->field('ec.preview_text','preview_text')
		->field('ec.detail_text','detail_text')
		->field('ec.company_id','company_id')
		->field('ec.logo_id','logo_id')
		->field('ec.type','type');
	$obQuery->builder()->filter()
		->_eq('ec.company_id', $ID);

	foreach ($obQuery->select()->all() as $val){
		if ($val['type'] == 1){
			$arResult['clinic']=$val;
			$arType[$val['id']] = 'clinic';
		}elseif($val['type'] == 2){
			$arResult['org']=$val;
			$arType[$val['id']] = 'org';
		}elseif($val['type'] == 3){
			$arResult['maker']=$val;
			$arType[$val['id']] = 'maker';
		}elseif($val['type'] == 4){
			$arResult['learning']=$val;
			$arType[$val['id']] = 'learning';
		}
		$arCompanyTypesId[] = $val['id'];
	}

	//Получение контактов для типов компаний
	if (!empty($arCompanyTypesId)){
		$obQuery = $obCompanies->createQuery();
		$obQuery->builder()->from('estelife_company_type_contacts')->filter()
			->_in('company_id', $arCompanyTypesId);

		foreach($obQuery->select()->all() as $val){
			if($val['type']=='web')
				$arResult[$arType[$val['company_id']]]['web'][] = $val['value'];
			else if($val['type']=='email')
				$arResult[$arType[$val['company_id']]]['email'][] = $val['value'];
			else if($val['type']=='phone')
				$arResult[$arType[$val['company_id']]]['phone'][] = $val['value'];
			else if ($val['type']=='fax')
				$arResult[$arType[$val['company_id']]]['fax'][] = $val['value'];
		}
	}

}else{

    if (!empty($_COOKIE['el_sel_country'])){
        $nCountry = intval($_COOKIE['el_sel_country']);
        $obResult = $obElements->GetByID($nCountry);

        if ($arCountry = $obResult->Fetch()){
            $arResult['company']['country_id'] = $arCountry['ID'];
            $arResult['company']['country_name'] = $arCountry['NAME'];
        }
    }

	if(!empty($_COOKIE['el_sel_city'])){
		$nCity=intval($_COOKIE['el_sel_city']);
		$obResult=$obElements->GetByID($nCity);

		if($arCity=$obResult->Fetch()){
			$arResult['company']['city_id']=$arCity['ID'];
			$arResult['company']['city_name']=$arCity['NAME'];
		}
	}

	if(!empty($_COOKIE['el_sel_metro'])){
		$nMetro=intval($_COOKIE['el_sel_metro']);
		$obResult=$obElements->GetByID($nMetro);

		if($arMetro=$obResult->Fetch()){
			$arResult['company']['metro_id']=$arMetro['ID'];
			$arResult['company']['metro_name']=$arMetro['NAME'];
		}
	}
}

if($_SERVER['REQUEST_METHOD']=='POST'){
	$obPost=new VArray($_POST);
	$obError=new ex\VFormException();

	$obClinic=new VArray($obPost->one('clinic',array()));
	$obOrg=new VArray($obPost->one('org',array()));
	$obMaker=new VArray($obPost->one('maker',array()));
	$obLearning=new VArray($obPost->one('learning',array()));



	try{
      $obCompany=new VArray($obPost->one('company',array()));

		if($obCompany->blank('name'))
			$obError->setFieldError('NAME_NOT_FILL','name');

        if($obCompany->blank('country_id'))
            $obError->setFieldError('COUNTRY_NOT_FILL','country_id');
        else{
            $obCountry=$obElements->GetByID($obCompany->one('country_id'));

            if($obCountry->SelectedRowsCount()<=0)
                $obError->setFieldError('COUNTRY_NOT_FOUND','country_id');
        }


//		if($obCompany->blank('city_id'))
//			$obError->setFieldError('CITY_NOT_FILL','city_id');
//		else{
//			$obCity=$obElements->GetByID($obCompany->one('city_id'));
//
//			if($obCity->SelectedRowsCount()<=0)
//				$obError->setFieldError('CITY_NOT_FOUND','city_id');
//		}

//		if(!$obCompany->blank('metro_id')){
//			$obMetro=$obElements->GetByID($obCompany->one('metro_id'));
//
//			if($obMetro->SelectedRowsCount()<=0)
//				$obError->setFieldError('METRO_NOT_FOUND','metro_id');
//		}

//		if($obCompany->blank('address'))
//			$obError->setFieldError('ADDRESS_NOT_FILL', 'address');

//		if($obPost->blank('company_detail_text'))
//			$obError->setFieldError('DETAIL_TEXT_NOT_FILL', 'detail_text');
//
//		if($obPost->blank('company_preview_text'))
//			$obError->setFieldError('PREVIEW_TEXT_NOT_FILL', 'preview_text');

		$obError->raise();

		if($obCompany->blank('translit')){
			$arTranslit = VString::translit($obCompany->one('name'));
		}else{
			$arTranslit = VString::translit($obCompany->one('translit'));
		}


		//Добавление компании
        $obQuery = $obCompanies->createQuery();
        $obQuery->builder()->from('estelife_companies')
            ->value('name', trim(htmlentities($obCompany->one('name'),ENT_QUOTES,'utf-8')))
			->value('translit', trim($arTranslit))
            ->value('detail_text', htmlentities($obPost->one('company_detail_text'),ENT_QUOTES,'utf-8'))
            ->value('preview_text', htmlentities($obPost->one('company_preview_text'),ENT_QUOTES,'utf-8'))
			->value('company_id', intval($obCompany->one('company_id')));

        if(!empty($_FILES['company_logo'])){
            $arImage=$_FILES['company_logo'];
            $arImage['old_file']=$obRecord['logo_id'];
            $arImage['module']='estelife';
            $arImage['del']=$logo_del;

            if(strlen($arImage["name"])>0 || strlen($arImage["del"])>0){
                $nImageId=CFile::SaveFile($arImage, "estelife");
                $obQuery->builder()->value('logo_id', intval($nImageId));
            }
        }

        if (!empty($ID)){
            $obQuery->builder()->filter()
                    ->_eq('id',$ID);
            $obQuery->update();
            $idCompany = $ID;

            //удаление гео привязки
            $obQuery =  $obCompanies->createQuery();
            $obQuery->builder()->from('estelife_company_geo')
                ->filter()
                ->_eq('company_id', $idCompany);
             $obQuery->delete();

			//получение списка типов компании
			$obQuery = $obCompanies->CreateQuery();
			$obQuery->builder()->from('estelife_company_types')
				->field('id')
				->field('type')
				->filter()
				->_eq('company_id', $idCompany);

			foreach ($obQuery->select()->all() as $val){
				$arCompanyTypes[$val['type']] = $val['id'];
			}

        }else{
            $idCompany = $obQuery->insert()->insertId();
        }

		setcookie('el_sel_city',$obRecord['city_id'],time()+86400,'/');
        setcookie('el_sel_country',$obRecord['country_id'],time()+86400,'/');

		if(!empty($obRecord['metro_id'])){
			setcookie('el_sel_metro',$obRecord['metro_id'],time()+86400,'/');
		}else{
			setcookie('el_sel_metro',0,time()-86400,'/');
		}

        //Пишем ссылки на гео таблицу
        if (!$obCompany->blank('country_id')){
			$obCompaniesColl->addGeo($idCompany, $obCompany->all(), 'estelife_company_geo');
        }

		//Пишем ссылки на контакты
		$obCompaniesColl->addContacts($idCompany, $obCompany->all(), 'estelife_company_contacts');


		//пишем типы компаний

		//Клиники
		if (!$obClinic->blank('name')){
			if($obClinic->blank('translit')){
				$arTranslit = VString::translit($obClinic->one('name'));
			}else{
				$arTranslit = VString::translit($obClinic->one('translit'));
			}
			$obQuery = $obCompanies->createQuery();
			$obQuery->builder()->from('estelife_company_types')
				->value('name', trim(htmlentities($obClinic->one('name'),ENT_QUOTES,'utf-8')))
				->value('translit', trim($arTranslit))
				->value('detail_text', htmlentities($obPost->one('clinic_detail_text'),ENT_QUOTES,'utf-8'))
				->value('preview_text', htmlentities($obPost->one('clinic_preview_text'),ENT_QUOTES,'utf-8'))
				->value('company_id', $idCompany)
				->value('type', 1);

			if(!empty($_FILES['clinic_logo'])){
				$arImage=$_FILES['clinic_logo'];
				$arImage['old_file']=$obRecord['logo_id'];
				$arImage['module']='estelife';
				$arImage['del']=$logo_del;

				if(strlen($arImage["name"])>0 || strlen($arImage["del"])>0){
					$nImageId=CFile::SaveFile($arImage, "estelife");
					$obQuery->builder()->value('logo_id', intval($nImageId));
				}
			}

			if (!empty($arCompanyTypes[1])){
				$obQuery->builder()->filter()
					->_eq('id',$arCompanyTypes[1]);
				$obQuery->update();
				$idClinic = $arCompanyTypes[1];

				//удаление гео привязки
				$obQuery =  $obCompanies->createQuery();
				$obQuery->builder()->from('estelife_company_type_geo')
					->filter()
					->_eq('company_id', $idClinic);
				$obQuery->delete();
			}else{
				$idClinic = $obQuery->insert()->insertId();
			}

			//Пишем ссылки на гео таблицу
			if (!$obClinic->blank('address')){
				$obCompaniesColl->addGeo($idClinic, $obClinic->all(), 'estelife_company_type_geo');
			}

			//Пишем ссылки на контакты
			$obCompaniesColl->addContacts($idClinic, $obClinic->all(), 'estelife_company_type_contacts');

		}

		//Организаторы
		if (!$obOrg->blank('name')){
			if($obOrg->blank('translit')){
				$arTranslit = VString::translit($obOrg->one('name'));
			}else{
				$arTranslit = VString::translit($obOrg->one('translit'));
			}
			$obQuery = $obCompanies->createQuery();
			$obQuery->builder()->from('estelife_company_types')
				->value('name', trim(htmlentities($obOrg->one('name'),ENT_QUOTES,'utf-8')))
				->value('translit', trim($arTranslit))
				->value('detail_text', htmlentities($obPost->one('org_detail_text'),ENT_QUOTES,'utf-8'))
				->value('preview_text', htmlentities($obPost->one('org_preview_text'),ENT_QUOTES,'utf-8'))
				->value('company_id', $idCompany)
				->value('type', 2);

			if(!empty($_FILES['org_logo'])){
				$arImage=$_FILES['org_logo'];
				$arImage['old_file']=$obRecord['logo_id'];
				$arImage['module']='estelife';
				$arImage['del']=$logo_del;

				if(strlen($arImage["name"])>0 || strlen($arImage["del"])>0){
					$nImageId=CFile::SaveFile($arImage, "estelife");
					$obQuery->builder()->value('logo_id', intval($nImageId));
				}
			}

			if (!empty($arCompanyTypes[2])){
				$obQuery->builder()->filter()
					->_eq('id',$arCompanyTypes[2]);
				$obQuery->update();
				$idOrg = $arCompanyTypes[2];

				//удаление гео привязки
				$obQuery =  $obCompanies->createQuery();
				$obQuery->builder()->from('estelife_company_type_geo')
					->filter()
					->_eq('company_id', $idOrg);
				$obQuery->delete();
			}else{
				$idOrg = $obQuery->insert()->insertId();
			}

			//Пишем ссылки на гео таблицу
			if (!$obOrg->blank('address')){
				$obCompaniesColl->addGeo($idOrg, $obOrg->all(), 'estelife_company_type_geo');
			}

			//Пишем ссылки на контакты
			$obCompaniesColl->addContacts($idOrg, $obOrg->all(), 'estelife_company_type_contacts');

		}

		//Производители
		if (!$obMaker->blank('name')){
			if($obMaker->blank('translit')){
				$arTranslit = VString::translit($obMaker->one('name'));
			}else{
				$arTranslit = VString::translit($obMaker->one('translit'));
			}
			$obQuery = $obCompanies->createQuery();
			$obQuery->builder()->from('estelife_company_types')
				->value('name', htmlentities($obMaker->one('name'),ENT_QUOTES,'utf-8'))
				->value('translit', $arTranslit)
				->value('detail_text', htmlentities($obPost->one('maker_detail_text'),ENT_QUOTES,'utf-8'))
				->value('preview_text', htmlentities($obPost->one('maker_preview_text'),ENT_QUOTES,'utf-8'))
				->value('company_id', $idCompany)
				->value('type', 3);

			if(!empty($_FILES['maker_logo'])){
				$arImage=$_FILES['maker_logo'];
				$arImage['old_file']=$obRecord['logo_id'];
				$arImage['module']='estelife';
				$arImage['del']=$logo_del;

				if(strlen($arImage["name"])>0 || strlen($arImage["del"])>0){
					$nImageId=CFile::SaveFile($arImage, "estelife");
					$obQuery->builder()->value('logo_id', intval($nImageId));
				}
			}

			if (!empty($arCompanyTypes[3])){
				$obQuery->builder()->filter()
					->_eq('id',$arCompanyTypes[3]);
				$obQuery->update();
				$idMaker = $arCompanyTypes[3];

				//удаление гео привязки
				$obQuery =  $obCompanies->createQuery();
				$obQuery->builder()->from('estelife_company_type_geo')
					->filter()
					->_eq('company_id', $idMaker);
				$obQuery->delete();
			}else{
				$idMaker = $obQuery->insert()->insertId();
			}

			//Пишем ссылки на гео таблицу
			if (!$obMaker->blank('address')){
				$obCompaniesColl->addGeo($idMaker, $obMaker->all(), 'estelife_company_type_geo');
			}

			//Пишем ссылки на контакты
			$obCompaniesColl->addContacts($idMaker, $obMaker->all(), 'estelife_company_type_contacts');

		}

		//Учебные центры
		if (!$obLearning->blank('name')){
			if($obLearning->blank('translit')){
				$arTranslit = VString::translit($obLearning->one('name'));
			}else{
				$arTranslit = VString::translit($obLearning->one('translit'));
			}
			$obQuery = $obCompanies->createQuery();
			$obQuery->builder()->from('estelife_company_types')
				->value('name', htmlentities($obLearning->one('name'),ENT_QUOTES,'utf-8'))
				->value('translit', $arTranslit)
				->value('detail_text', htmlentities($obPost->one('learning_detail_text'),ENT_QUOTES,'utf-8'))
				->value('preview_text', htmlentities($obPost->one('learning_preview_text'),ENT_QUOTES,'utf-8'))
				->value('company_id', $idCompany)
				->value('type', 4);

			if(!empty($_FILES['learning_logo'])){
				$arImage=$_FILES['learning_logo'];
				$arImage['old_file']=$obRecord['logo_id'];
				$arImage['module']='estelife';
				$arImage['del']=$logo_del;

				if(strlen($arImage["name"])>0 || strlen($arImage["del"])>0){
					$nImageId=CFile::SaveFile($arImage, "estelife");
					$obQuery->builder()->value('logo_id', intval($nImageId));
				}
			}

			if (!empty($arCompanyTypes[4])){
				$obQuery->builder()->filter()
					->_eq('id',$arCompanyTypes[4]);
				$obQuery->update();
				$idLearning = $arCompanyTypes[4];

				//удаление гео привязки
				$obQuery =  $obCompanies->createQuery();
				$obQuery->builder()->from('estelife_company_type_geo')
					->filter()
					->_eq('company_id', $idLearning);
				$obQuery->delete();
			}else{
				$idLearning = $obQuery->insert()->insertId();
			}

			//Пишем ссылки на гео таблицу
			if (!$obLearning->blank('address')){
				$obCompaniesColl->addGeo($idLearning, $obLearning->all(), 'estelife_company_type_geo');
			}

			//Пишем ссылки на контакты
			$obCompaniesColl->addContacts($idLearning, $obLearning->all(), 'estelife_company_type_contacts');

		}

		if(!$obPost->blank('save'))
			LocalRedirect('/bitrix/admin/estelife_company_list.php?lang='.LANGUAGE_ID);
		else
			LocalRedirect('/bitrix/admin/estelife_company_edit.php?lang='.LANGUAGE_ID.'&ID='.$idCompany);
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


$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("ESTELIFE_T_BASE"), "ICON" => "estelife_r_base", "TITLE" => GetMessage("ESTELIFE_T_BASE")),
	array("DIV" => "edit3", "TAB" => GetMessage("ESTELIFE_T_CLINICS"), "ICON" => "estelife_r_base", "TITLE" => GetMessage("ESTELIFE_T_CLINICS")),
	array("DIV" => "edit4", "TAB" => GetMessage("ESTELIFE_T_ORGANISATION"), "ICON" => "estelife_r_base", "TITLE" => GetMessage("ESTELIFE_T_ORGANISATION")),
	array("DIV" => "edit5", "TAB" => GetMessage("ESTELIFE_T_MAKER"), "ICON" => "estelife_r_base", "TITLE" => GetMessage("ESTELIFE_T_MAKER")),
	array("DIV" => "edit6", "TAB" => GetMessage("ESTELIFE_T_LEARNING"), "ICON" => "estelife_r_base", "TITLE" => GetMessage("ESTELIFE_T_LEARNING")),
);
$tabControl = new CAdminTabControl("estelife_service_concreate_".$ID, $aTabs, true, true);
$message = null;

//===== Тут будем делать сохранение и подготовку данных

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
		foreach($_POST['company'] as $sKey=>$sValue)
			$arResult['company'][$sKey]=$sValue;
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

	<form name="estelife_company" method="POST" action="<?echo $APPLICATION->GetCurPage()?>" enctype="multipart/form-data">
		<?=bitrix_sessid_post()?>
		<input type="hidden" name="ID" value=<?=$ID?> />
		<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>" />
		<?php
		$tabControl->Begin();
		$tabControl->BeginNextTab()
		?>

		<tr class="adm-detail-required-field">
			<td width="40%"><?=GetMessage("ESTELIFE_F_TITLE")?></td>
			<td width="60%"><input type="text" name="company[name]" size="60" maxlength="255" value="<?=$arResult['company']['name']?>"></td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_TRANSLIT")?></td>
			<td width="60%"><input type="text" name="company[translit]" size="60" maxlength="255" value="<?=$arResult['company']['translit']?>"></td>
		</tr>
        <tr>
            <td width="40%"><?=GetMessage("ESTELIFE_F_LOGO")?></td>
            <td width="60%">
                <?echo CFileInput::Show("company_logo", $arResult['company']['logo_id'],
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
			<td width="40%"><?=GetMessage("ESTELIFE_F_FILIAL")?></td>
			<td width="60%">
				<select name=company[company_id]">
					<option value="0"><?=GetMessage('ESTELIFE_SELECT_FILIAL')?></option>
					<?php foreach($arResult['companies_list'] as $val):?>
						<option value="<?=$val['id']?>" <?if ($arResult['company']['company_id'] == $val['id']):?>selected<?endif?>><?=$val['name']?></option>
					<?endforeach?>
				</select>
			</td>
		</tr>
        <tr>
            <td></td>
            <td><br /><br /><b><?=GetMessage("ESTELIFE_F_GEO")?></b></td>
        </tr>
        <tr>
            <td width="40%"><?=GetMessage("ESTELIFE_F_COUNTRY")?></td>
            <td width="60%">
                <input type="hidden" name="company[country_id]" value="<?=$arResult['company']['country_id']?>" />
                <input type="text" name="company[country_name]" data-input="country_id" value="<?=$arResult['company']['country_name']?>" />
            </td>
        </tr>
        <tr>
            <td width="40%"><?=GetMessage("ESTELIFE_F_CITY")?></td>
            <td width="60%">
                <input type="hidden" name="company[city_id]" value="<?=$arResult['company']['city_id']?>" />
                <input type="text" name="company[city_name]" data-input="city_id" value="<?=$arResult['company']['city_name']?>" />
            </td>
        </tr>
        <tr>
            <td width="40%"><?=GetMessage("ESTELIFE_F_METRO")?></td>
            <td width="60%">
                <input type="hidden" name="company[metro_id]" value="<?=$arResult['company']['metro_id']?>" />
                <input type="text"<?=(empty($arResult['company']['metro_id']) ? ' readonly="readonly"' : '')?> name="company[metro_name]" data-input="metro_id" value="<?=$arResult['company']['metro_name']?>" />
            </td>
        </tr>
        <tr>
            <td width="40%"><?=GetMessage("ESTELIFE_F_ADDRESS")?></td>
            <td width="60%">
                <input type="hidden" name="company[latitude]" value="<?=$arResult['company']['latitude']?>" />
                <input type="hidden" name="company[longitude]" value="<?=$arResult['company']['longitude']?>" />
                <input type="text"<?=(empty($arResult['company']['city_id']) ? ' readonly="readonly"' : '')?> name="company[address]" size="60" maxlength="255" value="<?=$arResult['company']['address']?>">
                <div class="gmap"></div>
            </td>
        </tr>
        <tr>
            <td></td>
            <td><br /><br /><b><?=GetMessage("ESTELIFE_F_CONTACTS")?></b></td>
        </tr>
        <?php if(!empty($arResult['company']['web'])): ?>
            <?php foreach($arResult['company']['web'] as $sSite): ?>
                <tr>
                    <td width="40%"><?=GetMessage("ESTELIFE_F_SITE")?></td>
                    <td width="60%">
                        <input type="text" name="company[web][]" size="60" maxlength="255" value="<?=$sSite?>">
                        <a href="#" class="estelife-more estelife-btn adm-btn adm-btn-delete estelife-delete"></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        <tr>
            <td width="40%" align="right"><?=GetMessage("ESTELIFE_F_SITE")?></td>
            <td width="60%"><input type="text" name="company[web][]" size="60" maxlength="255" value=""><a href="#" class="estelife-more estelife-btn adm-btn adm-btn-save">+</a></td>
        </tr>
        <?php if(!empty($arResult['company']['email'])): ?>
            <?php foreach($arResult['company']['email'] as $sEmail): ?>
                <tr>
                    <td width="40%"><?=GetMessage("ESTELIFE_F_EMAIL")?></td>
                    <td width="60%">
                        <input type="text" name="company[email][]" size="60" maxlength="255" value="<?=$sEmail?>">
                        <a href="#" class="estelife-more estelife-btn adm-btn adm-btn-delete estelife-delete"></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        <tr>
            <td width="40%" align="right"><?=GetMessage("ESTELIFE_F_EMAIL")?></td>
            <td width="60%"><input type="text" name="company[email][]" size="60" maxlength="255" value=""><a href="#" class="estelife-more estelife-btn adm-btn adm-btn-save">+</a></td>
        </tr>
        <?php if(!empty($arResult['company']['phone'])): ?>
            <?php foreach($arResult['company']['phone'] as $sPhone): ?>
                <tr>
                    <td width="40%"><?=GetMessage("ESTELIFE_F_PHONE")?></td>
                    <td width="60%">
                        <input type="text" name="company[phone][]" size="60" maxlength="255" value="<?=VString::formatPhone($sPhone)?>">
                        <a href="#" class="estelife-more estelife-btn adm-btn adm-btn-delete estelife-delete"></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        <tr>
            <td width="40%"><?=GetMessage("ESTELIFE_F_PHONE")?></td>
            <td width="60%"><input type="text" name="company[phone][]" size="60" maxlength="255" value=""><a href="#" class="estelife-more estelife-btn adm-btn adm-btn-save">+</a></td>
        </tr>
		<?php if(!empty($arResult['company']['fax'])): ?>
			<?php foreach($arResult['company']['fax'] as $sFax): ?>
				<tr>
					<td width="40%"><?=GetMessage("ESTELIFE_F_FAX")?></td>
					<td width="60%">
						<input type="text" name="company[fax][]" size="60" maxlength="255" value="<?=VString::formatPhone($sFax)?>">
						<a href="#" class="estelife-more estelife-btn adm-btn adm-btn-delete estelife-delete"></a>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_FAX")?></td>
			<td width="60%"><input type="text" name="company[fax][]" size="60" maxlength="255" value=""><a href="#" class="estelife-more estelife-btn adm-btn adm-btn-save">+</a></td>
		</tr>
        <tr>
            <td></td>
            <td><br /><br /><b><?=GetMessage("ESTELIFE_F_DESCRIPTION")?></b></td>
        </tr>
        <tr>
            <td width="40%"><?=GetMessage("ESTELIFE_F_PREVIEW_TEXT")?></td>
            <td width="60%">
                <?CFileMan::AddHTMLEditorFrame(
                    "company_preview_text",
                    $arResult['company']['preview_text'],
                    "company[preview_text_type]",
                    $str_preview_text_type,
                    array(
                        'height' => 300,
                        'width' => '100%'
                    ),
                    "N",
                    0,
                    "",
                    "",
                    "ru"
                );?>
            </td>
        </tr>
        <tr>
            <td width="40%"><?=GetMessage("ESTELIFE_F_DETAIL_TEXT")?></td>
            <td width="60%">
                <?CFileMan::AddHTMLEditorFrame(
                    "company_detail_text",
                    $arResult['company']['detail_text'],
                    "company[detail_text_type]",
                    $str_detail_text_type,
                    array(
                        'height' => 300,
                        'width' => '100%'
                    ),
                    "N",
                    0,
                    "",
                    "",
                    "ru"
                );?>
            </td>
        </tr>
		<?php $tabControl->BeginNextTab()?>
		<input type="hidden" name="clinic[type]" value="1" />
		<tr class="adm-detail-required-field">
			<td width="40%"><?=GetMessage("ESTELIFE_F_TITLE")?></td>
			<td width="60%"><input type="text" name="clinic[name]" size="60" maxlength="255" value="<?=$arResult['clinic']['name']?>"></td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_TRANSLIT")?></td>
			<td width="60%"><input type="text" name="clinic[translit]" size="60" maxlength="255" value="<?=$arResult['clinic']['translit']?>"></td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_LOGO")?></td>
			<td width="60%">
				<?echo CFileInput::Show("clinic_logo", $arResult['clinic']['logo_id'],
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
			<td></td>
			<td><br /><br /><b><?=GetMessage("ESTELIFE_F_GEO")?></b></td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_COUNTRY")?></td>
			<td width="60%">
				<input type="hidden" name="clinic[country_id]" value="<?=$arResult['clinic']['country_id']?>" />
				<input type="text" name="clinic[country_name]" data-input="country_id" value="<?=$arResult['clinic']['country_name']?>" />
			</td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_CITY")?></td>
			<td width="60%">
				<input type="hidden" name="clinic[city_id]" value="<?=$arResult['clinic']['city_id']?>" />
				<input type="text" name="clinic[city_name]" data-input="city_id" value="<?=$arResult['clinic']['city_name']?>" />
			</td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_METRO")?></td>
			<td width="60%">
				<input type="hidden" name="clinic[metro_id]" value="<?=$arResult['clinic']['metro_id']?>" />
				<input type="text"<?=(empty($arResult['clinic']['metro_id']) ? ' readonly="readonly"' : '')?> name="clinic[metro_name]" data-input="metro_id" value="<?=$arResult['clinic']['metro_name']?>" />
			</td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_ADDRESS")?></td>
			<td width="60%">
				<input type="hidden" name="clinic[latitude]" value="<?=$arResult['clinic']['latitude']?>" />
				<input type="hidden" name="clinic[longitude]" value="<?=$arResult['clinic']['longitude']?>" />
				<input type="text"<?=(empty($arResult['clinic']['city_id']) ? ' readonly="readonly"' : '')?> name="clinic[address]" size="60" maxlength="255" value="<?=$arResult['clinic']['address']?>">
				<div class="gmap"></div>
			</td>
		</tr>
		<tr>
			<td></td>
			<td><br /><br /><b><?=GetMessage("ESTELIFE_F_CONTACTS")?></b></td>
		</tr>
		<?php if(!empty($arResult['clinic']['web'])): ?>
			<?php foreach($arResult['clinic']['web'] as $sSite): ?>
				<tr>
					<td width="40%"><?=GetMessage("ESTELIFE_F_SITE")?></td>
					<td width="60%">
						<input type="text" name="clinic[web][]" size="60" maxlength="255" value="<?=$sSite?>">
						<a href="#" class="estelife-more estelife-btn adm-btn adm-btn-delete estelife-delete"></a>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<tr>
			<td width="40%" align="right"><?=GetMessage("ESTELIFE_F_SITE")?></td>
			<td width="60%"><input type="text" name="clinic[web][]" size="60" maxlength="255" value=""><a href="#" class="estelife-more estelife-btn adm-btn adm-btn-save">+</a></td>
		</tr>
		<?php if(!empty($arResult['clinic']['email'])): ?>
			<?php foreach($arResult['clinic']['email'] as $sEmail): ?>
				<tr>
					<td width="40%"><?=GetMessage("ESTELIFE_F_EMAIL")?></td>
					<td width="60%">
						<input type="text" name="clinic[email][]" size="60" maxlength="255" value="<?=$sEmail?>">
						<a href="#" class="estelife-more estelife-btn adm-btn adm-btn-delete estelife-delete"></a>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<tr>
			<td width="40%" align="right"><?=GetMessage("ESTELIFE_F_EMAIL")?></td>
			<td width="60%"><input type="text" name="clinic[email][]" size="60" maxlength="255" value=""><a href="#" class="estelife-more estelife-btn adm-btn adm-btn-save">+</a></td>
		</tr>
		<?php if(!empty($arResult['clinic']['phone'])): ?>
			<?php foreach($arResult['clinic']['phone'] as $sPhone): ?>
				<tr>
					<td width="40%"><?=GetMessage("ESTELIFE_F_PHONE")?></td>
					<td width="60%">
						<input type="text" name="clinic[phone][]" size="60" maxlength="255" value="<?=VString::formatPhone($sPhone)?>">
						<a href="#" class="estelife-more estelife-btn adm-btn adm-btn-delete estelife-delete"></a>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_PHONE")?></td>
			<td width="60%"><input type="text" name="clinic[phone][]" size="60" maxlength="255" value=""><a href="#" class="estelife-more estelife-btn adm-btn adm-btn-save">+</a></td>
		</tr>
		<?php if(!empty($arResult['clinic']['fax'])): ?>
			<?php foreach($arResult['clinic']['fax'] as $sFax): ?>
				<tr>
					<td width="40%"><?=GetMessage("ESTELIFE_F_FAX")?></td>
					<td width="60%">
						<input type="text" name="clinic[fax][]" size="60" maxlength="255" value="<?=VString::formatPhone($sFax)?>">
						<a href="#" class="estelife-more estelife-btn adm-btn adm-btn-delete estelife-delete"></a>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_FAX")?></td>
			<td width="60%"><input type="text" name="clinic[fax][]" size="60" maxlength="255" value=""><a href="#" class="estelife-more estelife-btn adm-btn adm-btn-save">+</a></td>
		</tr>
		<tr>
			<td></td>
			<td><br /><br /><b><?=GetMessage("ESTELIFE_F_DESCRIPTION")?></b></td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_PREVIEW_TEXT")?></td>
			<td width="60%">
				<?CFileMan::AddHTMLEditorFrame(
					"clinic_preview_text",
					$arResult['clinic']['preview_text'],
					"clinic[preview_text_type]",
					$str_preview_text_type,
					array(
						'height' => 300,
						'width' => '100%'
					),
					"N",
					0,
					"",
					"",
					"ru"
				);?>
			</td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_DETAIL_TEXT")?></td>
			<td width="60%">
				<?CFileMan::AddHTMLEditorFrame(
					"clinic_detail_text",
					$arResult['clinic']['detail_text'],
					"clinic[detail_text_type]",
					$str_detail_text_type,
					array(
						'height' => 300,
						'width' => '100%'
					),
					"N",
					0,
					"",
					"",
					"ru"
				);?>
			</td>
		</tr>
		<?php $tabControl->BeginNextTab()?>
		<input type="hidden" name="org[type]" value="2" />
		<tr class="adm-detail-required-field">
			<td width="40%"><?=GetMessage("ESTELIFE_F_TITLE")?></td>
			<td width="60%"><input type="text" name="org[name]" size="60" maxlength="255" value="<?=$arResult['org']['name']?>"></td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_TRANSLIT")?></td>
			<td width="60%"><input type="text" name="org[translit]" size="60" maxlength="255" value="<?=$arResult['org']['translit']?>"></td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_LOGO")?></td>
			<td width="60%">
				<?echo CFileInput::Show("org_logo", $arResult['org']['logo_id'],
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
			<td></td>
			<td><br /><br /><b><?=GetMessage("ESTELIFE_F_GEO")?></b></td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_COUNTRY")?></td>
			<td width="60%">
				<input type="hidden" name="org[country_id]" value="<?=$arResult['org']['country_id']?>" />
				<input type="text" name="org[country_name]" data-input="country_id" value="<?=$arResult['org']['country_name']?>" />
			</td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_CITY")?></td>
			<td width="60%">
				<input type="hidden" name="org[city_id]" value="<?=$arResult['org']['city_id']?>" />
				<input type="text" name="org[city_name]" data-input="city_id" value="<?=$arResult['org']['city_name']?>" />
			</td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_METRO")?></td>
			<td width="60%">
				<input type="hidden" name="org[metro_id]" value="<?=$arResult['org']['metro_id']?>" />
				<input type="text"<?=(empty($arResult['org']['metro_id']) ? ' readonly="readonly"' : '')?> name="org[metro_name]" data-input="metro_id" value="<?=$arResult['org']['metro_name']?>" />
			</td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_ADDRESS")?></td>
			<td width="60%">
				<input type="hidden" name="org[latitude]" value="<?=$arResult['org']['latitude']?>" />
				<input type="hidden" name="org[longitude]" value="<?=$arResult['org']['longitude']?>" />
				<input type="text"<?=(empty($arResult['org']['city_id']) ? ' readonly="readonly"' : '')?> name="org[address]" size="60" maxlength="255" value="<?=$arResult['org']['address']?>">
				<div class="gmap"></div>
			</td>
		</tr>
		<tr>
			<td></td>
			<td><br /><br /><b><?=GetMessage("ESTELIFE_F_CONTACTS")?></b></td>
		</tr>
		<?php if(!empty($arResult['org']['web'])): ?>
			<?php foreach($arResult['org']['web'] as $sSite): ?>
				<tr>
					<td width="40%"><?=GetMessage("ESTELIFE_F_SITE")?></td>
					<td width="60%">
						<input type="text" name="org[web][]" size="60" maxlength="255" value="<?=$sSite?>">
						<a href="#" class="estelife-more estelife-btn adm-btn adm-btn-delete estelife-delete"></a>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<tr>
			<td width="40%" align="right"><?=GetMessage("ESTELIFE_F_SITE")?></td>
			<td width="60%"><input type="text" name="org[web][]" size="60" maxlength="255" value=""><a href="#" class="estelife-more estelife-btn adm-btn adm-btn-save">+</a></td>
		</tr>
		<?php if(!empty($arResult['org']['email'])): ?>
			<?php foreach($arResult['org']['email'] as $sEmail): ?>
				<tr>
					<td width="40%"><?=GetMessage("ESTELIFE_F_EMAIL")?></td>
					<td width="60%">
						<input type="text" name="org[email][]" size="60" maxlength="255" value="<?=$sEmail?>">
						<a href="#" class="estelife-more estelife-btn adm-btn adm-btn-delete estelife-delete"></a>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<tr>
			<td width="40%" align="right"><?=GetMessage("ESTELIFE_F_EMAIL")?></td>
			<td width="60%"><input type="text" name="org[email][]" size="60" maxlength="255" value=""><a href="#" class="estelife-more estelife-btn adm-btn adm-btn-save">+</a></td>
		</tr>
		<?php if(!empty($arResult['org']['phone'])): ?>
			<?php foreach($arResult['org']['phone'] as $sPhone): ?>
				<tr>
					<td width="40%"><?=GetMessage("ESTELIFE_F_PHONE")?></td>
					<td width="60%">
						<input type="text" name="org[phone][]" size="60" maxlength="255" value="<?=VString::formatPhone($sPhone)?>">
						<a href="#" class="estelife-more estelife-btn adm-btn adm-btn-delete estelife-delete"></a>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_PHONE")?></td>
			<td width="60%"><input type="text" name="org[phone][]" size="60" maxlength="255" value=""><a href="#" class="estelife-more estelife-btn adm-btn adm-btn-save">+</a></td>
		</tr>
		<?php if(!empty($arResult['org']['fax'])): ?>
			<?php foreach($arResult['org']['fax'] as $sFax): ?>
				<tr>
					<td width="40%"><?=GetMessage("ESTELIFE_F_FAX")?></td>
					<td width="60%">
						<input type="text" name="org[fax][]" size="60" maxlength="255" value="<?=VString::formatPhone($sFax)?>">
						<a href="#" class="estelife-more estelife-btn adm-btn adm-btn-delete estelife-delete"></a>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_FAX")?></td>
			<td width="60%"><input type="text" name="org[fax][]" size="60" maxlength="255" value=""><a href="#" class="estelife-more estelife-btn adm-btn adm-btn-save">+</a></td>
		</tr>
		<tr>
			<td></td>
			<td><br /><br /><b><?=GetMessage("ESTELIFE_F_DESCRIPTION")?></b></td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_PREVIEW_TEXT")?></td>
			<td width="60%">
				<?CFileMan::AddHTMLEditorFrame(
					"org_preview_text",
					$arResult['org']['preview_text'],
					"org[preview_text_type]",
					$str_preview_text_type,
					array(
						'height' => 300,
						'width' => '100%'
					),
					"N",
					0,
					"",
					"",
					"ru"
				);?>
			</td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_DETAIL_TEXT")?></td>
			<td width="60%">
				<?CFileMan::AddHTMLEditorFrame(
					"org_detail_text",
					$arResult['org']['detail_text'],
					"org[detail_text_type]",
					$str_detail_text_type,
					array(
						'height' => 300,
						'width' => '100%'
					),
					"N",
					0,
					"",
					"",
					"ru"
				);?>
			</td>
		</tr>
		<?php $tabControl->BeginNextTab()?>
		<input type="hidden" name="maker[type]" value="3" />
		<tr class="adm-detail-required-field">
			<td width="40%"><?=GetMessage("ESTELIFE_F_TITLE")?></td>
			<td width="60%"><input type="text" name="maker[name]" size="60" maxlength="255" value="<?=$arResult['maker']['name']?>"></td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_TRANSLIT")?></td>
			<td width="60%"><input type="text" name="maker[translit]" size="60" maxlength="255" value="<?=$arResult['maker']['translit']?>"></td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_LOGO")?></td>
			<td width="60%">
				<?echo CFileInput::Show("maker_logo", $arResult['maker']['logo_id'],
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
			<td></td>
			<td><br /><br /><b><?=GetMessage("ESTELIFE_F_GEO")?></b></td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_COUNTRY")?></td>
			<td width="60%">
				<input type="hidden" name="maker[country_id]" value="<?=$arResult['maker']['country_id']?>" />
				<input type="text" name="maker[country_name]" data-input="country_id" value="<?=$arResult['maker']['country_name']?>" />
			</td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_CITY")?></td>
			<td width="60%">
				<input type="hidden" name="maker[city_id]" value="<?=$arResult['maker']['city_id']?>" />
				<input type="text" name="maker[city_name]" data-input="city_id" value="<?=$arResult['maker']['city_name']?>" />
			</td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_METRO")?></td>
			<td width="60%">
				<input type="hidden" name="maker[metro_id]" value="<?=$arResult['maker']['metro_id']?>" />
				<input type="text"<?=(empty($arResult['maker']['metro_id']) ? ' readonly="readonly"' : '')?> name="maker[metro_name]" data-input="metro_id" value="<?=$arResult['maker']['metro_name']?>" />
			</td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_ADDRESS")?></td>
			<td width="60%">
				<input type="hidden" name="maker[latitude]" value="<?=$arResult['maker']['latitude']?>" />
				<input type="hidden" name="maker[longitude]" value="<?=$arResult['maker']['longitude']?>" />
				<input type="text"<?=(empty($arResult['maker']['city_id']) ? ' readonly="readonly"' : '')?> name="maker[address]" size="60" maxlength="255" value="<?=$arResult['maker']['address']?>">
				<div class="gmap"></div>
			</td>
		</tr>
		<tr>
			<td></td>
			<td><br /><br /><b><?=GetMessage("ESTELIFE_F_CONTACTS")?></b></td>
		</tr>
		<?php if(!empty($arResult['maker']['web'])): ?>
			<?php foreach($arResult['maker']['web'] as $sSite): ?>
				<tr>
					<td width="40%"><?=GetMessage("ESTELIFE_F_SITE")?></td>
					<td width="60%">
						<input type="text" name="maker[web][]" size="60" maxlength="255" value="<?=$sSite?>">
						<a href="#" class="estelife-more estelife-btn adm-btn adm-btn-delete estelife-delete"></a>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<tr>
			<td width="40%" align="right"><?=GetMessage("ESTELIFE_F_SITE")?></td>
			<td width="60%"><input type="text" name="maker[web][]" size="60" maxlength="255" value=""><a href="#" class="estelife-more estelife-btn adm-btn adm-btn-save">+</a></td>
		</tr>
		<?php if(!empty($arResult['maker']['email'])): ?>
			<?php foreach($arResult['maker']['email'] as $sEmail): ?>
				<tr>
					<td width="40%"><?=GetMessage("ESTELIFE_F_EMAIL")?></td>
					<td width="60%">
						<input type="text" name="maker[email][]" size="60" maxlength="255" value="<?=$sEmail?>">
						<a href="#" class="estelife-more estelife-btn adm-btn adm-btn-delete estelife-delete"></a>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<tr>
			<td width="40%" align="right"><?=GetMessage("ESTELIFE_F_EMAIL")?></td>
			<td width="60%"><input type="text" name="maker[email][]" size="60" maxlength="255" value=""><a href="#" class="estelife-more estelife-btn adm-btn adm-btn-save">+</a></td>
		</tr>
		<?php if(!empty($arResult['maker']['phone'])): ?>
			<?php foreach($arResult['maker']['phone'] as $sPhone): ?>
				<tr>
					<td width="40%"><?=GetMessage("ESTELIFE_F_PHONE")?></td>
					<td width="60%">
						<input type="text" name="maker[phone][]" size="60" maxlength="255" value="<?=VString::formatPhone($sPhone)?>">
						<a href="#" class="estelife-more estelife-btn adm-btn adm-btn-delete estelife-delete"></a>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_PHONE")?></td>
			<td width="60%"><input type="text" name="maker[phone][]" size="60" maxlength="255" value=""><a href="#" class="estelife-more estelife-btn adm-btn adm-btn-save">+</a></td>
		</tr>
		<?php if(!empty($arResult['maker']['fax'])): ?>
			<?php foreach($arResult['maker']['fax'] as $sFax): ?>
				<tr>
					<td width="40%"><?=GetMessage("ESTELIFE_F_FAX")?></td>
					<td width="60%">
						<input type="text" name="maker[fax][]" size="60" maxlength="255" value="<?=VString::formatPhone($sFax)?>">
						<a href="#" class="estelife-more estelife-btn adm-btn adm-btn-delete estelife-delete"></a>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_FAX")?></td>
			<td width="60%"><input type="text" name="maker[fax][]" size="60" maxlength="255" value=""><a href="#" class="estelife-more estelife-btn adm-btn adm-btn-save">+</a></td>
		</tr>
		<tr>
			<td></td>
			<td><br /><br /><b><?=GetMessage("ESTELIFE_F_DESCRIPTION")?></b></td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_PREVIEW_TEXT")?></td>
			<td width="60%">
				<?CFileMan::AddHTMLEditorFrame(
					"maker_preview_text",
					$arResult['maker']['preview_text'],
					"maker[preview_text_type]",
					$str_preview_text_type,
					array(
						'height' => 300,
						'width' => '100%'
					),
					"N",
					0,
					"",
					"",
					"ru"
				);?>
			</td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_DETAIL_TEXT")?></td>
			<td width="60%">
				<?CFileMan::AddHTMLEditorFrame(
					"maker_detail_text",
					$arResult['maker']['detail_text'],
					"maker[detail_text_type]",
					$str_detail_text_type,
					array(
						'height' => 300,
						'width' => '100%'
					),
					"N",
					0,
					"",
					"",
					"ru"
				);?>
			</td>
		</tr>
		<?php $tabControl->BeginNextTab()?>
		<input type="hidden" name="learning[type]" value="4" />
		<tr class="adm-detail-required-field">
			<td width="40%"><?=GetMessage("ESTELIFE_F_TITLE")?></td>
			<td width="60%"><input type="text" name="learning[name]" size="60" maxlength="255" value="<?=$arResult['learning']['name']?>"></td>
		</tr>
		<tr >
			<td width="40%"><?=GetMessage("ESTELIFE_F_TRANSLIT")?></td>
			<td width="60%"><input type="text" name="learning[translit]" size="60" maxlength="255" value="<?=$arResult['learning']['translit']?>"></td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_LOGO")?></td>
			<td width="60%">
				<?echo CFileInput::Show("learning_logo", $arResult['learning']['logo_id'],
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
			<td></td>
			<td><br /><br /><b><?=GetMessage("ESTELIFE_F_GEO")?></b></td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_COUNTRY")?></td>
			<td width="60%">
				<input type="hidden" name="learning[country_id]" value="<?=$arResult['learning']['country_id']?>" />
				<input type="text" name="learning[country_name]" data-input="country_id" value="<?=$arResult['learning']['country_name']?>" />
			</td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_CITY")?></td>
			<td width="60%">
				<input type="hidden" name="learning[city_id]" value="<?=$arResult['learning']['city_id']?>" />
				<input type="text" name="learning[city_name]" data-input="city_id" value="<?=$arResult['learning']['city_name']?>" />
			</td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_METRO")?></td>
			<td width="60%">
				<input type="hidden" name="learning[metro_id]" value="<?=$arResult['learning']['metro_id']?>" />
				<input type="text"<?=(empty($arResult['learning']['metro_id']) ? ' readonly="readonly"' : '')?> name="learning[metro_name]" data-input="metro_id" value="<?=$arResult['learning']['metro_name']?>" />
			</td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_ADDRESS")?></td>
			<td width="60%">
				<input type="hidden" name="learning[latitude]" value="<?=$arResult['learning']['latitude']?>" />
				<input type="hidden" name="learning[longitude]" value="<?=$arResult['learning']['longitude']?>" />
				<input type="text"<?=(empty($arResult['learning']['city_id']) ? ' readonly="readonly"' : '')?> name="learning[address]" size="60" maxlength="255" value="<?=$arResult['learning']['address']?>">
				<div class="gmap"></div>
			</td>
		</tr>
		<tr>
			<td></td>
			<td><br /><br /><b><?=GetMessage("ESTELIFE_F_CONTACTS")?></b></td>
		</tr>
		<?php if(!empty($arResult['learning']['web'])): ?>
			<?php foreach($arResult['learning']['web'] as $sSite): ?>
				<tr>
					<td width="40%"><?=GetMessage("ESTELIFE_F_SITE")?></td>
					<td width="60%">
						<input type="text" name="learning[web][]" size="60" maxlength="255" value="<?=$sSite?>">
						<a href="#" class="estelife-more estelife-btn adm-btn adm-btn-delete estelife-delete"></a>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<tr>
			<td width="40%" align="right"><?=GetMessage("ESTELIFE_F_SITE")?></td>
			<td width="60%"><input type="text" name="learning[web][]" size="60" maxlength="255" value=""><a href="#" class="estelife-more estelife-btn adm-btn adm-btn-save">+</a></td>
		</tr>
		<?php if(!empty($arResult['learning']['email'])): ?>
			<?php foreach($arResult['learning']['email'] as $sEmail): ?>
				<tr>
					<td width="40%"><?=GetMessage("ESTELIFE_F_EMAIL")?></td>
					<td width="60%">
						<input type="text" name="learning[email][]" size="60" maxlength="255" value="<?=$sEmail?>">
						<a href="#" class="estelife-more estelife-btn adm-btn adm-btn-delete estelife-delete"></a>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<tr>
			<td width="40%" align="right"><?=GetMessage("ESTELIFE_F_EMAIL")?></td>
			<td width="60%"><input type="text" name="learning[email][]" size="60" maxlength="255" value=""><a href="#" class="estelife-more estelife-btn adm-btn adm-btn-save">+</a></td>
		</tr>
		<?php if(!empty($arResult['learning']['phone'])): ?>
			<?php foreach($arResult['learning']['phone'] as $sPhone): ?>
				<tr>
					<td width="40%"><?=GetMessage("ESTELIFE_F_PHONE")?></td>
					<td width="60%">
						<input type="text" name="learning[phone][]" size="60" maxlength="255" value="<?=VString::formatPhone($sPhone)?>">
						<a href="#" class="estelife-more estelife-btn adm-btn adm-btn-delete estelife-delete"></a>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_PHONE")?></td>
			<td width="60%"><input type="text" name="learning[phone][]" size="60" maxlength="255" value=""><a href="#" class="estelife-more estelife-btn adm-btn adm-btn-save">+</a></td>
		</tr>
		<?php if(!empty($arResult['learning']['fax'])): ?>
			<?php foreach($arResult['learning']['fax'] as $sFax): ?>
				<tr>
					<td width="40%"><?=GetMessage("ESTELIFE_F_FAX")?></td>
					<td width="60%">
						<input type="text" name="learning[fax][]" size="60" maxlength="255" value="<?=VString::formatPhone($sFax)?>">
						<a href="#" class="estelife-more estelife-btn adm-btn adm-btn-delete estelife-delete"></a>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_FAX")?></td>
			<td width="60%"><input type="text" name="learning[fax][]" size="60" maxlength="255" value=""><a href="#" class="estelife-more estelife-btn adm-btn adm-btn-save">+</a></td>
		</tr>
		<tr>
			<td></td>
			<td><br /><br /><b><?=GetMessage("ESTELIFE_F_DESCRIPTION")?></b></td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_PREVIEW_TEXT")?></td>
			<td width="60%">
				<?CFileMan::AddHTMLEditorFrame(
					"learning_preview_text",
					$arResult['learning']['preview_text'],
					"learning[preview_text_type]",
					$str_preview_text_type,
					array(
						'height' => 300,
						'width' => '100%'
					),
					"N",
					0,
					"",
					"",
					"ru"
				);?>
			</td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_DETAIL_TEXT")?></td>
			<td width="60%">
				<?CFileMan::AddHTMLEditorFrame(
					"learning_detail_text",
					$arResult['learning']['detail_text'],
					"learning[detail_text_type]",
					$str_detail_text_type,
					array(
						'height' => 300,
						'width' => '100%'
					),
					"N",
					0,
					"",
					"",
					"ru"
				);?>
			</td>
		</tr>
		<?php
		$tabControl->EndTab();
		$tabControl->Buttons(array("disabled"=>false, "back_url"=>(strlen($back_url) > 0 ? $back_url : "estelife_company_list.php?lang=".LANGUAGE_ID)));
		$tabControl->End();
		?>
	</form>
<?php
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");