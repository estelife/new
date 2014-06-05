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


$obPills= VDatabase::driver();
$obCompaniesColl=new VCompanies();


if(!empty($ID)){
	$obQuery = $obPills->createQuery();
	$obQuery->builder()->from('estelife_threads', 'ep');
	$obJoin = $obQuery->builder()->join();
	$obJoin->_left()
		->_from('ep', 'company_id')
		->_to('estelife_companies', 'id', 'ec');
	$obJoin->_left()
		->_from('ec', 'id')
		->_to('estelife_company_types', 'company_id', 'ect')
		->_cond()->_eq('ect.type', 3);
	$obQuery->builder()
		->field('ep.*')
		->field('ec.name', 'company_name')
		->field('ect.name', 'company_type_name');
	$obQuery->builder()->filter()
		->_eq('ep.id', $ID);
	$obResult = $obQuery->select();
	$arResult['pills']=$obResult->assoc();

	$arResult['pills']['preview_text'] = html_entity_decode($arResult['pills']['preview_text'],ENT_QUOTES,'utf-8');
	$arResult['pills']['detail_text'] = html_entity_decode($arResult['pills']['detail_text'],ENT_QUOTES,'utf-8');
	$arResult['pills']['action'] = html_entity_decode($arResult['pills']['action'],ENT_QUOTES,'utf-8');
	$arResult['pills']['evidence'] = html_entity_decode($arResult['pills']['evidence'],ENT_QUOTES,'utf-8');
	$arResult['pills']['contra'] = html_entity_decode($arResult['pills']['contra'],ENT_QUOTES,'utf-8');
	$arResult['pills']['structure'] = html_entity_decode($arResult['pills']['structure'],ENT_QUOTES,'utf-8');
	$arResult['pills']['registration'] = html_entity_decode($arResult['pills']['registration'],ENT_QUOTES,'utf-8');
	$arResult['pills']['advantages'] = html_entity_decode($arResult['pills']['advantages'],ENT_QUOTES,'utf-8');
	$arResult['pills']['usage'] = html_entity_decode($arResult['pills']['usage'],ENT_QUOTES,'utf-8');
	$arResult['pills']['area'] = html_entity_decode($arResult['pills']['area'],ENT_QUOTES,'utf-8');
	$arResult['pills']['effect'] = html_entity_decode($arResult['pills']['effect'],ENT_QUOTES,'utf-8');
	$arResult['pills']['security'] = html_entity_decode($arResult['pills']['security'],ENT_QUOTES,'utf-8');
	$arResult['pills']['mix'] = html_entity_decode($arResult['pills']['mix'],ENT_QUOTES,'utf-8');
	$arResult['pills']['specs'] = html_entity_decode($arResult['pills']['specs'],ENT_QUOTES,'utf-8');
	$arResult['pills']['protocol'] = html_entity_decode($arResult['pills']['protocol'],ENT_QUOTES,'utf-8');
	$arResult['pills']['form'] = html_entity_decode($arResult['pills']['form'],ENT_QUOTES,'utf-8');
	$arResult['pills']['storage'] = html_entity_decode($arResult['pills']['storage'],ENT_QUOTES,'utf-8');
	$arResult['pills']['undesired'] = html_entity_decode($arResult['pills']['undesired'],ENT_QUOTES,'utf-8');
	$arResult['pills']['specialist'] = html_entity_decode($arResult['pills']['specialist'],ENT_QUOTES,'utf-8');
	$arResult['pills']['patient'] = html_entity_decode($arResult['pills']['patient'],ENT_QUOTES,'utf-8');

	//Получение галереи
	$obQuery = $obPills->createQuery();
	$obQuery->builder()->from('estelife_threads_photos');
	$obQuery->builder()->filter()
		->_eq('thread_id', $ID);
	$obResult = $obQuery->select();
	foreach ($obResult->all() as $val){
		if ($val['type'] == 1){
			$arResult['pills']['gallery'][] = $val;
		}elseif ($val['type'] == 2){
			$arResult['pills']['reg'][] = $val;
		}
	}

	//Получение типов препаратов
	$obQuery = $obPills->createQuery();
	$obQuery->builder()->from('estelife_threads_type');
	$obQuery->builder()->filter()
		->_eq('thread_id', $ID);
	$obResult = $obQuery->select();
	$arFormats=$obResult->all();
	foreach ($arFormats as $val){
		$arResult['pills']['format'][]=$val['type_id'];
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

		$obError->raise();

		if($obPost->blank('translit')){
			$arTranslit = VString::translit($obPost->one('name'));
		}else{
			$arTranslit = $obPost->one('translit');
		}

		$nTime=time();

		//Добавление препарата
		$obQuery = $obPills->createQuery();
		$obQuery->builder()->from('estelife_threads')
			->value('type_id', 2)
			->value('name', trim(htmlentities($obPost->one('name'),ENT_QUOTES,'utf-8')))
			->value('date_edit', $nTime)
			->value('translit', trim($arTranslit))
			->value('company_id', intval($obPost->one('company_id')))
			->value('preview_text', htmlentities($obPost->one('preview_text'),ENT_QUOTES,'utf-8'))
			->value('detail_text', htmlentities($obPost->one('detail_text'),ENT_QUOTES,'utf-8'))
			->value('action', htmlentities($obPost->one('action'),ENT_QUOTES,'utf-8'))
			->value('evidence', htmlentities($obPost->one('evidence'),ENT_QUOTES,'utf-8'))
			->value('contra', htmlentities($obPost->one('contra'),ENT_QUOTES,'utf-8'))
			->value('structure', htmlentities($obPost->one('structure'),ENT_QUOTES,'utf-8'))
			->value('registration', htmlentities($obPost->one('registration'),ENT_QUOTES,'utf-8'))
			->value('advantages', htmlentities($obPost->one('advantages'),ENT_QUOTES,'utf-8'))
			->value('usage', htmlentities($obPost->one('usage'),ENT_QUOTES,'utf-8'))
			->value('area', htmlentities($obPost->one('area'),ENT_QUOTES,'utf-8'))
			->value('effect', htmlentities($obPost->one('effect'),ENT_QUOTES,'utf-8'))
			->value('security', htmlentities($obPost->one('security'),ENT_QUOTES,'utf-8'))
			->value('mix', htmlentities($obPost->one('mix'),ENT_QUOTES,'utf-8'))
			->value('specs', htmlentities($obPost->one('specs'),ENT_QUOTES,'utf-8'))
			->value('protocol', htmlentities($obPost->one('protocol'),ENT_QUOTES,'utf-8'))
			->value('form', htmlentities($obPost->one('form'),ENT_QUOTES,'utf-8'))
			->value('storage', htmlentities($obPost->one('storage'),ENT_QUOTES,'utf-8'))
			->value('undesired', htmlentities($obPost->one('undesired'),ENT_QUOTES,'utf-8'))
			->value('specialist', htmlentities($obPost->one('specialist'),ENT_QUOTES,'utf-8'))
			->value('patient', htmlentities($obPost->one('patient'),ENT_QUOTES,'utf-8'));


		if ($obPost->one('logo_id_del') == 'Y'){
			$obQuery->builder()->value('logo_id', 0);
		}

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
			$idPill = $ID;

			//Удаляем привязку к типам
			$obQuery =  $obPills->createQuery();
			$obQuery->builder()->from('estelife_threads_type')
				->filter()
				->_eq('thread_id', $idPill);
			$obQuery->delete();
		}else{
			$obQuery->builder()->value('date_create', $nTime);
			$idPill = $obQuery->insert()->insertId();
		}

		if ($obPost->one('logo_id_del') == 'Y' && $arResult['apps']['logo_id']>0){
			CFile::Delete($arResult['apps']['logo_id']);
		}

		//Пишем тип препарата
		if (!$obPost->blank('format')){
			$arPillsType = $obPost->one('format');
			foreach ($arPillsType as $val){
				$obQuery = $obPills->createQuery();
				$obQuery->builder()->from('estelife_threads_type')
					->value('type_id', intval($val))
					->value('thread_id', $idPill);
				$idPillType = $obQuery->insert()->insertId();
			}
		}


		//Пишем описание к фоткам
		$arPost=$obPost->all();
		foreach($arPost as $sKey=>$mValue){
			if(preg_match('#^photo_descriptions_([0-9]+)$#i',$sKey,$arMatches)){
				try{
					$obQuery = $obPills->createQuery();
					$obQuery->builder()->from('estelife_threads_photos');
					$obQuery->builder()->filter()->_eq('id', $arMatches[1]);
					$arPhoto = $obQuery->select()->assoc();
					if (!empty($arPhoto)){
						$obQuery = $obPills->createQuery();
						$obQuery->builder()->from('estelife_threads_photos')
							->value('description', htmlentities($mValue,ENT_QUOTES,'utf-8'));
						$obQuery->builder()->filter()->_eq('id', $arMatches[1]);
						$obQuery->update();
					}
				}catch(\core\database\exceptions\VCollectionException $e){}
			}
		}

		//Пишем ссылки на фото до/после
		if(!$obPost->blank('photo_deleted')){
			$arDeleted=$obPost->one('photo_deleted');
			foreach($arDeleted as $nDelete){
				try{
					$obQuery = $obPills->createQuery();
					$obQuery->builder()->from('estelife_threads_photos');
					$obQuery->builder()->filter()->_eq('id', $nDelete);
					$arPhoto = $obQuery->select()->assoc();
					if (!empty($arPhoto)){
						CFile::Delete($arPhoto['original']);
						$obQuery = $obPills->createQuery();
						$obQuery->builder()->from('estelife_threads_photos');
						$obQuery->builder()->filter()->_eq('id', $nDelete);
						$obQuery->delete();
					}
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

				$obQuery = $obPills->createQuery();
				$obQuery->builder()->from('estelife_threads_photos')
					->value('original', $nImageId)
					->value('thread_id', $idPill)
					->value('type',1);
				$idPillPhoto = $obQuery->insert()->insertId();
			}
		}

		if(!empty($_FILES['registration'])){
			$arFiles=$_FILES['registration'];
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

				$obQuery = $obPills->createQuery();
				$obQuery->builder()->from('estelife_threads_photos')
					->value('original', $nImageId)
					->value('thread_id', $idPill)
					->value('type',2);
				$idPillPhoto = $obQuery->insert()->insertId();
			}
		}

		if(!$obPost->blank('save'))
			LocalRedirect('/bitrix/admin/estelife_threads_list.php?lang='.LANGUAGE_ID);
		else
			LocalRedirect('/bitrix/admin/estelife_threads_edit.php?lang='.LANGUAGE_ID.'&ID='.$idPill);
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
	array("DIV" => "edit2", "TAB" => GetMessage("ESTELIFE_T_GALLERY"), "ICON" => "estelife_r_gallery", "TITLE" => GetMessage("ESTELIFE_T_GALLERY")),
	array("DIV" => "edit3", "TAB" => GetMessage("ESTELIFE_T_REGISTRATION"), "ICON" => "estelife_r_registration", "TITLE" => GetMessage("ESTELIFE_T_REGISTRATION"))
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
			$arResult['pills'][$sKey]=$sValue;
	}
}

