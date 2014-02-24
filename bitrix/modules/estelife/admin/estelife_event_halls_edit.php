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

$obQuery= \core\database\VDatabase::driver()->createQuery();


if(!empty($ID)){
	$obQuery->builder()
		->from('estelife_event_halls','eh')
		->field('eh.name','name')
		->field('ee.short_name','event_name')
		->field('ee.id','event_id');
	$obJoin=$obQuery->builder()->join();
	$obJoin->_left()->
		_from('eh','event_id')->
		_to('estelife_events','id','ee');

	$obQuery->builder()->filter()->_eq('eh.id',$ID);
	$arResult['hall']=$obQuery->select()->assoc();
}


if($_SERVER['REQUEST_METHOD']=='POST'){
	$obPost=new VArray($_POST);
	$obError=new ex\VFormException();
	try{

		//не срабатывает
		if($obPost->blank('name'))
			$obError->setFieldError('NAME_NOT_FILL','name');

		if($obPost->blank('event_id'))
			$obError->setFieldError('NAME_NOT_FILL','event_name');

		$obError->raise();

		$obQuery= \core\database\VDatabase::driver()->createQuery();
		$obQuery->builder()->from('estelife_event_halls')
			->value('name', trim(htmlentities($obPost->one('name'),ENT_QUOTES,'utf-8')))
			->value('event_id', intval($obPost->one('event_id')));


		if (!empty($ID)){
			$obQuery->builder()->filter()
				->_eq('id',$ID);
			$obQuery->update();
			$idEntr = $ID;
		}else{
			$idPill = $obQuery->insert()->insertId();
		}


		if(!$obPost->blank('save'))
			LocalRedirect('/bitrix/admin/estelife_event_halls_list.php?lang='.LANGUAGE_ID);
		else
			LocalRedirect('/bitrix/admin/estelife_event_halls_edit.php?lang='.LANGUAGE_ID.'&ID='.$idEntr);
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

$APPLICATION->SetTitle(GetMessage('ESTELIFE_CREATE_TITLE'));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

?>

	<script type="text/javascript" src="/bitrix/js/estelife/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/jquery-ui-1.10.3.custom.min.js"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/estelife.js"></script>

	<form name="estelife_event_halls_edit" method="POST" action="/bitrix/admin/estelife_event_halls_edit.php" enctype="multipart/form-data">
		<?=bitrix_sessid_post()?>
		<input type="hidden" name="ID" value=<?=$ID?> />
		<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>" />
		<?php
		$tabControl->Begin();
		$tabControl->BeginNextTab();
		?>

		<tr class="adm-detail-required-field">
			<td width="40%"><?=GetMessage("ESTELIFE_F_NAME")?></td>
			<td width="60%"><input type="text" name="name" size="20" maxlength="255" value="<?=$arResult['hall']['name']?>"></td>
		</tr>

		<tr class="adm-detail-required-field">
			<td width="40%"><?=GetMessage("ESTELIFE_F_EVENT")?></td>
			<td width="60%">
				<input type="hidden" name="event_type_id" value="3" />
				<input type="hidden" name="event_id" value="<?=$arResult['hall']['event_id']?>" />
				<input type="text" name="event_name" size="40" data-input="event_id" value="<?=$arResult['hall']['event_name']?>" />
			</td>
		</tr>

		<?php
		$tabControl->EndTab();
		$tabControl->Buttons(array("disabled"=>false, "back_url"=>(strlen($back_url) > 0 ? $back_url : "estelife_event_halls_list.php?lang=".LANGUAGE_ID)));
		$tabControl->End();
		?>
	</form>
<?php
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");

