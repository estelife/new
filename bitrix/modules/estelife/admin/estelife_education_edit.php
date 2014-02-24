<?php
use core\database\exceptions\VCollectionException;
use core\database\VDatabase;
use core\exceptions as ex;
use core\types\VArray;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/estelife/prolog.php");

ClearVars();
$FORM_RIGHT = $APPLICATION->GetGroupRight("estelife");

if($FORM_RIGHT<="D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

CModule::IncludeModule("estelife");
IncludeModuleLangFile(__FILE__);

define("HELP_FILE","estelife_list.php");
$ID = isset($_REQUEST['ID']) ? intval($_REQUEST['ID']) : 0;
$obQuery=VDatabase::driver()
	->createQuery();

if(!empty($ID)){
	try{
		$obQuery->builder()
			->from('estelife_education')
			->filter()
			->_eq('id',$ID);
		$arResult['education'] = $obQuery
			->select()
			->assoc();

		$arResult['education']['date'] = date('d.m.y H:i:s',strtotime($arResult['education']['date']));
	}catch(VCollectionException $e){}
}

if($_SERVER['REQUEST_METHOD']=='POST'){
	$obPost=new VArray($_POST);
	$obError=new ex\VFormException();

	try{
		if($obPost->blank('name'))
			$obError->setFieldError('NAME_NOT_FILL','name');

		if($obPost->blank('date'))
			$obError->setFieldError('DATE_NOT_FILL','date');

		if($obPost->blank('price'))
			$obError->setFieldError('PRICE_NOT_FILL','price');

		$obError->raise();

		$sDate = trim(strip_tags($obPost->one('date')));
		$sDate = strtotime($sDate);
		$sDate = date('Y-m-d H:i:s', $sDate);

		$obQuery->builder()
			->from('estelife_education')
			->value('date', $sDate)
			->value('name', trim(strip_tags($obPost->one('name'))))
			->value('price', floatval($obPost->one('price')));

		if (!empty($ID)){
			$obQuery->builder()
				->filter()
				->_eq('id',$ID);
			$obQuery->update();
			$idMethod = $ID;
		}else{
			$idMethod = $obQuery->insert()
				->insertId();
			$ID =$idMethod;
		}

		if(!empty($idMethod)){
			if(!$obPost->blank('save'))
				LocalRedirect('/bitrix/admin/estelife_education_list.php?lang='.LANGUAGE_ID);
			else
				LocalRedirect('/bitrix/admin/estelife_education_edit.php?lang='.LANGUAGE_ID.'&ID='.$idMethod);
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
	array(
		"DIV" => "edit1",
		"TAB" => GetMessage("ESTELIFE_T_BASE"),
		"ICON" => "estelife_r_base",
		"TITLE" => GetMessage("ESTELIFE_T_BASE_TITLE")
	)
);
$tabControl = new CAdminTabControl("estelife_education_".$ID, $aTabs, true, true);
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
			$arResult['education'][$sKey]=$sValue;
	}
}
?>
	<form name="estelife_education" method="POST" action="<?echo $APPLICATION->GetCurPage()?>" enctype="multipart/form-data">
		<?=bitrix_sessid_post()?>
		<input type="hidden" name="ID" value=<?=$ID?> />
		<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>" />
		<?php
			$tabControl->Begin();
			$tabControl->BeginNextTab()
		?>

		<tr class="adm-detail-required-field">
			<td width="40%"><?=GetMessage("ESTELIFE_F_NAME")?></td>
			<td width="60%"><input type="text" name="name" size="60" maxlength="255" value="<?=$arResult['education']['name']?>"></td>
		</tr>
		<tr class="adm-detail-required-field">
			<td width="40%"><?=GetMessage("ESTELIFE_F_DATE")?></td>
			<td width="60%">
				<?echo CAdminCalendar::CalendarDate("date", $arResult['education']['date'], 19, true)?>
			</td>
		</tr>
		<tr class="adm-detail-required-field">
			<td width="40%"><?=GetMessage("ESTELIFE_F_PRICE")?></td>
			<td width="60%"><input type="text" name="price" size="60" maxlength="255" value="<?=$arResult['education']['price']?>"></td>
		</tr>

		<?php
		$tabControl->EndTab();
		$tabControl->Buttons(array("disabled"=>false, "back_url"=>(strlen($back_url) > 0 ? $back_url : "estelife_education_list.php?lang=".LANGUAGE_ID)));
		$tabControl->End();
		?>
	</form>
<?php
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");