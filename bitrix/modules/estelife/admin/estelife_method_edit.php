<?php
use core\database\exceptions\VCollectionException;
use core\database\VDatabase;
use core\types\VArray;
use reference\services\VSpecs;
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

$obColl= VDatabase::driver();

$obSpecs=$obColl->createQuery();
$obSpecs->builder()->from('estelife_specializations')->sort('name','asc');
$arSpecs=$obSpecs->select()->all();


$arServs=array();

$obRecord=null;

if(!empty($ID)){
	try{

		$obQuery = $obColl->createQuery();
		$obQuery->builder()->from('estelife_methods')
			->filter()
			->_eq('id', $ID);
		$arResult['spec'] = $obQuery->select()->assoc();


		$obQuery = $obColl->createQuery();
		$obQuery->builder()->from('estelife_services')
			->sort('name','asc')
			->filter()
				->_eq('specialization_id',$arResult['spec']['specialization_id']);
		$arServs=$obQuery->select()->all();
	}catch(VCollectionException $e){}
}

if($_SERVER['REQUEST_METHOD']=='POST'){
	$obPost=new VArray($_POST);
	$obError=new ex\VFormException();

	try{
		if($obPost->blank('name'))
			$obError->setFieldError('NAME_NOT_FILL','name');

		if($obPost->blank('specialization_id'))
			$obError->setFieldError('SPEC_NOT_FILL','specialization_id');

		if($obPost->blank('service_id'))
			$obError->setFieldError('SERVICE_NOT_FILL','service_id');

		$obError->raise();

		$obQuery = $obColl->createQuery();
		$obQuery->builder()->from('estelife_methods')
			->value('specialization_id', intval($obPost->one('specialization_id')))
			->value('service_id', intval($obPost->one('service_id')))
			->value('name', trim(strip_tags($obPost->one('name'))));

		if (!empty($ID)){
			$obQuery->builder()->filter()
				->_eq('id',$ID);
			$obQuery->update();
			$idMethod = $ID;
		}else{
			$idMethod = $obQuery->insert()->insertId();
			$ID =$idMethod;

		}

		if(!empty($idMethod)){
			if(!$obPost->blank('save'))
				LocalRedirect('/bitrix/admin/estelife_method_list.php?lang='.LANGUAGE_ID);
			else
				LocalRedirect('/bitrix/admin/estelife_method_edit.php?lang='.LANGUAGE_ID.'&ID='.$idMethod);
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
	array("DIV" => "edit1", "TAB" => GetMessage("ESTELIFE_T_BASE"), "ICON" => "estelife_r_base", "TITLE" => GetMessage("ESTELIFE_T_BASE_TITLE"))
);
$tabControl = new CAdminTabControl("estelife_method_".$ID, $aTabs, true, true);
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
			<td width="60%"><input type="text" name="name" size="60" maxlength="255" value="<?=$arResult['spec']['name']?>"></td>
		</tr>
		<tr class="adm-detail-required-field">
			<td width="40%"><?=GetMessage("ESTELIFE_F_SPEC")?></td>
			<td width="60%">
				<select name="specialization_id" value="">
					<option value="0"><?=GetMessage('ESTELIFE_NOT_IMPORTANT')?></option>
					<?php foreach($arSpecs as $arSpec): ?>
						<option value="<?=$arSpec['id']?>"<?=($arSpec['id']==$arResult['spec']['specialization_id']) ? ' selected="selected"' : ''?>><?=$arSpec['name']?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr class="adm-detail-required-field">
			<td width="40%"><?=GetMessage("ESTELIFE_F_SERVICE")?></td>
			<td width="60%">
				<select name="service_id" value="">
					<option value="0"><?=GetMessage('ESTELIFE_NOT_IMPORTANT')?></option>
					<?php foreach($arServs as $arServ): ?>
						<option value="<?=$arServ['id']?>"<?=($arServ['id']==$arResult['spec']['service_id']) ? ' selected="selected"' : ''?>><?=$arServ['name']?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>

		<?php
		$tabControl->EndTab();
		$tabControl->Buttons(array("disabled"=>false, "back_url"=>(strlen($back_url) > 0 ? $back_url : "estelife_method_list.php?lang=".LANGUAGE_ID)));
		$tabControl->End();
		?>
	</form>
<?php
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");