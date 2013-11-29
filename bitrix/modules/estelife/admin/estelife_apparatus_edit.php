<?php
use companies\VCompanies;
use core\database\VDatabase;
use core\exceptions as ex;
use core\types\VArray;
use core\types\VString;

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


$obApp= VDatabase::driver();


if(!empty($ID)){
	$obQuery = $obApp->createQuery();
	$obQuery->builder()->from('estelife_apparatus', 'ea');
	$obJoin = $obQuery->builder()->join();
	$obJoin->_left()
		->_from('ea', 'type_id')
		->_to('iblock_element','ID','mt')
		->_cond()->_eq('mt.IBLOCK_ID',31);
	$obJoin->_left()
		->_from('ea', 'company_id')
		->_to('estelife_companies', 'id', 'ec');
	$obJoin->_left()
		->_from('ec', 'id')
		->_to('estelife_company_types', 'company_id', 'ect')
		->_cond()->_eq('ect.type', 3);
	$obQuery->builder()
		->field('ea.*')
		->field('ec.name', 'company_name')
		->field('ect.name', 'company_type_name')
		->field('mt.NAME', 'type_name')
		->field('mt.ID', 'type_id');
	$obQuery->builder()->filter()
		->_eq('ea.id', $ID);
	$obResult = $obQuery->select();
	$arResult['apps']=$obResult->assoc();

	//Получение галереи
	$obQuery = $obApp->createQuery();
	$obQuery->builder()->from('estelife_apparatus_photos');
	$obQuery->builder()->filter()
		->_eq('apparatus_id', $ID);
	$obResult = $obQuery->select();
	foreach ($obResult->all() as $val){
		if ($val['type'] == 1){
			$arResult['apps']['gallery'][] = $val;
		}elseif ($val['type'] == 2){
			$arResult['apps']['certification'][] = $val;
		}elseif($val['type'] == 3){
			$arResult['apps']['fitting'][] = $val;
		}

	}

	//Получение типов аппаратов
	$obQuery = $obApp->createQuery();
	$obQuery->builder()->from('estelife_apparatus_type');
	$obQuery->builder()->filter()
		->_eq('apparatus_id', $ID);
	$obResult = $obQuery->select();
	$arFormats=$obResult->all();
	foreach ($arFormats as $val){
		$arResult['apps']['format'][]=$val['type_id'];
	}



}else{

}

