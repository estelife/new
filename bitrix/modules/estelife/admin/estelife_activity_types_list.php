<?php
use core\database\mysql\VFilter;
use core\database\VDatabase;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$sTableID = "tbl_estelife_activity_types";
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
	"find_name",
	"find_id",
);
$lAdmin->InitFilter($arFilterFields);

InitBVar($find_name_exact_match);
InitBVar($find_id_exact_match);

$arFilter = Array(
	"name"	=> $find_name,
	"id"	=> $find_id,
);

//====== TABLE HEADERS =========
$headers = array(
	array("id"=>"name", "content"=>GetMessage("ESTELIFE_F_NAME"),"sort"=>"name","default"=>true),
	array("id"=>"id", "content"=>GetMessage("ESTELIFE_F_ID"), "sort"=>"id", "default"=>true),
);

$lAdmin->AddHeaders($headers);

//==== Здесь надо зафигачить генерацию списка ========
if(($arID = $lAdmin->GroupAction()) && check_bitrix_sessid()){
	foreach($arID as $ID){
		if(($ID = IntVal($ID))>0 && $_REQUEST['action']=='delete'){
			try{
				$obQuery = VDatabase::driver()->createQuery();
				$obQuery->builder()->from('estelife_activity_types')->filter()
					->_eq('id', $ID);
				$obQuery->delete();
			}catch(\core\database\exceptions\VCollectionException $e){}
		}
	}
}

$obQuery=VDatabase::driver()->createQuery();
$obQuery->builder()
	->from('estelife_activity_types');

$obFilter=$obQuery->builder()->filter();


if($_GET && $_GET['set_filter'] == 'Y'){
	if(!empty($arFilter['name']))
		$obFilter->_like('name',$arFilter['name'],VFilter::LIKE_BEFORE|VFilter::LIKE_AFTER);
	if(!empty($arFilter['id']))
		$obFilter->_eq('id',$arFilter['id']);
}

if($by=='name')
	$obQuery->builder()->sort('name',$order);
elseif($by=='id')
	$obQuery->builder()->sort('id',$order);
else
	$obQuery->builder()->sort($by,$order);


$obResult=$obQuery->select();

while($arRecord=$obResult->Fetch()){
	$f_ID=$arRecord['id'];
	$row =& $lAdmin->AddRow($f_ID,$arRecord);
	$sName = $arRecord['name'];

	$row->AddViewField("ID",$arRecord['id']);
	$row->AddViewField("NAME", $arRecord['name']);

	$arActions = Array();
	$arActions[] = array("DEFAULT"=>"Y", "ICON"=>"edit", "TITLE"=>GetMessage("ESTELIFE_EDIT_ALT"), "ACTION"=>$lAdmin->ActionRedirect("estelife_activity_types_edit.php?lang=".LANGUAGE_ID."&ID=$f_ID"), "TEXT"=>GetMessage("ESTELIFE_EDIT"));
	$arActions[] = array("ICON"=>"delete", "TITLE"=>GetMessage("ESTELIFE_DELETE_ALT"),"ACTION"=>"javascript:if(confirm('".GetMessage("ESTELIFE_CONFIRM_DELETE")."')) window.location='?lang=".LANGUAGE_ID."&action=delete&ID=$f_ID&".bitrix_sessid_get()."'","TEXT"=>GetMessage("ESTELIFE_DELETE"));
	$row->AddActions($arActions);

}

$lAdmin->AddFooter(
	array(
		array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>1),
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
	"LINK"=>"estelife_activity_types_edit.php?lang=".LANG,
	"ICON" => "btn_new"
);

$aContext = $aMenu;
$lAdmin->AddAdminContextMenu($aContext);

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("ESTELIFE_HEAD_TITLE"));
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
	<script type="text/javascript" src="/bitrix/js/estelife/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/jquery-ui-1.10.3.custom.min.js"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/estelife.js"></script>

	<a name="tb"></a>
	<form name="form1" method="GET" action="<?=$APPLICATION->GetCurPage()?>?">
		<?php
		$oFilter = new CAdminFilter(
			$sTableID."_filter",
			array(
				GetMessage("ESTELIFE_F_NAME"),
				GetMessage("ESTELIFE_F_ID"),
			)
		);
		$oFilter->Begin();
		?>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_NAME")?></td>
			<td><input type="text" name="find_name" size="30" value="<?echo htmlspecialcharsbx($find_name)?>"></td>
		</tr>

		<tr>
			<td><?echo GetMessage("ESTELIFE_F_ID")?></td>
			<td><input type="text" name="find_id" size="30" value="<?echo htmlspecialcharsbx($find_id)?>"></td>
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