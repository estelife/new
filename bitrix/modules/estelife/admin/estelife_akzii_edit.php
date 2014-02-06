<?php
use core\database\exceptions\VCollectionException;
use core\database\VDatabase;
use core\types\VArray;
use core\exceptions as ex;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/estelife/prolog.php");

ClearVars();
$FORM_RIGHT = $APPLICATION->GetGroupRight("estelife");

if($FORM_RIGHT<="D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

CModule::IncludeModule("estelife");
IncludeModuleLangFile(__FILE__);

define("HELP_FILE","estelife_list.php");
$ID = isset($_REQUEST['ID']) ?
	intval($_REQUEST['ID']) : 0;

$obPrices=VDatabase::driver();
$obAkzii=VDatabase::driver();

//получение списка типов услуг
$obQuery = $obPrices->createQuery();
$obQuery->builder()->from('estelife_service_concreate', 'esc');
$obJoin = $obQuery->builder()->join();
$obJoin->_left()
	->_from('esc', 'service_id')
	->_to('estelife_services', 'id', 'es');
$obQuery->builder()
	->field('esc.name', 'name')
	->field('esc.id', 'id')
	->field('es.name', 'service_name');
foreach ($obQuery->select()->all() as $val){
	$val['name'] = $val['name'].' ('.$val['service_name'].')';
	$arResult['service_concreate'][] = $val;
}


if(!empty($ID)){
	$obQuery=$obPrices->createQuery();
	$obQuery->builder()->from('estelife_akzii','ea');
	$obQuery->builder()
		->field('ea.*');
	$obQuery->builder()->filter()->_eq('ea.id', $ID);
	$obResult=$obQuery->select();
	$arResult['ak']=$obResult->assoc();


	//Получение типов акций
	$obQuery=$obPrices->createQuery();
	$obQuery->builder()
		->from('estelife_akzii_types')
		->filter()
		->_eq('akzii_id', $ID);
	$arResult['ak']['types'] = $obQuery->select()->all();


	//Получение клиник
	$obQuery=$obPrices->createQuery();
	$obQuery->builder()->from('estelife_clinic_akzii','eca');
	$obJoin=$obQuery->builder()->join();
	$obJoin->_left()
		->_from('eca','clinic_id')
		->_to('estelife_clinics','id','ec');
	$obQuery->builder()
		->field('ec.name','clinic_name')
		->field('eca.akzii_id','akzii_id')
		->field('ec.id','clinic_id');
	$obQuery->builder()->filter()->_eq('eca.akzii_id', $ID);
	$arResult['ak']['clinic']=$obQuery->select()->all();


	if(!empty($arResult['ak']['start_date']))
		$arResult['ak']['start_date']=date('d.m.Y H:i:s',$arResult['ak']['start_date']);

	if(!empty($arResult['ak']['end_date']))
		$arResult['ak']['end_date']=date('d.m.Y H:i:s',$arResult['ak']['end_date']);

	$obQuery=$obPrices->createQuery();
	$obQuery->builder()->from('estelife_akzii_photos');
	$obQuery->builder()->filter()->_eq('akzii_id',$arResult['ak']['id']);
	$obResult=$obQuery->select();
	$arResult['ak']['photos']=$obResult->all();

	//Получение цен на процедуры
	$obQuery = $obPrices->createQuery();
	$obQuery->builder()->from('estelife_akzii_prices');
	$obQuery->builder()->filter()->_eq('akzii_id', $ID);
	$arResult['ak']['prices'] = $obQuery->select()->all();



}
//}else if(!empty($CLINIC_ID)){
//	$obQuery=$obClinics->createQuery();
//	$obQuery->builder()
//		->from('estelife_clinics')
//		->field('id')
//		->field('name')
//		->filter()
//		->_eq('id',$CLINIC_ID);
//	$obResult=$obQuery->select();
//	$arResult['ak']['clinic']=$obResult->all();
//}

if($_SERVER['REQUEST_METHOD']=='POST'){
	$obPost=new VArray($_POST);
	$obError=new ex\VFormException();

	try{
		if($obPost->blank('name'))
			$obError->setFieldError('NAME_NOT_FILL','name');

		if($obPost->blank('detail_text'))
			$obError->setFieldError('DETAIL_TEXT_NOT_FILL', 'detail_text');

		if($obPost->blank('clinic_id'))
			$obError->setFieldError('CLINIC_ID_NOT_FILL', 'clinic_id');


		$obError->raise();

		$nTime=time();
		$obQueryAkzii = $obAkzii->createQuery();
		$obQueryAkzii->builder()->from('estelife_akzii')
			->value('name', trim(strip_tags($obPost->one('name'))))
			->value('active', $obPost->one('active'))
			->value('preview_text', htmlentities($obPost->one('preview_text'),ENT_QUOTES,'utf-8'))
			->value('detail_text', htmlentities($obPost->one('detail_text'),ENT_QUOTES,'utf-8'))
			->value('base_old_price', $obPost->one('base_old_price'))
			->value('base_new_price', $obPost->one('base_new_price'))
			->value('base_sale', $obPost->one('base_sale'))
			->value('start_date', strtotime($obPost->one('start_date')))
			->value('end_date', strtotime($obPost->one('end_date')))
			->value('view_type', intval($obPost->one('view_type')))
			->value('more_information', strip_tags($obPost->one('more_information')))
			->value('date_edit',$nTime);

		if(!empty($_FILES['small_photo'])){
			$arImage=$_FILES['small_photo'];
			$arImage['old_file']=$obRecord['small_photo'];
			$arImage['module']='estelife';
			$arImage['del']=$small_photo_del;

			if(strlen($arImage["name"])>0 || strlen($arImage["del"])>0){
				$nImageId=CFile::SaveFile($arImage,"estelife");
				$obQueryAkzii->builder()
					->value('small_photo', intval($nImageId));
			}
		}

		if(!empty($_FILES['big_photo'])){
			$arImage=$_FILES['big_photo'];
			$arImage['old_file']=$obRecord['big_photo'];
			$arImage['module']='estelife';
			$arImage['del']=$big_photo_del;

			if(strlen($arImage["name"])>0 || strlen($arImage["del"])>0){
				$nImageId=CFile::SaveFile($arImage, "estelife");
				$obQueryAkzii->builder()
					->value('big_photo', intval($nImageId));
			}
		}


		if (!empty($ID)){
			$obQueryAkzii->builder()->filter()
				->_eq('id',$ID);
			$obQueryAkzii->update();
			$idAkzii = $ID;
		}else{
			$obQueryAkzii->builder()
				->value('date_create',$nTime);
			$idAkzii = $obQueryAkzii->insert()->insertId();
		}

		if(!empty($idAkzii)){
			$obQuery=$obPrices->createQuery();
			$obQuery->builder()->from('estelife_clinic_akzii');
			$obQuery->builder()->filter()
				->_eq('akzii_id',$idAkzii);
			$obQuery->delete();

			$obQuery=$obPrices->createQuery();
			$obQuery->builder()->from('estelife_akzii_types');
			$obQuery->builder()->filter()
				->_eq('akzii_id',$idAkzii);
			$obQuery->delete();
		}


		//добавление типов акций
		foreach ($obPost->one('service_concreate_id') as $val){
			if (empty($val) || intval($val)==0)
				continue;

			//получение вида услуг и специализации
			$obQuery = $obPrices->createQuery();
			$obQuery->builder()->from('estelife_service_concreate');
			$obQuery->builder()->filter()
				->_eq('id', intval($val));
			$arSpecialization = $obQuery->select()->assoc();

			$obQuery=$obPrices->createQuery();
			$obQuery->builder()->from('estelife_akzii_types')
				->value('service_concreate_id', $val)
				->value('service_id', $arSpecialization['service_id'])
				->value('specialization_id',$arSpecialization['specialization_id'])
				->value('method_id',$arSpecialization['method_id'])
				->value('akzii_id', $idAkzii);
			$idAkziiType = $obQuery->insert()->insertId();
		}


		foreach ($obPost->one('clinic_id') as $val){

			if (empty($val))
				continue;

			$obQuery=$obPrices->createQuery();
			$obQuery->builder()->from('estelife_clinic_akzii')
				->value('clinic_id', $val)
				->value('akzii_id', $idAkzii);
			$idAkziiClinic = $obQuery->insert()->insertId();
		}


		$arPost=$obPost->all();
		foreach($arPost as $sKey=>$mValue){
			if(preg_match('#^photo_descriptions_([0-9]+)$#i',$sKey,$arMatches)){
				try{
					$obQuery=$obPrices->createQuery();
					$obQuery->builder()->from('estelife_akzii_photos')
						->filter()
						->_eq('id', $arMatches[1]);
					$arPhoto = $obQuery->select()->assoc();

					$obQuery=$obPrices->createQuery();
					$obQuery->builder()->from('estelife_akzii_photos')
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
					$obQuery=$obPrices->createQuery();
					$obQuery->builder()->from('estelife_akzii_photos')
						->filter()
						->_eq('id',$nDelete);
					$arPhoto = $obQuery->select()->assoc();
					CFile::Delete($obPhoto['original']);

					$obQuery=$obPrices->createQuery();
					$obQuery->builder()->from('estelife_akzii_photos');
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

				$obQuery=$obPrices->createQuery();
				$obQuery->builder()->from('estelife_akzii_photos')
					->value('original', $nImageId)
					->value('akzii_id', $idAkzii);
				$idAkziiClinic = $obQuery->insert();
			}
		}

		//Запись цен
		$obQuery=$obPrices->createQuery();
		$obQuery->builder()->from('estelife_akzii_prices');
		$obQuery->builder()->filter()
			->_eq('akzii_id',$idAkzii);
		$obQuery->delete();


		if (!$obPost->blank('old_price')){
			$arOldPrice = $obPost->one('old_price');
			$arNewPrice = $obPost->one('new_price');
			$arSale= $obPost->one('procedure');

			foreach ($arOldPrice as $key=>$val){
				if (!empty($val) || !empty($arNewPrice[$key]) || !empty($arSale[$key])){
					$obQuery = $obPrices->createQuery();
					$obQuery->builder()->from('estelife_akzii_prices')
						->value('old_price', $val)
						->value('new_price', $arNewPrice[$key])
						->value('procedure', $arSale[$key])
						->value('akzii_id', $idAkzii);
					$obQuery->insert();
				}
			}
		}



		if(!empty($idAkzii)){
			if(!$obPost->blank('save'))
				LocalRedirect('/bitrix/admin/estelife_akzii_list.php?lang='.LANGUAGE_ID);
			else
				LocalRedirect('/bitrix/admin/estelife_akzii_edit.php?lang='.LANGUAGE_ID.'&ID='.$idAkzii);
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

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("ESTELIFE_T_BASE"), "ICON" => "estelife_r_base", "TITLE" => GetMessage("ESTELIFE_T_BASE")),
	array("DIV" => "edit2", "TAB" => GetMessage("ESTELIFE_T_PREVIEW_TEXT"), "ICON" => "estelife_r_base", "TITLE" => GetMessage("ESTELIFE_T_PREVIEW_TEXT")),
	array("DIV" => "edit3", "TAB" => GetMessage("ESTELIFE_T_DETAIL_TEXT"), "ICON" => "estelife_r_base", "TITLE" => GetMessage("ESTELIFE_T_DETAIL_TEXT")),
	array("DIV" => "edit5", "TAB" => GetMessage("ESTELIFE_T_PHOTO"), "ICON" => "estelife_r_base", "TITLE" => GetMessage("ESTELIFE_T_PHOTO")),
	array("DIV" => "edit4", "TAB" => GetMessage("ESTELIFE_T_NOTE"), "ICON" => "estelife_r_base", "TITLE" => GetMessage("ESTELIFE_T_NOTE")),
	array("DIV" => "edit8", "TAB" => GetMessage("ESTELIFE_T_GALLERIES"), "ICON" => "estelife_r_base", "TITLE" => GetMessage("ESTELIFE_T_GALLERIES"))
);
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
			$arResult['ak'][$sKey]=$sValue;
	}

}
?>
	<script type="text/javascript" src="/bitrix/js/estelife/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/jquery-ui-1.10.3.custom.min.js"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/jquery.damnUploader.js"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/estelife.js"></script>
	<form name="estelife_spec" method="POST" action="<?echo $APPLICATION->GetCurPage()?>" enctype="multipart/form-data">
		<?=bitrix_sessid_post()?>
		<input type="hidden" name="ID" value=<?=$ID?> />
		<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>" />
		<?php
		$tabControl->Begin();
		$tabControl->BeginNextTab()
		?>

		<tr class="adm-detail-required-field">
			<td width="40%"><?=GetMessage("ESTELIFE_F_TITLE")?></td>
			<td width="60%"><input type="text" name="name" size="60" maxlength="255" value="<?=$arResult['ak']['name']?>"></td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_VIEW_TYPE")?></td>
			<td width="60%">
				<select name="view_type">
					<option value="1"<?php if (empty($arResult['ak']['view_type']) || $arResult['ak']['view_type'] == 1):?> selected="true"<?php endif?>>Указывается старая цена, новая цена, размер скидки</option>
					<option value="2"<?php if ($arResult['ak']['view_type'] == 2):?> selected="true"<?php endif?>>Указывается только одна цена</option>
					<option value="3"<?php if ($arResult['ak']['view_type'] == 3):?> selected="true"<?php endif?>>Указывается только размер скидки</option>
				</select>
			</td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_ACTIVE")?></td>
			<td width="60%">
				<ul class="estelife-checklist">
					<li>
						<label for="type_1">
							<input type="checkbox" name="active" id="type_1" value="1"<?=(($arResult['ak']['active'] == 1) ? ' checked="true"' : '')?> />
						</label>
					</li>
				</ul>
			</td>
		</tr>
		<?php if(!empty($arResult['ak']['clinic'])): ?>
			<?php foreach($arResult['ak']['clinic'] as $arClinic): ?>
				<tr class="adm-detail-required-field">
					<td width="30%"><?=GetMessage("ESTELIFE_F_CLINIC")?></td>
					<td width="70%">
						<input type="hidden" name="clinic_id[]" value="<?=$arClinic['clinic_id']?>" />
						<input type="text" disabled="disabled" name="clinic_name[]" data-input="clinic_id" class="estelife-need-clone" value="<?=$arClinic['clinic_name']?>" />
						<a href="#" class="estelife-more estelife-btn adm-btn adm-btn-delete estelife-delete"></a>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<tr class="adm-detail-required-field">
			<td width="30%"><?=GetMessage("ESTELIFE_F_CLINIC")?></td>
			<td width="70%">
				<input type="hidden" name="clinic_id[]" value="" />
				<input type="text" name="clinic_name[]" data-input="clinic_id" class="estelife-need-clone" value="" />
				<a href="#" class="estelife-more estelife-btn adm-btn adm-btn-save">&crarr;</a>
			</td>
		</tr>
		<tr class="adm-detail-required-field">
			<td width="40%"><?=GetMessage("ESTELIFE_F_START_DATE")?></td>
			<td width="60%"><?echo CAdminCalendar::CalendarDate("start_date", $arResult['ak']['start_date'], 19, true)?></td>
		</tr>
		<tr class="adm-detail-required-field">
			<td width="40%"><?=GetMessage("ESTELIFE_F_END_DATE")?></td>
			<td width="60%"><?echo CAdminCalendar::CalendarDate("end_date", $arResult['ak']['end_date'], 19, true)?></td>
		</tr>
		<?php if(!empty($arResult['ak']['types'])): ?>
			<?php foreach($arResult['ak']['types'] as $arValue): ?>
				<tr class="adm-detail-required-field">
					<td width="40%"><?=GetMessage("ESTELIFE_F_TYPE")?></td>
					<td width="60%">
						<select name="service_concreate_id[]" class="estelife-need-clone">
							<option><?=GetMessage("ESTELIFE_F_SELECT_TYPE")?></option>
							<?php if (!empty($arResult['service_concreate'])):?>
								<?php foreach ($arResult['service_concreate'] as $val):?>
									<option value="<?=$val['id']?>" <?php if ($arValue['service_concreate_id'] == $val['id']):?> selected <?php endif?>><?=$val['name']?></option>
								<?php endforeach?>
							<?php endif?>
						</select>
						<a href="#" class="estelife-more estelife-btn adm-btn adm-btn-delete estelife-delete"></a>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<?php //if ($arResult['ak']['service_concreate_id'] == $val['id']):?><!-- selected --><?php //endif?>
		<tr class="adm-detail-required-field">
			<td width="40%"><?=GetMessage("ESTELIFE_F_TYPE")?></td>
			<td width="60%">
				<select name="service_concreate_id[]" class="estelife-need-clone">
					<option><?=GetMessage("ESTELIFE_F_SELECT_TYPE")?></option>
					<?php if (!empty($arResult['service_concreate'])):?>
						<?php foreach ($arResult['service_concreate'] as $val):?>
							<option value="<?=$val['id']?>" ><?=$val['name']?></option>
						<?php endforeach?>
					<?php endif?>
				</select>
				<a href="#" class="estelife-more estelife-btn adm-btn adm-btn-save">&crarr;</a>
			</td>
		</tr>
		<tr>
			<td width="30%"><?=GetMessage("ESTELIFE_F_MORE_INFORMATION")?></td>
			<td width="70%">
				<input type="text" size="60" maxlength="255" name="more_information" value="<?=$arResult['ak']['more_information']?>" />
			</td>
		</tr>
		<?
		$tabControl->BeginNextTab()
		?>

		<tr id="tr_preview_text_editor">
			<td colspan="2" align="center">
				<?CFileMan::AddHTMLEditorFrame(
					"preview_text",
					$arResult['ak']['preview_text'],
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
					$arResult['ak']['detail_text'],
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

		<tr>
			<td width="30%"><?=GetMessage('ESTELIFE_F_SMALL_PHOTO')?></td>
			<td width="70%">
				<?echo CFileInput::Show("small_photo", $arResult['ak']['small_photo'],
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
			<td width="30%"><?=GetMessage('ESTELIFE_F_BIG_PHOTO')?></td>
			<td width="70%">
				<?echo CFileInput::Show("big_photo", $arResult['ak']['big_photo'],
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

		<?
		$tabControl->BeginNextTab()
		?>
		<tr>
			<td colspan="2"><b><?=GetMessage("ESTELIFE_T_BASE_PRICE")?></b></td>
		</tr>
		<tr>
			<td colspan="2">
				<table class="price">
					<tr>
						<td><?=GetMessage("ESTELIFE_F_OLD_PRICE")?></td>
						<td><?=GetMessage("ESTELIFE_F_NEW_PRICE")?></td>
						<td><?=GetMessage("ESTELIFE_F_SALE")?></td>
					</tr>
					<tr>
						<td><input type="text" name="base_old_price" size="60" maxlength="255" value="<?=$arResult['ak']['base_old_price']?>"></td>
						<td><input type="text" name="base_new_price" size="60" maxlength="255" value="<?=$arResult['ak']['base_new_price']?>"></td>
						<td><input type="text" name="base_sale" size="60" maxlength="255" value="<?=$arResult['ak']['base_sale']?>"></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2"><br /><br /><b><?=GetMessage("ESTELIFE_T_PROCEDURE_PRICE")?></b></td>
		</tr>
		<tr>
			<td colspan="2">
				<table class="price">
					<tr>
						<td><?=GetMessage("ESTELIFE_F_PROCEDURE")?></td>
						<td><?=GetMessage("ESTELIFE_F_OLD_PRICE")?></td>
						<td><?=GetMessage("ESTELIFE_F_NEW_PRICE")?></td>

					</tr>
					<?php if(!empty($arResult['ak']['prices'])): ?>
						<?php foreach($arResult['ak']['prices'] as $val): ?>
							<tr>
								<td><input type="text" name="procedure[]" size="60" maxlength="255" value="<?=$val['procedure']?>"></td>
								<td><input type="text" name="old_price[]" size="60" maxlength="255" value="<?=$val['old_price']?>"></td>
								<td>
									<input type="text" name="new_price[]" size="60" maxlength="255" value="<?=$val['new_price']?>">
									<a href="#" class="estelife-more estelife-btn adm-btn adm-btn-delete estelife-delete"></a>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
					<tr>
						<td><input type="text" name="procedure[]" size="60" maxlength="255" value=""></td>
						<td><input type="text" name="old_price[]" size="60" maxlength="255" value=""></td>
						<td>
							<input type="text" name="new_price[]" size="60" maxlength="255" value="">
							<a href="#" class="estelife-more estelife-btn adm-btn adm-btn-save">+</a>
						</td>
					</tr>
				</table>
			</td>
		</tr>

		<?
		$tabControl->BeginNextTab()
		?>

		<tr>
			<td colspan="2">
				<input type="file" name="gallery[]" id="gallery" />
				<?php if(!empty($arResult['ak']['photos'])): ?>
					<div class="estelife-photos">
						<?php foreach($arResult['ak']['photos'] as $arPhoto): ?>
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

		<?php
		$tabControl->EndTab();
		$tabControl->Buttons(array("disabled"=>false, "back_url"=>(strlen($back_url) > 0 ? $back_url : "estelife_service_concreate_list.php?lang=".LANGUAGE_ID)));
		$tabControl->End();
		?>
	</form>
<?php
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");