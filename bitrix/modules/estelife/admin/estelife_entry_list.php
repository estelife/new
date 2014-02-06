<?php
use core\database\mysql\VFilter;
use core\database\VDatabase;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$sTableID = "tbl_estelife_entry_list";
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
	"find_email",
	"find_phone"
);
$lAdmin->InitFilter($arFilterFields);

InitBVar($find_name_exact_match);
InitBVar($find_email_exact_match);
InitBVar($find_phone_exact_match);

$arFilter = Array(
	"name"						=> $find_name,
	"email"						=> $find_email,
	"phone"						=> $find_phone
);


//====== TABLE HEADERS =========
$headers = array(
	array("id"=>"ID", "content"=>GetMessage("ESTELIFE_F_ID"), "sort"=>"id", "default"=>true),
	array("id"=>"NAME", "content"=>GetMessage("ESTELIFE_F_NAME"), "sort"=>"name", "default"=>true),
	array("id"=>"EMAIL", "content"=>GetMessage("ESTELIFE_F_EMAIL"), "sort"=>"email", "default"=>true),
	array("id"=>"PHONE", "content"=>GetMessage("ESTELIFE_F_PHONE"), "sort"=>"phone", "default"=>true),
	array("id"=>"DATE", "content"=>GetMessage("ESTELIFE_F_DATE"), "sort"=>"date", "default"=>true),
);

$lAdmin->AddHeaders($headers);


//==== Здесь надо зафигачить генерацию списка ========
if(($arID = $lAdmin->GroupAction()) && check_bitrix_sessid()){
	foreach($arID as $ID){
		if(($ID = IntVal($ID))>0 && $_REQUEST['action']=='delete'){
			try{
				$obQuery = VDatabase::driver()->createQuery();
				$obQuery->builder()->from('estelife_entry')->filter()
					->_eq('id', $ID);
				$obQuery->delete();
			}catch(\core\database\exceptions\VCollectionException $e){}
		}
	}
}


$obQuery=VDatabase::driver()->createQuery();
$obQuery->builder()
	->from('estelife_entry');

$obFilter=$obQuery->builder()->filter();

if($_GET && $_GET['set_filter'] == 'Y'){

	if(!empty($arFilter['name']))
		$obFilter->_like('name',$arFilter['name'],VFilter::LIKE_BEFORE|VFilter::LIKE_AFTER);
	if(!empty($arFilter['email']))
		$obFilter->_like('email',$arFilter['email'],VFilter::LIKE_BEFORE|VFilter::LIKE_AFTER);
	if(!empty($arFilter['phone']))
		$obFilter->_like('phone',$arFilter['phone'],VFilter::LIKE_BEFORE|VFilter::LIKE_AFTER);
}


if($by=='name')
	$obQuery->builder()->sort('name',$order);
elseif($by=='email')
	$obQuery->builder()->sort('email',$order);
elseif($by=='phone')
	$obQuery->builder()->sort('phone',$order);
elseif($by=='date')
	$obQuery->builder()->sort('date',$order);
else
	$obQuery->builder()->sort($by,$order);





$obResult=$obQuery->select();
$obResult=new CAdminResult(
	$obResult->bxResult(),
	$sTableID
);



$obResult->NavStart();
$lAdmin->NavText($obResult->GetNavPrint(GetMessage('ESTELIFE_PAGES')));


while($arRecord=$obResult->Fetch()){
	$f_ID=$arRecord['id'];
	$row =& $lAdmin->AddRow($f_ID,$arRecord);

	$date = date('d-m-Y, h:i',$arRecord['date']);


	$row->AddViewField("ID",$arRecord['id']);
	$row->AddViewField("NAME", $arRecord['name']);
	$row->AddViewField("EMAIL", $arRecord['email']);
	$row->AddViewField("PHONE", $arRecord['phone']);
	$row->AddViewField("DATE", $date);

	$arActions = Array();
	$arActions[] = array("DEFAULT"=>"Y", "ICON"=>"edit", "TITLE"=>GetMessage("ESTELIFE_EDIT_ALT"), "ACTION"=>$lAdmin->ActionRedirect("estelife_entry_edit.php?lang=".LANGUAGE_ID."&ID=$f_ID"), "TEXT"=>GetMessage("ESTELIFE_EDIT"));
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
				GetMessage("ESTELIFE_F_EMAIL"),
				GetMessage("ESTELIFE_F_PHONE"),
			)
		);
		$oFilter->Begin();
		?>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_NAME")?></td>
			<td><input type="text" name="find_name" size="30" value="<?echo htmlspecialcharsbx($find_name)?>"></td>
		</tr>

		<tr>
			<td><?echo GetMessage("ESTELIFE_F_EMAIL")?></td>
			<td><input type="text" name="find_email" size="30" value="<?echo htmlspecialcharsbx($find_email)?>"></td>
		</tr>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_PHONE")?></td>
			<td><input type="text" name="find_phone" size="30" value="<?echo htmlspecialcharsbx($find_phone)?>"></td>
		</tr>

		<?
		$oFilter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage()));
		$oFilter->End();
		#############################################################
		?>
	</form>

<?


$lAdmin->DisplayList();