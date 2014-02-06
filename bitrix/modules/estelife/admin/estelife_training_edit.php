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

if($FORM_RIGHT<="D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

CModule::IncludeModule("estelife");
IncludeModuleLangFile(__FILE__);

define("HELP_FILE","estelife_list.php");
$ID = isset($_REQUEST['ID']) ?
	intval($_REQUEST['ID']) : 0;

$arResult['event']['types']=array();
$arResult['event']['directions']=array();

if(!empty($ID)){
	$obQuery=\core\database\VDatabase::driver()->createQuery();
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
		->_cond()->_eq('ect.type',4);
	$obQuery->builder()->filter()
		->_eq('ee.id', $ID);

	$arResult['event']=$obQuery->select()->assoc();


	$obQuery->builder()
		->from('estelife_event_contacts')
		->filter()
		->_eq('event_id',$arResult['event']['id']);
	$arContacts=$obQuery->select()->all();

	if(!empty($arContacts)){
		foreach($arContacts as $arContact){
			if($arContact['type']=='email')
				$arResult['event']['email']=$arContact['value'];
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
		->_eq('ect.type',4);

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

	//Получение препаратов
	$obQuery->builder()
		->from('estelife_event_pills', 'eep');
	$obJoin = $obQuery->builder()->join();
	$obJoin->_left()
		->_from('eep', 'pill_id')
		->_to('estelife_pills', 'id', 'ep');
	$obQuery->builder()
		->field('eep.id', 'pill_id')
		->field('ep.name', 'pill_name');
	$obQuery->builder()->filter()
		->_eq('eep.event_id', $ID);
	$arResult['event']['pills'] = $obQuery->select()->all();

	//Получение аппаратов
	$obQuery->builder()
		->from('estelife_event_apparatus', 'eea');
	$obJoin = $obQuery->builder()->join();
	$obJoin->_left()
		->_from('eea', 'apparatus_id')
		->_to('estelife_apparatus', 'id', 'ea');
	$obQuery->builder()
		->field('eea.id', 'apparatus_id')
		->field('ea.name', 'apparatus_name');
	$obQuery->builder()->filter()
		->_eq('eea.event_id', $ID);
	$arResult['event']['apparatus'] = $obQuery->select()->all();

	$obQuery->builder()
		->from('estelife_event_teachers')
		->filter()
		->_eq('event_id',$ID);
	$arPrepods=$obQuery->select()->all();

	if(!empty($arPrepods))
		$arResult['event']['prepods']=$arPrepods;
}

if($_SERVER['REQUEST_METHOD']=='POST'){
	$obPost=new VArray($_POST);
	$obError=new ex\VFormException();

	try{
		if($obPost->blank('company_id'))
			$obError->setFieldError('COMPANY_NOT_FILL','company_id');

		if($obPost->blank('full_name'))
			$obError->setFieldError('FULL_NAME_NOT_FILL','full_name');

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

		$nTime=time();
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
			value('address',strip_tags($obPost->one('address','')))->
			value('preview_text',htmlentities($obPost->one('preview_text',''),ENT_QUOTES,'utf-8'))->
			value('detail_text',htmlentities($obPost->one('detail_text',''),ENT_QUOTES,'utf-8'))->
			value('latitude',floatval($obPost->one('latitude')))->
			value('longitude',floatval($obPost->one('longitude')))->
			value('date_edit',$nTime)->
			value('logo_id',0);

		if(!empty($ID)){
			$obQuery->builder()->filter()
				->_eq('id',$ID);
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
			$obQuery->builder()
				->from('estelife_event_pills')
				->filter()
				->_eq('event_id',$ID);
			$obQuery->delete();
			$obQuery->builder()
				->from('estelife_event_apparatus')
				->filter()
				->_eq('event_id',$ID);
			$obQuery->delete();
			$obFilter=$obQuery->builder()
				->from('estelife_event_teachers')
				->filter()
				->_eq('event_id',$ID)
				->_ne('image',0);

			if(!$obPost->blank('prepod_not_deleted_id')){
				$obFilter->_notIn('id',$obPost->one('prepod_not_deleted_id'));
			}

			$arPrepods=$obQuery->select()->all();

			if(!empty($arPrepods)){
				foreach($arPrepods as $arPrepod){
					$arImage=array(
						'old_file'=>$arPrepod['image'],
						'module'=>'estelife',
						'del'=>$arPrepod['image']
					);
					$nImageId=CFile::SaveFile($arImage, "estelife");
				}
			}

			$obFilter=$obQuery->builder()
				->from('estelife_event_teachers')
				->filter()
				->_eq('event_id',$ID);

			if(!$obPost->blank('prepod_not_deleted_id')){
				$obFilter->_notIn('id',$obPost->one('prepod_not_deleted_id'));
			}

			$obQuery->delete();
		}else{
			$obQuery->builder()->value('date_create',$nTime);
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

		if(($sEmail=$obPost->one('email'))
			&& \core\types\VString::isEmail($sEmail)){
			$obQuery->builder()
				->from('estelife_event_contacts')
				->value('type','email')
				->value('value',$sEmail)
				->value('event_id',$ID);
			$obQuery->insert();
		}

		if($arPhones=$obPost->one('phones')){
			foreach($arPhones as $sPhone){
				if(!\core\types\VString::isPhone($sPhone))
					continue;

				$obQuery->builder()
					->from('estelife_event_contacts')
					->value('type','phone')
					->value('value',$sPhone)
					->value('event_id',$ID);
				$obQuery->insert();
			}
		}

		if($arFaxes=$obPost->one('faxes')){
			foreach($arFaxes as $sFax){
				if(!\core\types\VString::isPhone($sFax))
					continue;

				$obQuery->builder()
					->from('estelife_event_contacts')
					->value('type','fax')
					->value('value',$sFax)
					->value('event_id',$ID);
				$obQuery->insert();
			}
		}

		if($arDates=$obPost->one('date')){
			$arTimeFrom=$obPost->one('time_from',array());
			$arTimeTo=$obPost->one('time_to',array());

			foreach($arDates as $nKey=>$sDate){
				if(empty($sDate) || !preg_match('#^[0-9]{1,2}\s[a-zа-я]+\s[0-9]{4}$#ui',$sDate))
					continue;

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
		}

		if($arCompanies=$obPost->one('companies')){
			$arCompanies=$arCompanies['company_id'];

			foreach($arCompanies as $nCompany){
				$nCompany=intval($nCompany);

				if(empty($nCompany))
					continue;

				$obQuery->builder()
					->from('estelife_company_events')
					->value('company_id',$nCompany)
					->value('event_id',$ID)
					->value('is_owner',0);
				$obQuery->insert();
			}
		}

		if ($arPills = $obPost->one('pills')){
			foreach($arPills['pill_id'] as $nPill){
				$nPill = intval($nPill);

				if (empty($nPill))
					continue;

				$obQuery->builder()
					->from('estelife_event_pills')
					->value('pill_id',$nPill)
					->value('event_id',$ID);
				$obQuery->insert();
			}
		}

		if ($arApps = $obPost->one('apparatus')){
			foreach($arApps['apparatus_id'] as $nApp){
				$nApp = intval($nApp);

				if (empty($nApp))
					continue;

				$obQuery->builder()
					->from('estelife_event_apparatus')
					->value('apparatus_id',$nApp)
					->value('event_id',$ID);
				$obQuery->insert();
			}
		}

		if($arTypes=$obPost->one('types')){
			foreach($arTypes as $nType){
				$nType=intval($nType);

				if(empty($nType))
					continue;

				$obQuery->builder()
					->from('estelife_event_types')
					->value('type',$nType)
					->value('event_id',$ID);
				$obQuery->insert();
			}
		}

		if($arDirections=$obPost->one('directions')){
			foreach($arDirections as $nDirection){
				$nType=intval($nDirection);

				if(empty($nDirection))
					continue;

				$obQuery->builder()
					->from('estelife_event_directions')
					->value('type',$nDirection)
					->value('event_id',$ID);
				$obQuery->insert();
			}
		}

		if($arPrepodNames=$obPost->one('prepod_names')){
			$arPrepodSpec=$obPost->one('prepod_specializations');
			$arPrepodFiles=$_FILES['prepod_images'];
			$arTempFiles=array();

			foreach($arPrepodFiles['name'] as $nKey=>$sName){
				$arTempFiles[]=array(
					'name'=>$sName,
					'tmp_name'=>$arPrepodFiles['tmp_name'][$nKey],
					'size'=>$arPrepodFiles['size'][$nKey],
					'error'=>$arPrepodFiles['error'][$nKey],
					'type'=>$arPrepodFiles['type'][$nKey]
				);
			}

			$arPrepodFiles=$arTempFiles;
			unset($arTempFiles);

			foreach($arPrepodNames as $nKey=>$sName){
				if(empty($sName) || empty($arPrepodSpec[$nKey]))
					continue;

				$nImageId=0;

				if(!empty($arPrepodFiles[$nKey]['tmp_name']))
					$nImageId=CFile::SaveFile($arPrepodFiles[$nKey], "estelife");

				$obQuery->builder()
					->from('estelife_event_teachers')
					->value('event_id',$ID)
					->value('name',$sName)
					->value('specialization',$arPrepodSpec[$nKey])
					->value('image',$nImageId);
				$obQuery->insert();
			}
		}

		if(!$obPost->blank('save'))
			LocalRedirect('/bitrix/admin/estelife_training_list.php?lang='.LANGUAGE_ID);
		else
			LocalRedirect('/bitrix/admin/estelife_training_edit.php?lang='.LANGUAGE_ID.'&ID='.$ID);
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
	array("DIV" => "edit9", "TAB" => GetMessage("ESTELIFE_T_APPAR"), "ICON" => "estelife_r_base", "TITLE" => GetMessage("ESTELIFE_T_APPAR")),
	array("DIV" => "edit11", "TAB" => GetMessage("ESTELIFE_T_PILLS"), "ICON" => "estelife_r_base", "TITLE" => GetMessage("ESTELIFE_T_PILLS")),
	array("DIV" => "edit10", "TAB" => GetMessage("ESTELIFE_T_TEACHERS"), "ICON" => "estelife_r_base", "TITLE" => GetMessage("ESTELIFE_T_TEACHERS"))
);
$tabControl = new CAdminTabControl("estelife_training_".$ID, $aTabs, true, true);
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
			$arResult['event'][$sKey]=$sValue;
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

	<form name="estelife_spec" method="POST" action="<?echo $APPLICATION->GetCurPage()?>" enctype="multipart/form-data">
		<?=bitrix_sessid_post()?>
		<input type="hidden" name="ID" value=<?=$ID?> />
		<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>" />
		<input type="hidden" name="types[]" value="3" />
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
				<input type="hidden" name="company_type_id" value="4" />
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
			<td width="40%"><?=GetMessage("ESTELIFE_F_ADDRESS")?></td>
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
			<td width="40%"><?=GetMessage("ESTELIFE_F_DIRECTIONS")?></td>
			<td width="60%">
				<ul class="estelife-checklist">
					<li>
						<label for="direction_5"><input type="checkbox" name="directions[]" id="direction_5" value="5"<?=(in_array(5,$arResult['event']['directions']) ? ' checked="true"' : '')?> />Ботулинотерапия</label>
					</li>
					<li>
						<label for="direction_6"><input type="checkbox" name="directions[]" id="direction_6" value="6"<?=(in_array(6,$arResult['event']['directions']) ? ' checked="true"' : '')?> />Контурная пластика</label>
					</li>
					<li>
						<label for="direction_7"><input type="checkbox" name="directions[]" id="direction_7" value="7"<?=(in_array(7,$arResult['event']['directions']) ? ' checked="true"' : '')?> />Мезотерапия</label>
					</li>
					<li>
						<label for="direction_8"><input type="checkbox" name="directions[]" id="direction_8" value="8"<?=(in_array(8,$arResult['event']['directions']) ? ' checked="true"' : '')?> />Биоревитализация</label>
					</li>
					<li>
						<label for="direction_9"><input type="checkbox" name="directions[]" id="direction_9" value="9"<?=(in_array(9,$arResult['event']['directions']) ? ' checked="true"' : '')?> />Объемное моделирование</label>
					</li>
					<li>
						<label for="direction_10"><input type="checkbox" name="directions[]" id="direction_10" value="10"<?=(in_array(10,$arResult['event']['directions']) ? ' checked="true"' : '')?> />Безоперационный лифтинг</label>
					</li>
					<li>
						<label for="direction_12"><input type="checkbox" name="directions[]" id="direction_12" value="12"<?=(in_array(12,$arResult['event']['directions']) ? ' checked="true"' : '')?> />Пилинги</label>
					</li>
					<li>
						<label for="direction_13"><input type="checkbox" name="directions[]" id="direction_13" value="13"<?=(in_array(13,$arResult['event']['directions']) ? ' checked="true"' : '')?> />Космецевтика</label>
					</li>
					<li>
						<label for="direction_14"><input type="checkbox" name="directions[]" id="direction_14" value="14"<?=(in_array(14,$arResult['event']['directions']) ? ' checked="true"' : '')?> />Аппаратная косметология</label>
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
				<input type="hidden" name="company_type_id" class="ignore_cleared" value="4" />
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

		<tr>
			<td width="30%" align="right"><?=GetMessage("ESTELIFE_F_EMAIL")?></td>
			<td width="70%"><input type="text" name="email" size="60" maxlength="255" value="<?=$arResult['event']['email']?>"></td>
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
			<td width="70%" class="estelife-cell"><input type="text" name="phones[]" size="60" maxlength="255" value=""><a href="#" class="estelife-more estelife-btn adm-btn adm-btn-save">+</a></td>
		</tr>

		<?
		$tabControl->BeginNextTab()
		?>
		<?php if(!empty($arResult['event']['apparatus'])): ?>
			<?php foreach($arResult['event']['apparatus'] as $arApp): ?>
				<tr>
					<td width="30%"><?=GetMessage("ESTELIFE_F_APPARATUS")?></td>
					<td width="70%">
						<input type="hidden" name="apparatus[apparatus_id][]" value="<?=$arApp['apparatus_id']?>" />
						<input type="text" disabled="disabled" name="apparatus[apparatus_name][]" data-input="apparatus_id" class="estelife-need-clone" value="<?=$arApp['apparatus_name']?>" />
						<a href="#" class="estelife-more estelife-btn adm-btn adm-btn-delete estelife-delete"></a>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<tr>
			<td width="30%"><?=GetMessage("ESTELIFE_F_APPARATUS")?></td>
			<td width="70%">
				<input type="hidden" name="apparatus[apparatus_id][]" value="" />
				<input type="text" name="apparatus[apparatus_name][]" data-input="apparatus_id" class="estelife-need-clone" value="" />
				<a href="#" class="estelife-more estelife-btn adm-btn adm-btn-save">&crarr;</a>
			</td>
		</tr>
		<?
		$tabControl->BeginNextTab()
		?>

		<?php if(!empty($arResult['event']['pills'])): ?>
			<?php foreach($arResult['event']['pills'] as $arPill): ?>
				<tr>
					<td width="30%"><?=GetMessage("ESTELIFE_F_PILL")?></td>
					<td width="70%">
						<input type="hidden" name="pills[pill_id][]" value="<?=$arPill['pill_id']?>" />
						<input type="text" disabled="disabled" name="pills[pill_name][]" data-input="pill_id" class="estelife-need-clone" value="<?=$arPill['pill_name']?>" />
						<a href="#" class="estelife-more estelife-btn adm-btn adm-btn-delete estelife-delete"></a>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<tr>
			<td width="30%"><?=GetMessage("ESTELIFE_F_PILL")?></td>
			<td width="70%">
				<input type="hidden" name="pills[pill_id][]" value="" />
				<input type="text" name="pills[pill_name][]" data-input="pill_id" class="estelife-need-clone" value="" />
				<a href="#" class="estelife-more estelife-btn adm-btn adm-btn-save">&crarr;</a>
			</td>
		</tr>

		<?
		$tabControl->BeginNextTab()
		?>
			<?php if(!empty($arResult['event']['prepods'])): ?>
				<tr>
				<td colspan="2">
				<?php foreach($arResult['event']['prepods'] as $arPrepod): ?>
					<div class="estelife-prepod">
						<input type="hidden" name="prepod_not_deleted_id[]" value="<?=$arPrepod['id']?>" />
						<div class="image">
							<?php if(!empty($arPrepod['image'])): ?>
								<?=CFile::ShowImage($arPrepod['image'],200,200)?>
							<?php endif; ?>
						</div>
						<div class="info">
							<?=$arPrepod['name']?><br />
							<div class="spec"><?=$arPrepod['specialization']?></div>
							<a href="#" class="estelife-btn adm-btn adm-btn-delete estelife-delete"></a>
						</div
							>
					</div>
				<?php endforeach; ?>
				</td>
				</tr>
			<?php endif;?>
			<tr><td colspan="2" class="estelife-sep"><span>Добавление преподов</span></td></tr>
			<tr>
				<td colspan="2">
					<table class="adm-detail-content-table edit-table estelife-prepod-table estelife-prepod-table-first">
						<tr>
							<td width="30%" class="adm-detail-content-cell-l"><?=GetMessage('ESTELIFE_F_PREPOD_NAME')?></td>
							<td width="70%" class="adm-detail-content-cell-r"><input type="text" name="prepod_names[]" /></td>
						</tr>
						<tr>
							<td width="30%" class="adm-detail-content-cell-l"><?=GetMessage('ESTELIFE_F_PREPOD_IMAGE')?></td>
							<td width="70%" class="adm-detail-content-cell-r"><input type="file" name="prepod_images[]" /></td>
						</tr>
						<tr>
							<td width="30%" class="adm-detail-content-cell-l"><?=GetMessage('ESTELIFE_F_PREPOD_SPECIALIZATION')?></td>
							<td width="70%" class="adm-detail-content-cell-r"><input type="text" name="prepod_specializations[]" /></td>
						</tr>
						<tr>
							<td width="30%" class="adm-detail-content-cell-l"></td>
							<td width="70%" class="adm-detail-content-cell-r"><a href="#" class="estelife-btn adm-btn adm-btn-save adm-btn-add estelife-prepod-more">Добавить</a></td>
						</tr>
					</table>
				</td>
			</tr>

		<?php
		$tabControl->EndTab();
		$tabControl->Buttons(array("disabled"=>false, "back_url"=>(strlen($back_url) > 0 ? $back_url : "estelife_training_list.php?lang=".LANGUAGE_ID)));
		$tabControl->End();
		?>
	</form>
<?php
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");