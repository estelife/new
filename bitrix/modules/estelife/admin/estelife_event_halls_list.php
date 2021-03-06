<?php
use core\database\mysql\VFilter;
use core\database\VDatabase;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$sTableID = "tbl_estelife_event_halls";
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
	"find_event_name",
);
$lAdmin->InitFilter($arFilterFields);

InitBVar($find_name_exact_match);
InitBVar($find_id_exact_match);
InitBVar($find_event_exact_match);

$arFilter = Array(
	"name"						=> $find_name,
	"id"						=> $find_id,
	"event"						=> $find_event_name,
);


//====== TABLE HEADERS =========
$headers = array(
	array("id"=>"id", "content"=>GetMessage("ESTELIFE_F_ID"), "sort"=>"id", "default"=>true),
	array("id"=>"name", "content"=>GetMessage("ESTELIFE_F_NAME"),"sort"=>"name","default"=>true),
	array("id"=>"event_name", "content"=>GetMessage("ESTELIFE_F_EVENT_NAME"), "sort"=>"event_name", "default"=>true),
);
$lAdmin->AddHeaders($headers);


//==== Здесь надо зафигачить генерацию списка ========
if(($arID = $lAdmin->GroupAction()) && check_bitrix_sessid()){
	foreach($arID as $ID){
		if(($ID = IntVal($ID))>0 && $_REQUEST['action']=='delete'){
			try{
				$obQuery = VDatabase::driver()->createQuery();
				$obQuery->builder()->from('estelife_event_halls')->filter()
					->_eq('id', $ID);
				$obQuery->delete();
			}catch(\core\database\exceptions\VCollectionException $e){}
		}
	}
}


$obQuery=VDatabase::driver()->createQuery();
$obJoin=$obQuery->builder()
	->from('estelife_event_halls','eh')
	->join();
$obJoin->_left()
	->_from('eh','event_id')
	->_to('estelife_events','id','ee');

$obFilter=$obQuery->builder()
	->sort('eh.'.$by,$order)
	->field('eh.id','id')
	->field('eh.name','name')
	->field('ee.short_name','event_name')
	->field('ee.id', 'event_id')
	->filter();


if(!empty($arFilter['id']))
	$obFilter->_eq('eh.id',$arFilter['id']);
if(!empty($arFilter['name']))
	$obFilter->_like('eh.name',$arFilter['name'],VFilter::LIKE_BEFORE|VFilter::LIKE_AFTER);
if(!empty($arFilter['event'])){
	$obFilter->_like('ee.short_name',$arFilter['event'],VFilter::LIKE_BEFORE|VFilter::LIKE_AFTER);
}

if($by=='id'){
	$obQuery->builder()->sort('eh.id',$order);
}else if($by=='name'){
	$obQuery->builder()->sort('eh.name',$order);
}else if($by == 'event_name'){
	$obQuery->builder()->sort('ee.short_name',$order);
}


$obQuery->builder()->group('eh.id');
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
	$sName = $arRecord['name'];
	$sEventName = $arRecord['event_name'];

	$row->AddViewField("ID",$arRecord['id']);
	$row->AddViewField("NAME", $arRecord['name']);
	$row->AddViewField("EVENT_NAME", $arRecord['event_name']);

	$arActions = Array();
	$arActions[] = array("DEFAULT"=>"Y", "ICON"=>"edit", "TITLE"=>GetMessage("ESTELIFE_EDIT_ALT"), "ACTION"=>$lAdmin->ActionRedirect("estelife_event_halls_edit.php?lang=".LANGUAGE_ID."&ID=$f_ID"), "TEXT"=>GetMessage("ESTELIFE_EDIT"));
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
	"LINK"=>"estelife_event_halls_edit.php?lang=".LANG,
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
				GetMessage("ESTELIFE_F_EVENT"),
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
		<tr>
			<td><b><?echo GetMessage("ESTELIFE_F_EVENT")?></b></td>
			<td>
				<!--<input type="hidden" name="find_company_id" value="<?/*echo htmlspecialcharsbx($find_company_id)*/?>" />-->
				<input type="text" name="find_event_name" data-input="find_event_id" size="47" value="<?echo htmlspecialcharsbx($find_event_name)?>"><?=InputType("checkbox", "find_event_exact_match", "Y", $find_event_exact_match, false, "", "title='".GetMessage("ESTELIFE_EXACT_MATCH")."'")?>&nbsp;<?=ShowFilterLogicHelp()?>
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