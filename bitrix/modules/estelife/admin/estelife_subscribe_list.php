<?php
use core\database\mysql\VFilter;
use core\database\VDatabase;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$sTableID = "tbl_estelife_subscribe_list";
$oSort = new CAdminSorting($sTableID, "id", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/estelife/prolog.php");

ClearVars();
$FORM_RIGHT = $APPLICATION->GetGroupRight("estelife");

if($FORM_RIGHT<="D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

CModule::IncludeModule("estelife");
IncludeModuleLangFile(__FILE__);


//====== TABLE HEADERS =========
$headers = array(
	array("id"=>"ID", "content"=>GetMessage("ESTELIFE_F_ID"), "sort"=>"id", "default"=>true),
	array("id"=>"ACTIVE", "content"=>GetMessage("ESTELIFE_F_ACTIVE"), "sort"=>"active", "default"=>true),
	array("id"=>"EMAIL", "content"=>GetMessage("ESTELIFE_F_EMAIL"), "sort"=>"email", "default"=>true),
	array("id"=>"TYPE", "content"=>GetMessage("ESTELIFE_F_TYPE"), "sort"=>"type", "default"=>true),
	array("id"=>"DATE", "content"=>GetMessage("ESTELIFE_F_DATE"), "sort"=>"date", "default"=>true),
);
$lAdmin->AddHeaders($headers);

$obQuery=VDatabase::driver()->createQuery();
$obQuery->builder()
	->from('estelife_subscribe');

if($by=='email')
	$obQuery->builder()->sort('email',$order);
elseif($by=='active')
	$obQuery->builder()->sort('active',$order);
elseif($by=='type')
	$obQuery->builder()->sort('type',$order);
elseif($by=='date')
	$obQuery->builder()->sort('date_send',$order);
else
	$obQuery->builder()->sort($by,$order);

$obResult=$obQuery->select();
$obResult=new CAdminResult(
	$obResult->bxResult(),
	$sTableID
);

$obResult->NavStart();
$lAdmin->NavText($obResult->GetNavPrint(GetMessage('ESTELIFE_PAGES')));

$types = array(
	1=>'Клиники',
	2=>'Учебные центры'
);

while($arRecord=$obResult->Fetch()){
	$f_ID=$arRecord['id'];
	$row =& $lAdmin->AddRow($f_ID,$arRecord);

	if($arRecord['active'] == 1){
		$active = "Да";
	}else{
		$active = "Нет";
	}

	$date_send = date('d-m-Y, h:i',$arRecord['date_send']);


	$row->AddViewField("ID",$arRecord['id']);
	$row->AddViewField("ACTIVE", $active);
	$row->AddViewField("EMAIL", $arRecord['email']);
	$row->AddViewField("TYPE", $types[$arRecord['type']]);
	$row->AddViewField("DATE", $date_send);

	$arActions = Array();
	$arActions[] = array("DEFAULT"=>"Y", "ICON"=>"edit", "TITLE"=>GetMessage("ESTELIFE_EDIT_ALT"), "ACTION"=>$lAdmin->ActionRedirect("estelife_subscribe_edit.php?lang=".LANGUAGE_ID."&ID=$f_ID"), "TEXT"=>GetMessage("ESTELIFE_EDIT"));
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
	"LINK"=>"estelife_subscribe_edit.php?lang=".LANG,
	"ICON" => "btn_new"
);

/*$aContext = $aMenu;
$lAdmin->AddAdminContextMenu($aContext);*/
//}

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("ESTELIFE_HEAD_TITLE"));


require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$lAdmin->DisplayList();
