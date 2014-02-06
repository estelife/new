<?php
use core\database\mysql\VFilter;
use core\types\VArray;
use core\exceptions as ex;
use core\types\VString;
use core\types\VDate;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/estelife/prolog.php");

ClearVars();
$FORM_RIGHT=$APPLICATION->GetGroupRight("estelife");


$arEmpties = array(
	'translit',
	'logo_id',
	'country_id',
	'city_id',
	'metro_id',
	'address',
	'dop_address',
	'web',
	'preview_text',
	'detail_text',
	'directions',
	'email',
	'faxes',
	'phones',
	'dates',
	'companies'
);

if($FORM_RIGHT<="D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

CModule::IncludeModule("estelife");
IncludeModuleLangFile(__FILE__);

define("HELP_FILE","estelife_list.php");
$ID = isset($_REQUEST['ID']) ?
	intval($_REQUEST['ID']) : 0;

$arResult['event']['types']=array();
$arResult['event']['directions']=array();
$obQuery=\core\database\VDatabase::driver()->createQuery();

if(!empty($ID)){
	$obQuery->builder()
		->from('estelife_events','ee')
		->field('cn.ID','country_id')
		->field('cn.NAME','country_name')
		->field('ct.ID','city_id')
		->field('ct.NAME','city_name')
		->field('mt.ID','metro_id')
		->field('mt.NAME','metro_name')
		->field('eco.name','company_name')
		->field('eco.id','company_id')
		->field('ect.name','company_type_name')
		->field('ee.*');
	$obJoin=$obQuery->builder()->join();
	$obJoin->_left()->
		_from('ee','country_id')->
		_to('iblock_element','ID','cn')->
		_cond()->_eq('cn.IBLOCK_ID',15);
	$obJoin->_left()->
		_from('ee','city_id')->
		_to('iblock_element','ID','ct')->
		_cond()->_eq('ct.IBLOCK_ID',16);
	$obJoin->_left()->
		_from('ee','metro_id')->
		_to('iblock_element','ID','mt')->
		_cond()->_eq('mt.IBLOCK_ID',17);
	$obJoin->_left()->
		_from('ee','id')->
		_to('estelife_company_events','event_id','ece');
	$obJoin->_left()->
		_from('ece','company_id')->
		_to('estelife_companies','id','eco');
	$obJoin->_left()
		->_from('eco','id')
		->_to('estelife_company_types','company_id','ect')
		->_cond()->_eq('ect.type',2);

	$obQuery->builder()->filter()->_eq('ee.id',$ID);
	$arResult['event']=$obQuery->select()->assoc();

	$obQuery->builder()
		->from('estelife_event_contacts')
		->filter()
		->_eq('event_id',$arResult['event']['id']);
	$arContacts=$obQuery->select()->all();

	if(!empty($arContacts)){
		foreach($arContacts as $arContact){
			if($arContact['type']=='email')
				$arResult['event']['email'][]=$arContact['value'];
			else if($arContact['type']=='phone')
				$arResult['event']['phones'][]=$arContact['value'];
			else if($arContact['type']=='fax')
				$arResult['event']['faxes'][]=$arContact['value'];
		}
	}

	$obQuery->builder()
		->from('estelife_calendar')
		->filter()
		->_eq('event_id',$arResult['event']['id']);
	$arDates=$obQuery->select()->all();

	if(!empty($arDates)){
		foreach($arDates as &$arDate){
			$arDate['date']=VDate::date($arDate['date']);
		}
		$arResult['event']['dates']=$arDates;
	}

	$obJoin=$obQuery->builder()
		->from('estelife_company_events','ece')
		->field('ec.name','company_name')
		->field('ec.id','company_id')
		->field('ect.name','company_type_name')
		->join();
	$obJoin->_left()
		->_from('ece','company_id')
		->_to('estelife_companies','id','ec');
	$obJoin->_left()
		->_from('ec','id')
		->_to('estelife_company_types','company_id','ect')
		->_cond()
		->_eq('ect.type',2);

	$obQuery->builder()
		->filter()
		->_eq('ece.event_id',$ID)
		->_eq('ece.is_owner',0);
	$arCompanies=$obQuery->select()->all();

	if(!empty($arCompanies))
		$arResult['event']['companies']=$arCompanies;

	$obQuery->builder()
		->from('estelife_event_types')
		->filter()
		->_eq('event_id',$ID);
	$arTypes=$obQuery->select()->all();

	if(!empty($arTypes)){
		$arTemp=array();
		foreach($arTypes as $arType){
			$arTemp[]=$arType['type'];
		}
		$arResult['event']['types']=$arTemp;
	}

	$obQuery->builder()
		->from('estelife_event_directions')
		->filter()
		->_eq('event_id',$ID);
	$arDirections=$obQuery->select()->all();

	if(!empty($arDirections)){
		$arTemp=array();
		foreach($arDirections as $arDirection){
			$arTemp[]=$arDirection['type'];
		}
		$arResult['event']['directions']=$arTemp;
	}

	foreach ($arEmpties as $val){
		if (isset($arResult['event'][$val]) && !empty($arResult['event'][$val])){

		}else{
			$arResult['warnings'][] = $val;
		}
	}

	$obJoin=$obQuery->builder()
		->from('estelife_event_galleries','eeg')
		->field('eg.*')
		->join();
	$obJoin->_left()
		->_from('eeg','gallery_id')
		->_to('estelife_galleries','id','eg');

	$obQuery->builder()
		->sort('eg.date_add','desc')
		->filter()
		->_eq('eeg.event_id',$ID);

	$arResult['event']['galleries']=$obQuery->select()->all();
}

if(!empty($_SESSION['temp_gallery'])){
	$obQuery->builder()
		->from('estelife_galleries')
		->sort('date_add','desc')
		->filter()
		->_in('id',$_SESSION['temp_gallery']);

	$arGalleries=$obQuery->select()->all();

	if(is_array($arResult['event']['galleries'])){
		$arResult['event']['galleries']=array_merge($arGalleries,$arResult['event']['galleries']);
	}else{
		$arResult['event']['galleries']=$arGalleries;
	}
}

if(!empty($arResult['event']['galleries'])){
	$arTemp=array();

	foreach($arResult['event']['galleries'] as $arGallery){
		$arGallery['photos']=array();
		$arTemp[$arGallery['id']]=$arGallery;
	}

	$obQuery->builder()
		->from('estelife_photos')
		->filter()
		->_in('gallery_id',array_keys($arTemp));
	$arPhotos=$obQuery->select()->all();

	if(!empty($arPhotos)){
		foreach($arPhotos as $arPhoto){
			if(!empty($arPhoto['image_id']))
				$arPhoto['image']=CFile::GetByID($arPhoto['image_id'])->Fetch();
			$arTemp[$arPhoto['gallery_id']]['photos'][]=$arPhoto;
		}
	}

	$arResult['event']['galleries']=$arTemp;
}

if($_SERVER['REQUEST_METHOD']=='POST'){
	$obPost=new VArray($_POST);
	$obError=new ex\VFormException();


	try{
		if($obPost->blank('company_id'))
			$obError->setFieldError('COMPANY_NOT_FILL','company_id');

//		if($obPost->blank('full_name'))
//			$obError->setFieldError('FULL_NAME_NOT_FILL','full_name');

		if($obPost->blank('short_name'))
			$obError->setFieldError('SHORT_NAME_NOT_FILL','short_name');

		if($obPost->blank('city_id') && !$obPost->blank('city_name')){
			$sName=$obPost->blank('city_name');
			$obQuery=\core\database\VDatabase::driver()->createQuery();
			$obQuery->builder()->
				from('iblock_element')->
				filter()->
				_eq('IBLOCK_ID',16)->
				_like('NAME',$sName,VFilter::LIKE_BEFORE|VFilter::LIKE_AFTER);
			$obResult=$obQuery->select();

			if($obResult->count()>0){
				$arCity=$obResult->assoc();
				$obPost->set('city_id',$arCity['id']);
			}
		}

		$obError->raise();

		if($obPost->blank('translit'))
			$obPost->set('translit',\core\types\VString::translit($obPost->one('short_name')));

		$obQuery=\core\database\VDatabase::driver()->createQuery();
		$obBuilder=$obQuery->builder();
		$obBuilder->from('estelife_events')->
			value('full_name',trim(strip_tags($obPost->one('full_name'))))->
			value('short_name',trim(strip_tags($obPost->one('short_name'))))->
			value('translit',trim(strip_tags($obPost->one('translit',''))))->
			value('web',strip_tags($obPost->one('web','')))->
			value('country_id',intval($obPost->one('country_id',0)))->
			value('city_id',intval($obPost->one('city_id',0)))->
			value('metro_id',intval($obPost->one('metro_id',0)))->
			value('address',htmlentities(strip_tags($obPost->one('address','')),ENT_QUOTES, 'utf-8'))->
			value('dop_address',htmlentities(strip_tags($obPost->one('dop_address','')),ENT_QUOTES, 'utf-8'))->
			value('dop_web',strip_tags($obPost->one('dop_web','')))->
			value('date_create',time())->
			value('preview_text',htmlentities($obPost->one('preview_text',''),ENT_QUOTES,'utf-8'))->
			value('detail_text',htmlentities($obPost->one('detail_text',''),ENT_QUOTES,'utf-8'))->
			value('latitude',floatval($obPost->one('latitude')))->
			value('longitude',floatval($obPost->one('longitude')));

		if(!empty($_FILES['logo'])){
			$arImage=$_FILES['logo'];
			$arImage['old_file']=$obRecord['logo_id'];
			$arImage['module']='estelife';
			$arImage['del']=$logo_del;

			if(strlen($arImage["name"])>0 || strlen($arImage["del"])>0){
				$nImageId=CFile::SaveFile($arImage, "estelife");
				$obBuilder->value("logo_id",intval($nImageId));
			}
		}

		if(!empty($ID)){
			$obQuery->builder()->filter()->_eq('id',$ID);
			$obQuery->update();
			$obQuery->builder()
				->from('estelife_event_contacts')
				->filter()
				->_eq('event_id', $ID);
			$obQuery->delete();
			$obQuery->builder()
				->from('estelife_calendar')
				->filter()
				->_eq('event_id',$ID);
			$obQuery->delete();
			$obQuery->builder()
				->from('estelife_event_types')
				->filter()
				->_eq('event_id',$ID);
			$obQuery->delete();
			$obQuery->builder()
				->from('estelife_event_directions')
				->filter()
				->_eq('event_id',$ID);
			$obQuery->delete();

		}else{
			$obResult=$obQuery->insert();
			$ID=$obResult->insertId();
		}

		$nCompanyId=$obPost->one('company_id');
		$obQuery->builder()
			->from('estelife_company_events')
			->filter()
			->_eq('event_id',$ID);
		$obQuery->delete();
		$obQuery->builder()
			->from('estelife_company_events')
			->value('event_id',$ID)
			->value('company_id',$nCompanyId)
			->value('is_owner',1);
		$obQuery->insert();


		if($arEmails=$obPost->one('email')){
			$bFlag=false;
			foreach($arEmails as $sEmail){
				if(!\core\types\VString::isEmail($sEmail))
					continue;

				$bFlag=true;
				$obQuery->builder()
					->from('estelife_event_contacts')
					->value('type','email')
					->value('value',$sEmail)
					->value('event_id',$ID);
				$obQuery->insert();
			}
			$obPost->set('emails',$bFlag);
		}

		if($arPhones=$obPost->one('phones')){
			$bFlag=false;
			foreach($arPhones as $sPhone){
				if(!\core\types\VString::isPhone($sPhone))
					continue;

				$bFlag=true;
				$obQuery->builder()
					->from('estelife_event_contacts')
					->value('type','phone')
					->value('value',$sPhone)
					->value('event_id',$ID);
				$obQuery->insert();
			}
			$obPost->set('phones',$bFlag);
		}

		if($arFaxes=$obPost->one('faxes')){
			$bFlag=false;
			foreach($arFaxes as $sFax){
				if(!\core\types\VString::isPhone($sFax))
					continue;

				$bFlag=true;
				$obQuery->builder()
					->from('estelife_event_contacts')
					->value('type','fax')
					->value('value',$sFax)
					->value('event_id',$ID);
				$obQuery->insert();
			}
			$obPost->set('faxes',$bFlag);
		}

		if($arDates=$obPost->one('date')){
			$arTimeFrom=$obPost->one('time_from',array());
			$arTimeTo=$obPost->one('time_to',array());
			$bFlag=false;

			foreach($arDates as $nKey=>$sDate){
				if(empty($sDate) || !preg_match('#^[0-9]{1,2}\s[a-zа-я]+\s[0-9]{4}$#ui',$sDate))
					continue;


				$bFlag=true;
				$obQuery->builder()
					->from('estelife_calendar');

				$nDate=VDate::dateToTime($sDate);
				$obQuery->builder()
					->value('date',$nDate)
					->value('event_id',$ID);

				if(!empty($arTimeFrom[$nKey]))
					$obQuery->builder()->value('time_from',$arTimeFrom[$nKey]);

				if(!empty($arTimeTo[$nKey]))
					$obQuery->builder()->value('time_to',$arTimeTo[$nKey]);

				$obQuery->insert();
			}

			$obPost->set('date',$bFlag);
		}

		if($arCompanies=$obPost->one('companies')){
			$arCompanies=$arCompanies['company_id'];
			$bFlag = false;

			foreach($arCompanies as $nCompany){
				$nCompany=intval($nCompany);

				if(empty($nCompany))
					continue;

				$bFlag = true;
				$obQuery->builder()
					->from('estelife_company_events')
					->value('company_id',$nCompany)
					->value('event_id',$ID)
					->value('is_owner',0);
				$obQuery->insert();
			}

			$obPost->set('companies',$bFlag);
		}

		if($arTypes=$obPost->one('types',array(1))){
			$bFlag = false;

			foreach($arTypes as $nType){
				$nType=intval($nType);

				if(empty($nType))
					continue;

				$bFlag = true;
				$obQuery->builder()
					->from('estelife_event_types')
					->value('type',$nType)
					->value('event_id',$ID);
				$obQuery->insert();
			}
			$obPost->set('types', $bFlag);
		}

		if($arDirections=$obPost->one('directions')){
			$bFlag = false;
			foreach($arDirections as $nDirection){
				$nType=intval($nDirection);

				if(empty($nDirection))
					continue;

				$bFlag = true;
				$obQuery->builder()
					->from('estelife_event_directions')
					->value('type',$nDirection)
					->value('event_id',$ID);
				$obQuery->insert();
			}
			$obPost->set('directions', $bFlag);
		}

		$cFlag = false;
		foreach ($obPost->all() as $key=>$val){
			if (in_array($key, $arEmpties) && empty($val)){
				$cFlag = false;
				$arAct = json_decode($_COOKIE['activity'], true);

				if (empty($arAct[$ID]) ){
					$arAct[$ID] = array(
						'id' => $ID,
						'name' => $obPost->one('full_name')
					);
				}
				setcookie('activity', json_encode($arAct), time() + 12*60*60*24*30, '/bitrix/admin');
				break;
			}else{
				$cFlag = true;
			}
		}

		if ($cFlag == true){
			$arAct = json_decode($_COOKIE['activity'], true);
			if (!empty($arAct[$ID]))
				unset($arAct[$ID]);

			setcookie('activity', json_encode($arAct), time() + 12*60*60*24*30, '/bitrix/admin');
		}

		if(isset($_SESSION['temp_gallery'])){
			$arGalleries=$_SESSION['temp_gallery'];
			if(is_array($arGalleries) && !empty($arGalleries)){
				foreach($arGalleries as $nGallery){
					$obQuery->builder()
						->from('estelife_event_galleries')
						->value('gallery_id',$nGallery)
						->value('event_id',$ID);
					$obQuery->insert();
				}
			}
			unset($_SESSION['temp_galleries']);
		}

		if(!$obPost->blank('save'))
			LocalRedirect('/bitrix/admin/estelife_activity_list.php?lang='.LANGUAGE_ID);
		else
			LocalRedirect('/bitrix/admin/estelife_activity_edit.php?lang='.LANGUAGE_ID.'&ID='.$ID);
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
	array("DIV" => "edit2", "TAB" => GetMessage("ESTELIFE_T_PREVIEW_TEXT"), "ICON" => "estelife_r_base", "TITLE" => GetMessage("ESTELIFE_T_PREVIEW_TEXT")),
	array("DIV" => "edit3", "TAB" => GetMessage("ESTELIFE_T_DETAIL_TEXT"), "ICON" => "estelife_r_base", "TITLE" => GetMessage("ESTELIFE_T_DETAIL_TEXT")),
	array("DIV" => "edit4", "TAB" => GetMessage("ESTELIFE_T_CALENDAR"), "ICON" => "estelife_r_base", "TITLE" => GetMessage("ESTELIFE_T_CALENDAR")),
	array("DIV" => "edit5", "TAB" => GetMessage("ESTELIFE_T_CONTACTS"), "ICON" => "estelife_r_base", "TITLE" => GetMessage("ESTELIFE_T_CONTACTS")),
	array("DIV" => "edit6", "TAB" => GetMessage("ESTELIFE_T_VIDEO"), "ICON" => "estelife_r_base", "TITLE" => GetMessage("ESTELIFE_T_VIDEO")),
	array("DIV" => "edit7", "TAB" => GetMessage("ESTELIFE_T_PHOTO"), "ICON" => "estelife_r_base", "TITLE" => GetMessage("ESTELIFE_T_PHOTO"))
);
$tabControl = new CAdminTabControl("estelife_activity_".$ID, $aTabs, true, true);
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
			$arResult['activity'][$sKey]=$sValue;
	}
}
?>

	<script type="text/javascript" src="/bitrix/js/estelife/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/jquery-ui-1.10.3.custom.min.js"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/jquery-ui.rus.js"></script>
	<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyAZfcZn-KLKm52_chZk22TGMdooeDvMYfI&sensor=false"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/vMapStyle.js"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/vMap.js"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/jquery.damnUploader.js"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/estelife.js"></script>

	<?php if (!empty($arResult['warnings'])):?>
		<div class="adm-info-message-wrap adm-info-message-yellow">
			<div class="adm-info-message">
				<div class="adm-info-message-title">Вы не заполнили полностью следующие поля:</div>
					<?php foreach($arResult['warnings'] as $val):?>
						<?=GetMessage("ESTELIFE_F_".$val)?><br />
					<?php endforeach?>
			</div>
		</div>
	<?endif?>

	<form name="estelife_spec" method="POST" action="<?echo $APPLICATION->GetCurPage()?>" enctype="multipart/form-data">
		<?=bitrix_sessid_post()?>
		<input type="hidden" name="ID" value=<?=$ID?> />
		<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>" />
		<?php
		$tabControl->Begin();
		$tabControl->BeginNextTab()
		?>
		<tr>
			<td colspan="2" class="estelife-sep">
				<span><?=GetMessage("ESTELIFE_H_BASE")?></span>
			</td>
		</tr>

		<tr class="adm-detail-required-field">
			<td width="40%"><?=GetMessage("ESTELIFE_F_COMPANY")?></td>
			<td width="60%">
				<input type="hidden" name="company_type_id" value="2" />
				<input type="hidden" name="company_id" value="<?=$arResult['event']['company_id']?>" />
				<?php if (!empty($arResult['event']['company_type_name'])):?>
					<input type="text" name="company_name" data-input="company_id" value="<?=$arResult['event']['company_type_name']?>" />
				<?php else:?>
					<input type="text" name="company_name" data-input="company_id" value="<?=$arResult['event']['company_name']?>" />
				<?php endif?>
			</td>
		</tr>

		<tr class="adm-detail-required-field">
			<td width="40%"><?=GetMessage("ESTELIFE_F_SHORT_NAME")?></td>
			<td width="60%"><input type="text" name="short_name" size="60" maxlength="255" value="<?=$arResult['event']['short_name']?>"></td>
		</tr>

		<tr class="adm-detail-required-field">
			<td width="40%"><?=GetMessage("ESTELIFE_F_FULL_NAME")?></td>
			<td width="60%"><input type="text" name="full_name" size="60" maxlength="255" value="<?=$arResult['event']['full_name']?>"></td>
		</tr>

		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_TRANSLIT")?></td>
			<td width="60%"><input type="text" name="translit" size="60" maxlength="255" value="<?=$arResult['event']['translit']?>"></td>
		</tr>

		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_LOGO")?></td>
			<td width="60%">
				<?echo CFileInput::Show("logo", $arResult['event']['logo_id'],
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
			<td colspan="2" class="estelife-sep">
				<span><?=GetMessage("ESTELIFE_H_ADDRESS")?></span>
			</td>
		</tr>

		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_COUNTRY")?></td>
			<td width="60%">
				<input type="hidden" name="country_id" size="60" maxlength="255" value="<?=$arResult['event']['country_id']?>">
				<input type="text" name="country_name" data-input="country_id" size="60" maxlength="255" value="<?=$arResult['event']['country_name']?>">
			</td>
		</tr>

		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_CITY")?></td>
			<td width="60%">
				<input type="hidden" name="city_id" size="60" maxlength="255" value="<?=$arResult['event']['city_id']?>">
				<input type="text" name="city_name" data-input="city_id" size="60" maxlength="255" value="<?=$arResult['event']['city_name']?>">
			</td>
		</tr>

		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_METRO")?></td>
			<td width="60%">
				<input type="hidden" name="metro_id" value="<?=$arResult['event']['metro_id']?>">
				<input type="text" name="metro_name" data-input="metro_id"<?=(empty($arResult['event']['city_id'])) ? ' readonly="true"' : ''?> size="60" maxlength="255" value="<?=$arResult['event']['metro_name']?>">
			</td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_DOP_ADDRESS")?></td>
			<td width="60%">
				<input type="text" name="dop_address" size="60" maxlength="255" value="<?=$arResult['event']['dop_address']?>">
			</td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_DOP_WEB")?></td>
			<td width="60%">
				<input type="text" name="dop_web" size="60" maxlength="255" value="<?=$arResult['event']['dop_web']?>">
			</td>
		</tr>
		<tr>
			<td width="40%" valign="top"><?=GetMessage("ESTELIFE_F_ADDRESS")?></td>
			<td width="60%">
				<input type="hidden" name="latitude" value="<?=$arResult['event']['latitude']?>" />
				<input type="hidden" name="longitude" value="<?=$arResult['event']['longitude']?>" />
				<input type="text" name="address"<?=(empty($arResult['event']['city_id'])) ? ' readonly="true"' : ''?> size="60" maxlength="255" value="<?=$arResult['event']['address']?>">
				<div class="gmap"></div>
			</td>
		</tr>


		<tr>
			<td colspan="2" class="estelife-sep">
				<span><?=GetMessage("ESTELIFE_H_TYPES")?></span>
			</td>
		</tr>

		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_TYPES")?></td>
			<td width="60%">
				<ul class="estelife-checklist">
					<li>
						<label for="type_1"><input type="checkbox" name="types[]" id="type_1" value="1"<?=(in_array(1,$arResult['event']['types']) || empty($arResult['event']['types']) ? ' checked="true"' : '')?> />Форум</label>
					</li>
					<li>
						<label for="type_2"><input type="checkbox" name="types[]" id="type_2" value="2"<?=(in_array(2,$arResult['event']['types']) ? ' checked="true"' : '')?> />Выставка</label>
					</li>
					<li>
						<label for="type_4"><input type="checkbox" name="types[]" id="type_4" value="4"<?=(in_array(4,$arResult['event']['types']) ? ' checked="true"' : '')?> />Тренинг</label>
					</li>
				</ul>
			</td>
		</tr>

		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_DIRECTIONS")?></td>
			<td width="60%">
				<ul class="estelife-checklist">
					<li>
						<label for="direction_1"><input type="checkbox" name="directions[]" id="direction_1" value="1"<?=(in_array(1,$arResult['event']['directions']) ? ' checked="true"' : '')?> />Пластическая хирургия</label>
					</li>
					<li>
						<label for="direction_2"><input type="checkbox" name="directions[]" id="direction_2" value="2"<?=(in_array(2,$arResult['event']['directions']) ? ' checked="true"' : '')?> />Косметология</label>
					</li>
					<li>
						<label for="direction_4"><input type="checkbox" name="directions[]" id="direction_4" value="4"<?=(in_array(4,$arResult['event']['directions']) ? ' checked="true"' : '')?> />Дерматология</label>
					</li>
					<li>
						<label for="direction_3"><input type="checkbox" name="directions[]" id="direction_3" value="3"<?=(in_array(3,$arResult['event']['directions']) ? ' checked="true"' : '')?> />Косметика</label>
					</li>
					<li>
						<label for="direction_11"><input type="checkbox" name="directions[]" id="direction_11" value="11"<?=(in_array(11,$arResult['event']['directions']) ? ' checked="true"' : '')?> />Менеджмент</label>
					</li>
				</ul>
			</td>
		</tr>

		<tr>
			<td colspan="2" class="estelife-sep">
				<span><?=GetMessage("ESTELIFE_H_COMPANIES")?></span>
			</td>
		</tr>

		<?php if(!empty($arResult['event']['companies'])): ?>
			<?php foreach($arResult['event']['companies'] as $arCompany): ?>
				<tr>
					<td width="30%"><?=GetMessage("ESTELIFE_F_COMPANIES")?></td>
					<td width="70%">
						<input type="hidden" name="companies[company_id][]" value="<?=$arCompany['company_id']?>" />
						<input type="text" disabled="disabled" name="companies[company_name][]" data-input="company_id" class="estelife-need-clone" value="<?=(!empty($arCompany['company_type_name']) ? $arCompany['company_type_name'] : $arCompany['company_name'])?>" />
						<a href="#" class="estelife-more estelife-btn adm-btn adm-btn-delete estelife-delete"></a>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<tr>
			<td width="30%"><?=GetMessage("ESTELIFE_F_COMPANIES")?></td>
			<td width="70%">
				<input type="hidden" name="company_type_id" value="2" />
				<input type="hidden" name="companies[company_id][]" value="" />
				<input type="text" name="companies[company_name][]" data-input="company_id" class="estelife-need-clone" value="" />
				<a href="#" class="estelife-more estelife-btn adm-btn adm-btn-save">&crarr;</a>
			</td>
		</tr>

		<?
		$tabControl->BeginNextTab()
		?>

		<tr id="tr_preview_text_editor">
			<td colspan="2" align="center">
				<?CFileMan::AddHTMLEditorFrame(
					"preview_text",
					$arResult['event']['preview_text'],
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
					LANGUAGE_ID
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
					$arResult['event']['detail_text'],
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
					LANGUAGE_ID
				);?>
			</td>
		</tr>

		<?
		$tabControl->BeginNextTab()
		?>

		<tr>
			<td colspan="2">
				<div class="event-dates">
					<ul>
						<?php if(!empty($arResult['event']['dates'])):?>
							<?php foreach($arResult['event']['dates'] as $arValue): ?>
								<li>
									<input type="text" name="date[]" value="<?=$arValue['date']?>" class="date_select" /> c <input type="text" value="<?=$arValue['time_from']?>" name="time_from[]" class="time" size="5" /> по <input type="text" class="time" size="5" name="time_to[]" value="<?=$arValue['time_to']?>" />
									<a href="#" class="estelife-btn adm-btn adm-btn-delete estelife-delete"></a>
								</li>
							<?php endforeach; ?>
						<?php endif;?>
						<li>
							<input type="text" name="date[]" value="" class="date_select" /> c <input type="text" value="" name="time_from[]" class="time" size="5" /> по <input type="text" class="time" size="5" name="time_to[]" value="" />
							<a href="#" class="estelife-btn adm-btn adm-btn-save">&crarr;</a>
						</li>
					</ul>
				</div>
				<div class="calendar_l">
					<div class="calendar_l_in">
						<a href="#" class="ar l" id="lc">август<span> 2013 </span><i></i></a>
						<div class="cal l">
							<div id="datepicker1"></div>
						</div>
						<div class="cal r">
							<div id="datepicker2"></div>
						</div>
						<a href="#" class="ar r" id="rc">ноябрь<span> 2013 </span><i></i></a>
					</div>
				</div>
			</td>
		</tr>

		<?
		$tabControl->BeginNextTab()
		?>

		<tr>
			<td width="30%"><?=GetMessage("ESTELIFE_F_WEB")?></td>
			<td width="70%"><input type="text" name="web" size="60" maxlength="255" value="<?=$arResult['event']['web']?>"></td>
		</tr>

		<?php if(!empty($arResult['event']['email'])): ?>
			<?php foreach($arResult['event']['email'] as $sEmail): ?>
				<tr>
					<td width="30%"><?=GetMessage("ESTELIFE_F_EMAIL")?></td>
					<td width="70%" class="estelife-cell">
						<input type="text" name="email[]" size="60" maxlength="255" value="<?=$sEmail?>" />
						<a href="#" class="estelife-more estelife-btn adm-btn adm-btn-delete estelife-delete"></a>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<tr>
			<td width="30%" align="right"><?=GetMessage("ESTELIFE_F_EMAIL")?></td>
			<td width="70%"><input type="text" name="email[]" size="60" maxlength="255" value="" /><a href="#" class="estelife-more estelife-btn adm-btn adm-btn-save">+</a></td>
		</tr>

		<tr><td colspan="2" class="estelife-sep"><span>Факсы</span></td></tr>

		<?php if(!empty($arResult['event']['faxes'])): ?>
			<?php foreach($arResult['event']['faxes'] as $sFax): ?>
				<tr>
					<td width="30%"><?=GetMessage("ESTELIFE_F_FAX")?></td>
					<td width="70%" class="estelife-cell">
						<input type="text" name="faxes[]" size="60" maxlength="255" value="<?=VString::formatPhone($sFax)?>">
						<a href="#" class="estelife-more estelife-btn adm-btn adm-btn-delete estelife-delete"></a>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<tr>
			<td width="30%"><?=GetMessage("ESTELIFE_F_FAX")?></td>
			<td width="70%" class="estelife-cell"><input type="text" name="faxes[]" size="60" maxlength="255" value=""><a href="#" class="estelife-more estelife-btn adm-btn adm-btn-save">+</a></td>
		</tr>

		<tr><td colspan="2" class="estelife-sep"><span>Телефоны</span></td></tr>

		<?php if(!empty($arResult['event']['phones'])): ?>
			<?php foreach($arResult['event']['phones'] as $sPhone): ?>
				<tr>
					<td width="30%"><?=GetMessage("ESTELIFE_F_PHONE")?></td>
					<td width="70%" class="estelife-cell">
						<input type="text" name="phones[]" size="60" maxlength="255" value="<?=VString::formatPhone($sPhone)?>">
						<a href="#" class="estelife-more estelife-btn adm-btn adm-btn-delete estelife-delete"></a>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<tr>
			<td width="30%"><?=GetMessage("ESTELIFE_F_PHONE")?></td>
			<td width="70%" class="estelife-cell">
				<input type="text" name="phones[]" size="60" maxlength="255" value=""><a href="#" class="estelife-more estelife-btn adm-btn adm-btn-save">+</a>
			</td>
		</tr>

		<?
		$tabControl->BeginNextTab()
		?>

		<tr>
			<td width="40%"></td>
			<td width="60%"><?=GetMessage('unsupported_video')?></td>
		</tr>

		<?
		$tabControl->BeginNextTab()
		?>

		<tr>
			<td clospan="2">
				<div class="estelife-gallery">
					<div class="gallery-add">
						<span>Добавить галлерею</span>
						<input type="text" value="" />
						<a href="#" class="estelife-btn adm-btn adm-btn-save">&crarr;</a>
					</div>

					<div class="gallery-list">
						<?php if(!empty($arResult['event']['galleries'])):?>
							<?php foreach($arResult['event']['galleries'] as $arGallery): ?>
								<div class="gallery-item" data-id="<?=$arGallery['id']?>">
									<h2><?=$arGallery['name']?></h2>
									<a href="#" class="estelife-btn adm-btn adm-btn-delete estelife-delete gallery-delete"></a>
									<div class="gallery-item-photos">
										<?php if(!empty($arGallery['photos'])): ?>
											<table class="drop-table">
												<tr class="drop-list">
												<?php foreach($arGallery['photos'] as  $arPhoto): ?>
													<td>
														<div class="drop-item" data-id="<?=$arPhoto['id']?>">
															<div class="drop-image">
																<img src="/upload/<?=$arPhoto['image']['SUBDIR']?>/<?=$arPhoto['image']['FILE_NAME']?>" />
															</div>
															<div class="drop-name"><?=$arPhoto['image']['ORIGINAL_NAME']?></div>
															<a href="#" class="estelife-btn adm-btn adm-btn-delete estelife-delete photo-delete"></a>
														</div>
													</td>
												<?php endforeach; ?>
												</tr>
											</table>
										<?php endif; ?>
									</div>
								</div>
							<?php endforeach;?>
						<?php endif; ?>
						<div class="gallery-item gallery-item-template">
							<h2></h2>
							<a href="#" class="estelife-btn adm-btn adm-btn-delete estelife-delete gallery-delete"></a>
							<div class="gallery-item-photos">

							</div>
						</div>
					</div>
				</div>
			</td>
		</tr>
		<?php
		$tabControl->EndTab();
		$tabControl->Buttons(array("disabled"=>false, "back_url"=>(strlen($back_url) > 0 ? $back_url : "estelife_activity_list.php?lang=".LANGUAGE_ID)));
		$tabControl->End();
		?>
	</form>
<?php
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");