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

if($_SERVER['REQUEST_METHOD']=='POST'){
	$obPost=new VArray($_POST);
	$obError=new ex\VFormException();

	try{

		if($obPost->blank('name'))
			$obError->setFieldError('NAME_NOT_FILL','name');

		$obError->raise();

		//Добавление типа
		$obQuery = $obApp->createQuery();
		$obQuery->builder()->from('estelife_threads_typename')
			->value('name', trim(htmlentities($obPost->one('name'),ENT_QUOTES,'utf-8')));

		if (!empty($ID)){
			$obQuery->builder()->filter()
				->_eq('id',$ID);
			$obQuery->update();
			$idApp = $ID;
		}else{
			$idApp = $obQuery->insert()->insertId();
		}

		if(!$obPost->blank('save'))
			LocalRedirect('/bitrix/admin/estelife_threads_type_list.php?lang='.LANGUAGE_ID);
		else
			LocalRedirect('/bitrix/admin/estelife_threads_type_edit.php?lang='.LANGUAGE_ID.'&ID='.$idApp);
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
);
$tabControl = new CAdminTabControl("estelife_pills_type_".$ID, $aTabs, true, true);
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
			$arResult['type'][$sKey]=$sValue;
	}
}

?>
	<form name="estelife_type" method="POST" action="<?echo $APPLICATION->GetCurPage()?>" enctype="multipart/form-data">
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="ID" value=<?=$ID?> />
	<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>" />
	<?php
	$tabControl->Begin();
	$tabControl->BeginNextTab()
	?>
	<tr class="adm-detail-required-field">
		<td width="40%"><?=GetMessage("ESTELIFE_F_TITLE")?></td>
		<td width="60%"><input type="text" name="name" size="60" maxlength="255" value="<?=$arResult['type']['name']?>"></td>
	</tr>
	<?php
	$tabControl->EndTab();
	$tabControl->Buttons(array("disabled"=>false, "back_url"=>(strlen($back_url) > 0 ? $back_url : "estelife_threads_type_list.php?lang=".LANGUAGE_ID)));
	$tabControl->End();
	?>
	</form>
<?php
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");