if($_SERVER['REQUEST_METHOD']=='POST'){
	$obPost=new VArray($_POST);
	$obError=new ex\VFormException();

	try{

		if($obPost->blank('name'))
			$obError->setFieldError('NAME_NOT_FILL','name');

		if($obPost->blank('company_id'))
			$obError->setFieldError('COMPANY_NOT_FILL','company_id');

		if($obPost->blank('company_name'))
			$obError->setFieldError('COMPANY_NOT_FILL','company_id');

		if($obPost->blank('format'))
			$obError->setFieldError('FORMAT_NOT_FILL','format');

//		if($obPost->blank('type_id'))
//			$obError->setFieldError('COMPANY_NOT_FILL','type_id');

		$obError->raise();

		if($obPost->blank('translit')){
			$arTranslit = VString::translit($obPost->one('name'));
		}else{
			$arTranslit = $obPost->one('translit');
		}

		//Добавление компании
		$obQuery = $obApp->createQuery();
		$obQuery->builder()->from('estelife_apparatus')
			->value('name', trim(htmlentities($obPost->one('name'),ENT_QUOTES,'utf-8')))
			->value('translit', trim($arTranslit))
			->value('company_id', intval($obPost->one('company_id')))
			->value('type_id', intval($obPost->one('type_id')))
			->value('preview_text', htmlentities($obPost->one('preview_text'),ENT_QUOTES,'utf-8'))
			->value('detail_text', htmlentities($obPost->one('detail_text'),ENT_QUOTES,'utf-8'))
			->value('action', htmlentities($obPost->one('action'),ENT_QUOTES,'utf-8'))
			->value('evidence', htmlentities($obPost->one('evidence'),ENT_QUOTES,'utf-8'))
			->value('contra', htmlentities($obPost->one('contra'),ENT_QUOTES,'utf-8'))
			->value('specs', htmlentities($obPost->one('specs'),ENT_QUOTES,'utf-8'))
			->value('func', htmlentities($obPost->one('func'),ENT_QUOTES,'utf-8'))
			->value('registration', htmlentities($obPost->one('registration'),ENT_QUOTES,'utf-8'))
			->value('advantages', htmlentities($obPost->one('advantages'),ENT_QUOTES,'utf-8'))
			->value('procedure', htmlentities($obPost->one('procedure'),ENT_QUOTES,'utf-8'))
			->value('security', htmlentities($obPost->one('security'),ENT_QUOTES,'utf-8'))
			->value('protocol', htmlentities($obPost->one('protocol'),ENT_QUOTES,'utf-8'))
			->value('undesired', htmlentities($obPost->one('undesired'),ENT_QUOTES,'utf-8'))
			->value('equipment', htmlentities($obPost->one('equipment'),ENT_QUOTES,'utf-8'));

		if(!empty($_FILES['logo_id'])){
			$arImage=$_FILES['logo_id'];
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
			$idApp = $ID;

			//Удаляем привязку к типам
			$obQuery =  $obApp->createQuery();
			$obQuery->builder()->from('estelife_apparatus_type')
				->filter()
				->_eq('apparatus_id', $idApp);
			$obQuery->delete();
		}else{
			$idApp = $obQuery->insert()->insertId();
		}

		//Пишем описание к аксессуарам
		$arPost=$obPost->all();
		foreach($arPost as $sKey=>$mValue){
			if(preg_match('#^photo_descriptions_([0-9]+)$#i',$sKey,$arMatches)){
				try{
					$obQuery = $obApp->createQuery();
					$obQuery->builder()->from('estelife_apparatus_photos');
					$obQuery->builder()->filter()->_eq('id', $arMatches[1]);
					$arPhoto = $obQuery->select()->assoc();
					if (!empty($arPhoto)){
						$obQuery = $obApp->createQuery();
						$obQuery->builder()->from('estelife_apparatus_photos')
							->value('description', htmlentities($mValue,ENT_QUOTES,'utf-8'));
						$obQuery->builder()->filter()->_eq('id', $arMatches[1]);
						$obQuery->update();
					}
				}catch(\core\database\exceptions\VCollectionException $e){}
			}
		}

		//Пишем тип аппарата
		if (!$obPost->blank('format')){
			$arAppsType = $obPost->one('format');
			foreach ($arAppsType as $val){
				$obQuery = $obApp->createQuery();
				$obQuery->builder()->from('estelife_apparatus_type')
					->value('type_id', intval($val))
					->value('apparatus_id', $idApp);
				$idAppType = $obQuery->insert()->insertId();
			}
		}


		if(!$obPost->blank('photo_deleted')){
			$arDeleted=$obPost->one('photo_deleted');
			foreach($arDeleted as $nDelete){
				try{
					$obQuery = $obApp->createQuery();
					$obQuery->builder()->from('estelife_apparatus_photos');
					$obQuery->builder()->filter()->_eq('id', $nDelete);
					$arPhoto = $obQuery->select()->assoc();
					if (!empty($arPhoto)){
						CFile::Delete($arPhoto['original']);
						$obQuery = $obApp->createQuery();
						$obQuery->builder()->from('estelife_apparatus_photos');
						$obQuery->builder()->filter()->_eq('id', $nDelete);
						$obQuery->delete();
					}
				}catch(\core\database\exceptions\VCollectionException $e){}
			}
		}

		//Пишем ссылки на фото до/после
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

				$obQuery = $obApp->createQuery();
				$obQuery->builder()->from('estelife_apparatus_photos')
					->value('original', $nImageId)
					->value('apparatus_id', $idApp)
					->value('type', 1);
				$idPillPhoto = $obQuery->insert()->insertId();
			}
		}

		//Пишем ссылки на сертификаты
		if(!empty($_FILES['certification'])){
			$arFiles=$_FILES['certification'];
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

				$obQuery = $obApp->createQuery();
				$obQuery->builder()->from('estelife_apparatus_photos')
					->value('original', $nImageId)
					->value('apparatus_id', $idApp)
					->value('type', 2);
				$idPillPhoto = $obQuery->insert()->insertId();
			}
		}

		//Пишем ссылки на аксессуары
		if(!empty($_FILES['fitting'])){
			$arFiles=$_FILES['fitting'];
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

				$obQuery = $obApp->createQuery();
				$obQuery->builder()->from('estelife_apparatus_photos')
					->value('original', $nImageId)
					->value('apparatus_id', $idApp)
					->value('type', 3);
				$idPillPhoto = $obQuery->insert()->insertId();
			}
		}

		if(!$obPost->blank('save'))
			LocalRedirect('/bitrix/admin/estelife_apparatus_list.php?lang='.LANGUAGE_ID);
		else
			LocalRedirect('/bitrix/admin/estelife_apparatus_edit.php?lang='.LANGUAGE_ID.'&ID='.$idApp);
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
	array("DIV" => "edit2", "TAB" => GetMessage("ESTELIFE_T_CERTIFICATION"), "ICON" => "estelife_r_certification", "TITLE" => GetMessage("ESTELIFE_T_CERTIFICATION")),
	array("DIV" => "edit3", "TAB" => GetMessage("ESTELIFE_T_FITTING"), "ICON" => "estelife_r_fitting", "TITLE" => GetMessage("ESTELIFE_T_FITTING")),
	array("DIV" => "edit4", "TAB" => GetMessage("ESTELIFE_T_GALLERY"), "ICON" => "estelife_r_gallery", "TITLE" => GetMessage("ESTELIFE_T_GALLERY")),
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
		foreach($_POST as $sKey=>$sValue)
			$arResult['apparatus'][$sKey]=$sValue;
	}
}

