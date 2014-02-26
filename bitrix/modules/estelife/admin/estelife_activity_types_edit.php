<?php
use core\exceptions as ex;
use core\types\VArray;
use core\types\VString;

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

$obTypes= \core\database\VDatabase::driver();

if(!empty($ID)){
	$obQuery=$obTypes->createQuery();
	$obQuery->builder()
		->from('estelife_activity_types');

	$obQuery->builder()->filter()->_eq('id',$ID);

	$obResult=$obQuery->select();
	$obResult=new CAdminResult(
		$obResult->bxResult(),
		$sTableID
	);
	$arResult=$obResult->Fetch();
}


if($_SERVER['REQUEST_METHOD']=='POST'){
	$obPost=new VArray($_POST);
	$obError=new ex\VFormException();

	try{
		if($obPost->blank('name'))
			$obError->setFieldError('NAME_NOT_NAME','name');

		if($obPost->blank('description'))
			$obError->setFieldError('NAME_NOT_DESCRIPTION','description');

		$obError->raise();

		$obQuery = $obTypes->createQuery();
		$obQuery->builder()->from('estelife_activity_types')
			->value('name', trim(htmlentities($obPost->one('name'),ENT_QUOTES,'utf-8')))
			->value('description', trim(htmlentities($obPost->one('description'),ENT_QUOTES,'utf-8')));


		if (!empty($ID)){
			$obQuery->builder()->filter()
				->_eq('id',$ID);
			$obQuery->update();
			$idEntr = $ID;
		}else{
			$idPill = $obQuery->insert()->insertId();
		}

		if(!$obPost->blank('save'))
			LocalRedirect('/bitrix/admin/estelife_activity_types_list.php?lang='.LANGUAGE_ID);
		else
			LocalRedirect('/bitrix/admin/estelife_activity_types_edit.php?lang='.LANGUAGE_ID.'&ID='.$idEntr);
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
	array("DIV" => "edit1", "TAB" => GetMessage("ESTELIFE_T_BASE"),/* "ICON" => "estelife_r_base", */"TITLE" => GetMessage("ESTELIFE_T_BASE")),
);
$tabControl = new CAdminTabControl("estelife_entry_concreate_".$ID, $aTabs, true, true);

//===== Тут будем делать сохрпанение и подготовку данных

$APPLICATION->SetTitle(GetMessage('ESTELIFE_HEAD_TITLE'));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php"); ?>

	<script type="text/javascript" src="/bitrix/js/estelife/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/jquery-ui-1.10.3.custom.min.js"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/estelife.js"></script>

	<form name="estelife_activity_types" method="POST" action="/bitrix/admin/estelife_activity_types_edit.php" enctype="multipart/form-data">
		<?=bitrix_sessid_post()?>
		<input type="hidden" name="ID" value=<?=$ID?> />
		<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>" />
		<?php
		$tabControl->Begin();
		$tabControl->BeginNextTab();
		?>
		<tr class="adm-detail-required-field">
			<td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("ESTELIFE_F_NAME")?></td>
			<td width="60%" class="adm-detail-content-cell-r"><input type="text" name="name" size="20" maxlength="50" value="<?=$arResult['name'];?>"></td>
		</tr>
		<tr class="adm-detail-required-field">
			<td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("ESTELIFE_F_DESRIPTION")?></td>
			<td width="60%" class="adm-detail-content-cell-r"><textarea name="description"  cols="39" rows="5"><?=$arResult['description'];?></textarea></td>
		</tr>
		<?php
		$tabControl->EndTab();
		$tabControl->Buttons(array("disabled"=>false, "back_url"=>(strlen($back_url) > 0 ? $back_url : "estelife_activity_types_list.php?lang=".LANGUAGE_ID)));
		$tabControl->End();
		?>
	</form>
<?php
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");

