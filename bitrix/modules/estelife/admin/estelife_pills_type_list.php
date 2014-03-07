<?php


use core\database\mysql\VFilter;
use core\database\VDatabase;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$sTableID = "tbl_estelife_pills_typename_list";
$oSort = new CAdminSorting($sTableID, "id", "asc");
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
	"find_name"
);
$lAdmin->InitFilter($arFilterFields);

InitBVar($find_id_exact_match);
InitBVar($find_name_exact_match);
$arFilter = Array(
	"id"		=> $find_id,
	"name"		=> $find_name,
);

//====== TABLE HEADERS =========
$headers = array(
	array("id"=>"ID", "content"=>GetMessage("ESTELIFE_F_ID"), "sort"=>"id", "default"=>true),
	array("id"=>"NAME", "content"=>GetMessage("ESTELIFE_F_TITLE"), "sort"=>"name", "default"=>true),
	array("id"=>"TYPE", "content"=>GetMessage("ESTELIFE_F_TYPE"), "sort"=>"type", "default"=>true),
);
$lAdmin->AddHeaders($headers);

$obApp= VDatabase::driver();

//==== Здесь надо зафигачить генерацию списка ========
if(($arID = $lAdmin->GroupAction()) && check_bitrix_sessid()){
	foreach($arID as $ID){
		if(($ID = IntVal($ID))>0 && $_REQUEST['action']=='delete'){
			try{
				$obQuery = $obApp->createQuery();
				$obQuery->builder()->from('estelife_pills_typename')->filter()
					->_eq('id', $ID);
				$obQuery->delete();
			}catch(\core\database\exceptions\VCollectionException $e){}
		}
	}
}

$obQuery=$obApp->createQuery();
$obQuery->builder()->from('estelife_pills_typename');
$obFilter=$obQuery->builder()->filter();

if(!empty($arFilter['id']))
	$obFilter->_eq('id',$arFilter['id']);
if(!empty($arFilter['name']))
	$obFilter->_like('name',$arFilter['name'],VFilter::LIKE_BEFORE|VFilter::LIKE_AFTER);

$obQuery->builder()->sort($by,$order);
$obResult=$obQuery->select();

$obResult=new CAdminResult(
	$obResult->bxResult(),
	$sTableID
);
$obResult->NavStart();
$lAdmin->NavText($obResult->GetNavPrint(GetMessage('ESTELIFE_PAGES')));

while($arRecord=$obResult->GetNext()){
	$f_ID=$arRecord['id'];
	$row =& $lAdmin->AddRow($f_ID,$arRecord);

	$row->AddViewField("ID",$arRecord['id']);
	$row->AddViewField("NAME",$arRecord['name']);

	if ($arRecord['type']==1){
		$row->AddViewField("TYPE",'Препараты');
	}elseif($arRecord['type']==2){
		$row->AddViewField("TYPE",'Нити');
	}elseif($arRecord['type']==3){
		$row->AddViewField("TYPE",'Имплантаты');
	}

	$arActions = Array();
	$arActions[] = array(
		"DEFAULT"=>"Y",
		"ICON"=>"edit",
		"TITLE"=>GetMessage("ESTELIFE_EDIT_ALT"),
		"ACTION"=>$lAdmin->ActionRedirect("estelife_pills_type_edit.php?lang=".LANGUAGE_ID."&ID=$f_ID"),
		"TEXT"=>GetMessage("ESTELIFE_EDIT")
	);
	$arActions[] = array(
		"ICON"=>"delete",
		"TITLE"=>GetMessage("ESTELIFE_DELETE_ALT"),
		"ACTION"=>"javascript:if(confirm('".GetMessage("ESTELIFE_CONFIRM_DELETE")."')) window.location='?lang=".LANGUAGE_ID."&action=delete&ID=$f_ID&".bitrix_sessid_get()."'",
		"TEXT"=>GetMessage("ESTELIFE_DELETE"));
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
$aMenu = array();
$aMenu[] = array(
	"TEXT"	=>GetMessage("ESTELIFE_CREATE"),
	"TITLE"=>GetMessage("ESTELIFE_CREATE_TITLE"),
	"LINK"=>"estelife_pills_type_edit.php?lang=".LANG,
	"ICON" => "btn_new"
);

$aContext = $aMenu;
$lAdmin->AddAdminContextMenu($aContext);

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
			)
		);
		$oFilter->Begin();
		?>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_ID")?></td>
			<td><input type="text" name="find_id" size="47" value="<?echo htmlspecialcharsbx($find_id)?>"><?=InputType("checkbox", "find_id_exact_match", "Y", $find_id_exact_match, false, "", "title='".GetMessage("ESTELIFE_EXACT_MATCH")."'")?>&nbsp;<?=ShowFilterLogicHelp()?></td>
		</tr>
		<tr>
			<td><b><?echo GetMessage("ESTELIFE_F_TITLE")?></b></td>
			<td><input type="text" name="find_name" size="47" value="<?echo htmlspecialcharsbx($find_name)?>"><?=InputType("checkbox", "find_name_exact_match", "Y", $find_name_exact_match, false, "", "title='".GetMessage("ESTELIFE_EXACT_MATCH")."'")?>&nbsp;<?=ShowFilterLogicHelp()?></td>
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