//Получение всех типов аппаратов
$obQuery = $obApp->createQuery();
$obQuery->builder()->from('iblock_element');
$obQuery->builder()
	->field('ID')
	->field('NAME');
$obQuery->builder()->filter()->_eq('IBLOCK_ID', 31);
$arResult['types'] = $obQuery->select()->all();

?>
	<script type="text/javascript" src="/bitrix/js/estelife/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/jquery-ui-1.10.3.custom.min.js"></script>
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
		<td width="60%"><input type="text" name="name" size="60" maxlength="255" value="<?=$arResult['apps']['name']?>"></td>
	</tr>
	<tr>
		<td width="40%"><?=GetMessage("ESTELIFE_F_TRANSLIT")?></td>
		<td width="60%"><input type="text" name="translit" size="60" maxlength="255" value="<?=$arResult['apps']['translit']?>"></td>
	</tr>
	<tr>
		<td width="40%"><?=GetMessage("ESTELIFE_F_LOGO")?></td>
		<td width="60%">
			<?echo CFileInput::Show("logo_id", $arResult['apps']['logo_id'],
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
	<tr class="adm-detail-required-field">
		<td width="40%"><?=GetMessage("ESTELIFE_F_COMPANY")?></td>
		<td width="60%">
			<input type="hidden" name="company_type_id" value="3" />
			<input type="hidden" name="company_id" value="<?=$arResult['apps']['company_id']?>" />
			<?php if (!empty($arResult['apps']['company_type_name'])):?>
				<input type="text" name="company_name" data-input="company_id" value="<?=$arResult['apps']['company_type_name']?>" />
			<?php else:?>
				<input type="text" name="company_name" data-input="company_id" value="<?=$arResult['apps']['company_name']?>" />
			<?php endif?>
		</td>
	</tr>
	<tr class="adm-detail-required-field">
		<td width="40%"><?=GetMessage("ESTELIFE_F_FORMAT")?></td>
		<td width="60%">
			<ul class="estelife-checklist">
				<li>
					<label for="format_1"><input type="checkbox" name="format[]" id="format_1" value="1"<?=(in_array(1,$arResult['apps']['format']) ? ' checked="true"' : '')?> />Anti-Age терапия</label>
				</li>
				<li>
					<label for="format_2"><input type="checkbox" name="format[]" id="format_2" value="2"<?=(in_array(2,$arResult['apps']['format']) ? ' checked="true"' : '')?> />Коррекция фигуры</label>
				</li>
				<li>
					<label for="format_3"><input type="checkbox" name="format[]" id="format_3" value="3"<?=(in_array(3,$arResult['apps']['format']) ? ' checked="true"' : '')?> />Эпиляция</label>
				</li>
				<li>
					<label for="format_4"><input type="checkbox" name="format[]" id="format_4" value="4"<?=(in_array(4,$arResult['apps']['format']) ? ' checked="true"' : '')?> />Миостимуляция</label>
				</li>
				<li>
					<label for="format_5"><input type="checkbox" name="format[]" id="format_5" value="5"<?=(in_array(5,$arResult['apps']['format']) ? ' checked="true"' : '')?> />Микротоки</label>
				</li>
				<li>
					<label for="format_6"><input type="checkbox" name="format[]" id="format_6" value="6"<?=(in_array(6,$arResult['apps']['format']) ? ' checked="true"' : '')?> />Лазеры</label>
				</li>
				<li>
					<label for="format_7"><input type="checkbox" name="format[]" id="format_7" value="7"<?=(in_array(7,$arResult['apps']['format']) ? ' checked="true"' : '')?> />Диагностика</label>
				</li>
				<li>
					<label for="format_8"><input type="checkbox" name="format[]" id="format_8" value="8"<?=(in_array(8,$arResult['apps']['format']) ? ' checked="true"' : '')?> />Реабилитация</label>
				</li>
				<li>
					<label for="format_9"><input type="checkbox" name="format[]" id="format_9" value="9"<?=(in_array(9,$arResult['apps']['format']) ? ' checked="true"' : '')?> />Микропигментация</label>
				</li>
			</ul>
		</td>
	</tr>
	<tr class="adm-detail-required-field">
		<td width="40%"><?=GetMessage("ESTELIFE_F_TYPE")?></td>
		<td width="60%">
			<select name="type_id">
				<option value=""><?=GetMessage("ESTELIFE_F_TYPE_SELECT")?></option>
				<?php foreach ($arResult['types'] as $val):?>
					<option value="<?=$val['ID']?>" <?if ($val['ID'] == $arResult['apps']['type_id']):?>selected<?endif?>><?=$val['NAME']?></option>
				<?php endforeach?>
			</select>
		</td>
	</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_PREVIEW")?></td>
			<td width="60%">
				<textarea name="preview_text" rows="12" style="width:70%"><?=str_replace("<br />", "\r\n", $arResult['apps']['preview_text'])?></textarea>
			</td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_DETAIL")?></td>
			<td width="60%">
				<textarea name="detail_text" rows="12" style="width:70%"><?=str_replace("<br />", "\r\n", $arResult['apps']['detail_text'])?></textarea>
			</td>
		</tr>
	<tr>
		<td width="40%"><?=GetMessage("ESTELIFE_F_ACTION")?></td>
		<td width="60%">
			<textarea name="action" rows="12" style="width:70%"><?=$arResult['apps']['action']?></textarea>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=GetMessage("ESTELIFE_F_EVIDENCE")?></td>
		<td width="60%">
			<textarea name="evidence" rows="12" style="width:70%"><?=$arResult['apps']['evidence']?></textarea>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=GetMessage("ESTELIFE_F_CONTRA")?></td>
		<td width="60%">
			<textarea name="contra" rows="12" style="width:70%"><?=$arResult['apps']['contra']?></textarea>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=GetMessage("ESTELIFE_F_SPECS")?></td>
		<td width="60%">
			<textarea name="specs" rows="12" style="width:70%"><?=$arResult['apps']['specs']?></textarea>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=GetMessage("ESTELIFE_F_FUNC")?></td>
		<td width="60%">
			<textarea name="func" rows="12" style="width:70%"><?=$arResult['apps']['func']?></textarea>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=GetMessage("ESTELIFE_F_REGISTRATION")?></td>
		<td width="60%">
			<textarea name="registration" rows="12" style="width:70%"><?=$arResult['apps']['registration']?></textarea>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=GetMessage("ESTELIFE_F_ADVANTAGES")?></td>
		<td width="60%">
			<textarea name="advantages" rows="12" style="width:70%"><?=$arResult['apps']['advantages']?></textarea>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=GetMessage("ESTELIFE_F_PROCEDURE")?></td>
		<td width="60%">
			<textarea name="procedure" rows="12" style="width:70%"><?=$arResult['apps']['procedure']?></textarea>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=GetMessage("ESTELIFE_F_SECURITY")?></td>
		<td width="60%">
			<textarea name="security" rows="12" style="width:70%"><?=$arResult['apps']['security']?></textarea>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=GetMessage("ESTELIFE_F_PROTOCOL")?></td>
		<td width="60%">
			<textarea name="protocol" rows="12" style="width:70%"><?=$arResult['apps']['protocol']?></textarea>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=GetMessage("ESTELIFE_F_UNDESIRED")?></td>
		<td width="60%">
			<textarea name="undesired" rows="12" style="width:70%"><?=$arResult['apps']['undesired']?></textarea>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=GetMessage("ESTELIFE_F_EQUIPMENT")?></td>
		<td width="60%">
			<textarea name="equipment" rows="12" style="width:70%"><?=$arResult['apps']['equipment']?></textarea>
		</td>
	</tr>
		<?php $tabControl->BeginNextTab();?>
		<tr>
			<td colspan="2">
				<input type="file" name="certification[]" class="gallery" />
				<?php if(!empty($arResult['apps']['certification'])): ?>
					<div class="estelife-pill-photos">
						<?php foreach($arResult['apps']['certification'] as $arPhoto): ?>
							<div class="item" >
								<div class="image">
									<?=CFile::ShowImage($arPhoto['original'],300,300)?>
								</div>
								<div class="desc" id="tr_photo_descriptions_<?=$arPhoto['id']?>_editor">
									<label for="phdl<?=$arPhoto['id']?>"><input type="checkbox" id="phdl<?=$arPhoto['id']?>" name="photo_deleted[]" value="<?=$arPhoto['id']?>">Удалить</label>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</td>
		</tr>
	<?php $tabControl->BeginNextTab();?>
	<tr>
		<td colspan="2">
			<input type="file" name="fitting[]" class="gallery" />
			<?php if(!empty($arResult['apps']['fitting'])): ?>
				<div class="estelife-photos">
					<?php foreach($arResult['apps']['fitting'] as $arPhoto): ?>
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
	<?php $tabControl->BeginNextTab();?>
	<tr>
		<td colspan="2">
			<input type="file" name="gallery[]" class="gallery" />
			<?php if(!empty($arResult['apps']['gallery'])): ?>
				<div class="estelife-pill-photos">
					<?php foreach($arResult['apps']['gallery'] as $arPhoto): ?>
						<div class="item" >
							<div class="image">
								<?=CFile::ShowImage($arPhoto['original'],300,300)?>
							</div>
							<div class="desc" id="tr_photo_descriptions_<?=$arPhoto['id']?>_editor">
								<label for="phdl<?=$arPhoto['id']?>"><input type="checkbox" id="phdl<?=$arPhoto['id']?>" name="photo_deleted[]" value="<?=$arPhoto['id']?>">Удалить</label>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</td>
	</tr>
	<?php
	$tabControl->EndTab();
	$tabControl->Buttons(array("disabled"=>false, "back_url"=>(strlen($back_url) > 0 ? $back_url : "estelife_apparatus_list.php?lang=".LANGUAGE_ID)));
	$tabControl->End();
	?>
	</form>
<?php
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");