//Получение всех типов препаратов
$obQuery = $obPills->createQuery();
$obQuery
	->builder()
	->from('estelife_threads_typename');
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
		<td width="10%"><?=GetMessage("ESTELIFE_F_TITLE")?></td>
		<td width="90%"><input type="text" name="name" size="60" maxlength="255" value="<?=$arResult['pills']['name']?>"></td>
	</tr>
	<tr>
		<td width="10%"><?=GetMessage("ESTELIFE_F_TRANSLIT")?></td>
		<td width="90%"><input type="text" name="translit" size="60" maxlength="255" value="<?=$arResult['pills']['translit']?>"></td>
	</tr>
	<tr>
		<td width="10%"><?=GetMessage("ESTELIFE_F_LOGO")?></td>
		<td width="90%">
			<?echo CFileInput::Show("logo_id", $arResult['pills']['logo_id'],
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
		<td width="10%"><?=GetMessage("ESTELIFE_F_COMPANY")?></td>
		<td width="90%">
			<input type="hidden" name="company_type_id" value="3" />
			<input type="hidden" name="company_id" value="<?=$arResult['pills']['company_id']?>" />
			<?php if (!empty($arResult['pills']['company_type_name'])):?>
				<input type="text" name="company_name" data-input="company_id" value="<?=$arResult['pills']['company_type_name']?>" />
			<?php else:?>
				<input type="text" name="company_name" data-input="company_id" value="<?=$arResult['pills']['company_name']?>" />
			<?php endif?>
		</td>
	</tr>
	<?php if (!empty($arResult['types'])):?>
		<tr>
			<td width="10%"><?=GetMessage("ESTELIFE_F_FORMAT")?></td>
			<td width="90%">
				<ul class="estelife-checklist">
					<?php foreach ($arResult['types'] as $val):?>
						<li>
							<label for="format_<?=$val['id']?>"><input type="checkbox" name="format[]" id="format_<?=$val['id']?>" value="<?=$val['id']?>"<?=(in_array($val['id'],$arResult['pills']['format']) ? ' checked="true"' : '')?> /><?=$val['name']?></label>
						</li>
					<?php endforeach?>
				</ul>
			</td>
		</tr>
	<?php endif?>
	<tr>
		<td width="10%">1. <?=GetMessage("ESTELIFE_F_PREVIEW")?></td>
		<td width="90%">
			<textarea name="preview_text" rows="15" style="width:90%"><?=str_replace("<br />", "\r\n", $arResult['pills']['preview_text'])?></textarea>
		</td>
	</tr>
	<tr>
		<td width="10%">2. <?=GetMessage("ESTELIFE_F_DETAIL")?></td>
		<td width="90%">
			<textarea name="detail_text" rows="15" style="width:90%"><?=str_replace("<br />", "\r\n", $arResult['pills']['detail_text'])?></textarea>
		</td>
	</tr>
	<tr>
		<td width="10%">3. <?=GetMessage("ESTELIFE_F_ACTION")?></td>
		<td width="90%">
			<textarea name="action" rows="15" style="width:90%"><?=$arResult['pills']['action']?></textarea>
		</td>
	</tr>
	<tr>
		<td width="10%">4. <?=GetMessage("ESTELIFE_F_EVIDENCE")?></td>
		<td width="90%">
			<textarea name="evidence" rows="15" style="width:90%"><?=$arResult['pills']['evidence']?></textarea>
		</td>
	</tr>
	<tr>
		<td width="10%">5. <?=GetMessage("ESTELIFE_F_CONTRA")?></td>
		<td width="90%">
			<textarea name="contra" rows="15" style="width:90%"><?=$arResult['pills']['contra']?></textarea>
		</td>
	</tr>
	<tr>
		<td width="10%">6. <?=GetMessage("ESTELIFE_F_AREA")?></td>
		<td width="90%">
			<textarea name="area" rows="15" style="width:90%"><?=$arResult['pills']['area']?></textarea>
		</td>
	</tr>
	<tr>
		<td width="10%">7. <?=GetMessage("ESTELIFE_F_USAGE")?></td>
		<td width="90%">
			<textarea name="usage" rows="15" style="width:90%"><?=$arResult['pills']['usage']?></textarea>
		</td>
	</tr>
	<tr>
		<td width="10%">8. <?=GetMessage("ESTELIFE_F_REGISTRATION")?></td>
		<td width="90%">
			<textarea name="registration" rows="15" style="width:90%"><?=$arResult['pills']['registration']?></textarea>
		</td>
	</tr>
	<tr>
		<td width="10%">9. <?=GetMessage("ESTELIFE_F_SECURITY")?></td>
		<td width="90%">
			<textarea name="security" rows="15" style="width:90%"><?=$arResult['pills']['security']?></textarea>
		</td>
	</tr>
	<tr>
		<td width="10%">10. <?=GetMessage("ESTELIFE_F_EFFECT")?></td>
		<td width="90%">
			<textarea name="effect" rows="15" style="width:90%"><?=$arResult['pills']['effect']?></textarea>
		</td>
	</tr>
	<tr>
		<td width="10%">11. <?=GetMessage("ESTELIFE_F_UNDESIRED")?></td>
		<td width="90%">
			<textarea name="undesired" rows="15" style="width:90%"><?=$arResult['pills']['undesired']?></textarea>
		</td>
	</tr>
	<tr>
		<td width="10%">12. <?=GetMessage("ESTELIFE_F_STRUCTURE")?></td>
		<td width="90%">
			<textarea name="structure" rows="15" style="width:90%"><?=$arResult['pills']['structure']?></textarea>
		</td>
	</tr>
	<tr>
		<td width="10%">13. <?=GetMessage("ESTELIFE_F_ADVANTAGES")?></td>
		<td width="90%">
			<textarea name="advantages" rows="15" style="width:90%"><?=$arResult['pills']['advantages']?></textarea>
		</td>
	</tr>
	<tr>
		<td width="10%">14. <?=GetMessage("ESTELIFE_F_MIX")?></td>
		<td width="90%">
			<textarea name="mix" rows="15" style="width:90%"><?=$arResult['pills']['mix']?></textarea>
		</td>
	</tr>
	<tr>
		<td width="10%">15. <?=GetMessage("ESTELIFE_F_PATIENT")?></td>
		<td width="90%">
			<textarea name="patient" rows="15" style="width:90%"><?=$arResult['pills']['patient']?></textarea>
		</td>
	</tr>
	<tr>
		<td width="10%">16. <?=GetMessage("ESTELIFE_F_SPECIALIST")?></td>
		<td width="90%">
			<textarea name="specialist" rows="15" style="width:90%"><?=$arResult['pills']['specialist']?></textarea>
		</td>
	</tr>
	<tr>
		<td width="10%">17. <?=GetMessage("ESTELIFE_F_PROTOCOL")?></td>
		<td width="90%">
			<textarea name="protocol" rows="15" style="width:90%"><?=$arResult['pills']['protocol']?></textarea>
		</td>
	</tr>
	<tr>
		<td width="10%">18. <?=GetMessage("ESTELIFE_F_SPECS")?></td>
		<td width="90%">
			<textarea name="specs" rows="15" style="width:90%"><?=$arResult['pills']['specs']?></textarea>
		</td>
	</tr>
	<tr>
		<td width="10%">19. <?=GetMessage("ESTELIFE_F_FORM")?></td>
		<td width="90%">
			<textarea name="form" rows="15" style="width:90%"><?=$arResult['pills']['form']?></textarea>
		</td>
	</tr>
	<tr>
		<td width="10%">20. <?=GetMessage("ESTELIFE_F_STORAGE")?></td>
		<td width="90%">
			<textarea name="storage" rows="15" style="width:90%"><?=$arResult['pills']['storage']?></textarea>
		</td>
	</tr>

	<?php $tabControl->BeginNextTab();?>
	<tr>
		<td colspan="2">
			<input type="file" name="gallery[]" id="gallery" />
			<?php if(!empty($arResult['pills']['gallery'])): ?>
				<div class="estelife-photos">
					<?php foreach($arResult['pills']['gallery'] as $arPhoto): ?>
						<div class="item" >
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
			<input type="file" name="registration[]" id="registration" />
			<?php if(!empty($arResult['pills']['reg'])): ?>
				<div class="estelife-photos">
					<?php foreach($arResult['pills']['reg'] as $arPhoto): ?>
						<div class="item" >
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
	$tabControl->Buttons(array("disabled"=>false, "back_url"=>(strlen($back_url) > 0 ? $back_url : "estelife_threads_list.php?lang=".LANGUAGE_ID)));
	$tabControl->End();
	?>
	</form>
<?php
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");