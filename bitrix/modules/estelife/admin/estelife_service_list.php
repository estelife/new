<?php
use core\database\mysql\VFilter;
use core\database\VDatabase;
use reference\services\VServices;
use reference\services\VSpecs;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$sTableID = "tbl_estelife_service_list";
$oSort = new CAdminSorting($sTableID, "ID", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/estelife/prolog.php");

ClearVars();
$FORM_RIGHT = $APPLICATION->GetGroupRight("estelife");

if($FORM_RIGHT<="D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

CModule::IncludeModule("estelife");
IncludeModuleLangFile(__FILE__);

//===== FILTER ==========
$arFilterFields = Array(
	"find_id",
	"find_name",
	"find_specialization_id"
);
$lAdmin->InitFilter($arFilterFields);

InitBVar($find_id_exact_match);
InitBVar($find_name_exact_match);
InitBVar($find_specialization_id_exact_match);

//====== TABLE HEADERS =========
$headers = array(
	array("id"=>"NAME", "content"=>GetMessage("ESTELIFE_F_TITLE"), "sort"=>"name", "default"=>true),
	array("id"=>"SPECIALIZATION_ID", "content"=>GetMessage("ESTELIFE_F_SPEC"), "sort"=>"specialization_id", "default"=>true),
	array("id"=>"ID", "content"=>GetMessage("ESTELIFE_F_ID"), "sort"=>"id", "default"=>true)
);
$lAdmin->AddHeaders($headers);

//==== Здесь надо зафигачить генерацию списка ========
$obColl= VDatabase::driver();

$obSpecs=$obColl->createQuery();
$obSpecs->builder()->from('estelife_specializations')->sort('name','asc');
$arSpecs=$obSpecs->select()->all();

if(($arID = $lAdmin->GroupAction()) && check_bitrix_sessid()){
	foreach($arID as $ID){
		if(($ID = IntVal($ID))>0 && $_REQUEST['action']=='delete'){
			try{
				$obQuery = $obColl->createQuery();
				$obQuery->builder()->from('estelife_services')->filter()
					->_eq('id', $ID);
				$obQuery->delete();
			}catch(\core\database\exceptions\VCollectionException $e){}
		}
	}
}

$obQuery=$obColl->createQuery();
$obQuery->builder()->from('estelife_services')
	->sort($by,$order);
$obFilter=$obQuery->builder()->filter();

if(!empty($find_id))
	$obFilter->_like('id',$find_id,VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);

if(!empty($find_name))
	$obFilter->_like('name',$find_name,VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);

if(!empty($find_specialization_id))
	$obFilter->_eq('specialization_id',$find_specialization_id);

$arTemp=array();

foreach($arSpecs as $obRecord){
	$arTemp[$obRecord['id']]=$obRecord['name'];
}

$obSpecs=$obQuery->select()->all();
$arResult=array();

foreach($obSpecs as $obRecord){
	$arResult[]=$obRecord;

	$f_ID=$obRecord['id'];
	$row =& $lAdmin->AddRow($f_ID, $obRecord);

	$row->AddViewField("ID",$obRecord['id']);
	$row->AddViewField("NAME",$obRecord['name']);
	$row->AddViewField('SPECIALIZATION_ID',$arTemp[$obRecord['specialization_id']]);

	$arActions = Array();
	$arActions[] = array("DEFAULT"=>"Y", "ICON"=>"edit", "TITLE"=>GetMessage("ESTELIFE_EDIT_ALT"), "ACTION"=>$lAdmin->ActionRedirect("estelife_service_edit.php?lang=".LANGUAGE_ID."&ID=$f_ID"), "TEXT"=>GetMessage("ESTELIFE_EDIT"));
	$arActions[] = array("ICON"=>"delete", "TITLE"=>GetMessage("ESTELIFE_DELETE_ALT"),"ACTION"=>"javascript:if(confirm('".GetMessage("ESTELIFE_CONFIRM_DELETE")."')) window.location='?lang=".LANGUAGE_ID."&action=delete&ID=$f_ID&".bitrix_sessid_get()."'","TEXT"=>GetMessage("ESTELIFE_DELETE"));
	$row->AddActions($arActions);
}

$lAdmin->AddFooter(
	array(
		array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>1),//$rsData->SelectedRowsCount()),
		array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"),
	)
);

//========= Групповое удаление, если права позволяют
//if ($FORM_RIGHT=="W")
	$lAdmin->AddGroupActionTable(Array(
		"delete"=>GetMessage("FORM_DELETE_L"),
	));

//======= Контектстное меню ===========
//if ($FORM_RIGHT=="W")
//{
	$aMenu = array();
	$aMenu[] = array(
		"TEXT"	=>GetMessage("ESTELIFE_CREATE"),
		"TITLE"=>GetMessage("ESTELIFE_CREATE_TITLE"),
		"LINK"=>"estelife_service_edit.php?lang=".LANG,
		"ICON" => "btn_new"
	);

	$aContext = $aMenu;
	$lAdmin->AddAdminContextMenu($aContext);
//}

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("ESTELIFE_HEAD_TITLE"));
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>

	<a name="tb"></a>
	<form name="form1" method="GET" action="<?=$APPLICATION->GetCurPage()?>?">
		<?php
		$oFilter = new CAdminFilter(
			$sTableID."_filter",
			array(
				GetMessage("ESTELIFE_F_ID"),
				GetMessage("ESTELIFE_F_TITLE"),
				GetMessage("ESTELIFE_F_SPEC")
			)
		);
		$oFilter->Begin();
		?>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_ID")?></td>
			<td><input type="text" name="find_id" size="47" value="<?echo htmlspecialcharsbx($find_id)?>"></td>
		</tr>
		<tr>
			<td><b><?echo GetMessage("ESTELIFE_F_TITLE")?></b></td>
			<td><input type="text" name="find_name" size="47" value="<?echo htmlspecialcharsbx($find_name)?>"></td>
		</tr>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_SPEC")?></td>
			<td>
				<select name="find_specialization_id">
					<option value="0"><?echo GetMessage("ESTELIFE_NOT_IMPORTANT")?></option>
					<?php foreach($arSpecs as $arSpec): ?>
						<option value="<?=$arSpec['id']?>"<?=($arSpec['id']==$find_specialization_id) ? ' selected="selected"' : ''?>><?=$arSpec['name']?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<?
		$oFilter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage()));
		$oFilter->End();
		#############################################################
		?>
	</form>

<?
$lAdmin->DisplayList();
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");