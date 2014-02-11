<?php
use core\database\mysql\VFilter;
use core\database\VDatabase;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$sTableID = "tbl_estelife_akzii_list";
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
	"find_clinic_id"
);
$lAdmin->InitFilter($arFilterFields);

$arFilter = Array(
	"id"						=> $find_id,
	"name"						=> $find_name,
	"clinic"						=> $find_clinic
);

//====== TABLE HEADERS =========
$headers = array(
	array("id"=>"NAME", "content"=>GetMessage("ESTELIFE_F_TITLE"), "sort"=>"name", "default"=>true),
	array("id"=>"CLINIC", "content"=>GetMessage("ESTELIFE_F_CLINIC"), "sort"=>"clinic", "default"=>true),
	array("id"=>"ID", "content"=>GetMessage("ESTELIFE_F_ID"), "sort"=>"id", "default"=>true)
);
$lAdmin->AddHeaders($headers);

//==== Здесь надо зафигачить генерацию списка ========
$obAkzii= VDatabase::driver();

if(($arID = $lAdmin->GroupAction()) && check_bitrix_sessid()){
	foreach($arID as $ID){
		if(($ID = IntVal($ID))>0 && $_REQUEST['action']=='delete'){
			try{
				$obQuery=$obAkzii->createQuery();
				$obQuery->builder()->from('estelife_akzii')->filter()
					->_eq('id', $ID);
				$obQuery->delete();
			}catch(\core\database\exceptions\VCollectionException $e){}
		}
	}
}

$obQuery=$obAkzii->createQuery();
$obQuery->builder()->from('estelife_akzii','ea');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ea','id')
	->_to('estelife_clinic_akzii','akzii_id','eca');
$obJoin->_left()
	->_from('eca','clinic_id')
	->_to('estelife_clinics','id','ec');
$obQuery->builder()
	->field('ea.id','id')
	->field('ea.name','name')
	->field('ec.name','clinic');

$obFilter=$obQuery->builder()->filter();

if(!empty($arFilter['id']))
	$obFilter->_like('ea.id',$arFilter['id'],VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);

if(!empty($arFilter['name']))
	$obFilter->_like('ea.name',$arFilter['name'],VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);

if(!empty($arFilter['clinic']))
	$obFilter->_like('ec.name',$arFilter['clinic'],VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);

if($by=='clinic')
	$obQuery->builder()->sort('ec.name',$order);
else
	$obQuery->builder()->sort('ea.'.$by,$order);

$obQuery->builder()->group('ea.id');
$obRecords=$obQuery->select();
$arRecords=$obRecords->all();

foreach($arRecords as $arRecord){
	$f_ID=$arRecord['id'];
	$row =& $lAdmin->AddRow($f_ID,$arRecord);

	$row->AddViewField("ID",$arRecord['id']);
	$row->AddViewField("CLINIC",$arRecord['clinic']);
	$row->AddViewField("NAME",$arRecord['name']);

	$arActions = Array();
	$arActions[] = array("DEFAULT"=>"Y", "ICON"=>"edit", "TITLE"=>GetMessage("ESTELIFE_EDIT_ALT"), "ACTION"=>$lAdmin->ActionRedirect("estelife_akzii_edit.php?lang=".LANGUAGE_ID."&ID=$f_ID"), "TEXT"=>GetMessage("ESTELIFE_EDIT"));
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
		"LINK"=>"estelife_akzii_edit.php?lang=".LANG,
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
				GetMessage("ESTELIFE_F_CLINIC"),
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
			<td><?echo GetMessage("ESTELIFE_F_CLINIC")?></td>
			<td><input type="text" name="find_clinic" size="47" value="<?echo htmlspecialcharsbx($find_clinic)?>" /></td>
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