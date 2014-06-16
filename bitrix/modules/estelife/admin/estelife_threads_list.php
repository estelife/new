<?php
use core\database\mysql\VFilter;
use core\database\VDatabase;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$sTableID = "tbl_estelife_threads_list";
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
	"find_id_exact_match",
	"find_name",
	"find_name_exact_match",
	"find_company_id"
);
$lAdmin->InitFilter($arFilterFields);

InitBVar($find_id_exact_match);
InitBVar($find_name_exact_match);
InitBVar($find_company_exact_match);
$arFilter = Array(
	"id"		=> $find_id,
	"name"		=> $find_name,
	"company"		=> $find_company_id,
);

//====== TABLE HEADERS =========
$headers = array(
	array("id"=>"ID", "content"=>GetMessage("ESTELIFE_F_ID"), "sort"=>"id", "default"=>true),
	array("id"=>"NAME", "content"=>GetMessage("ESTELIFE_F_TITLE"), "sort"=>"name", "default"=>true),
	array("id"=>"COMPANY", "content"=>GetMessage("ESTELIFE_F_COMPANY"), "sort"=>"company_name", "default"=>true),
);
$lAdmin->AddHeaders($headers);

$obPills= VDatabase::driver();

//==== Здесь надо зафигачить генерацию списка ========
if(($arID = $lAdmin->GroupAction()) && check_bitrix_sessid()){
	foreach($arID as $ID){
		if(($ID = IntVal($ID))>0 && $_REQUEST['action']=='delete'){
			try{
				$obQuery = $obPills->createQuery();
				$obQuery->builder()->from('estelife_threads')->filter()
					->_eq('id', $ID);
				$obQuery->delete();
			}catch(\core\database\exceptions\VCollectionException $e){}
		}
	}
}

$obQuery=$obPills->createQuery();
$obQuery->builder()->from('estelife_threads','ep');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ep', 'company_id')
	->_to('estelife_companies', 'id', 'ec');
$obJoin->_left()
	->_from('ec', 'id')
	->_to('estelife_company_types', 'company_id', 'ect')
	->_cond()->_eq('ect.type', 3);
$obQuery->builder()
	->field('ec.id','company_id')
	->field('ec.name','company_name')
	->field('ect.id','company_type_id')
	->field('ect.name','company_type_name')
	->field('ep.id','id')
	->field('ep.name','name');
$obFilter=$obQuery->builder()->filter();

if(!empty($arFilter['id']))
	$obFilter->_like('ep.id',$arFilter['id'],VFilter::LIKE_BEFORE|VFilter::LIKE_AFTER);
if(!empty($arFilter['name']))
	$obFilter->_like('ep.name',$arFilter['name'],VFilter::LIKE_BEFORE|VFilter::LIKE_AFTER);
if(!empty($arFilter['company']))
	$obFilter->_like('ep.company_id',$arFilter['company'],VFilter::LIKE_BEFORE|VFilter::LIKE_AFTER);

$obQuery->builder()->sort('ep.'.$by,$order);

$obQuery->builder()->group('ep.id');
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
	$row->AddViewField("NAME", html_entity_decode($arRecord['name'],ENT_QUOTES,'utf-8'));

	if (!empty($arRecord['company_type_name'])){
		$row->AddViewField("COMPANY",$arRecord['company_type_name']);
	}else{
		$row->AddViewField("COMPANY",$arRecord['company_name']);
	}

	$arActions = Array();
	$arActions[] = array(
		"DEFAULT"=>"Y",
		"ICON"=>"edit",
		"TITLE"=>GetMessage("ESTELIFE_EDIT_ALT"),
		"ACTION"=>$lAdmin->ActionRedirect("estelife_threads_edit.php?lang=".LANGUAGE_ID."&ID=$f_ID"),
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
//if ($FORM_RIGHT=="W")
//{
$aMenu = array();
$aMenu[] = array(
	"TEXT"	=>GetMessage("ESTELIFE_CREATE"),
	"TITLE"=>GetMessage("ESTELIFE_CREATE_TITLE"),
	"LINK"=>"estelife_threads_edit.php?lang=".LANG,
	"ICON" => "btn_new"
);

$aContext = $aMenu;
$lAdmin->AddAdminContextMenu($aContext);
//}

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
				GetMessage("ESTELIFE_F_ID"),
				GetMessage("ESTELIFE_F_TITLE"),
				GetMessage("ESTELIFE_F_COMPANY"),
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
		<tr>
			<td><b><?echo GetMessage("ESTELIFE_F_COMPANY")?></b></td>
			<td>
				<input type="hidden" name="find_company_id" value="<?echo htmlspecialcharsbx($find_company_id)?>" />
				<input type="text" name="find_company_name" data-input="find_company_id" size="47" value="<?echo htmlspecialcharsbx($find_company_name)?>"><?=InputType("checkbox", "find_company_exact_match", "Y", $find_company_exact_match, false, "", "title='".GetMessage("ESTELIFE_EXACT_MATCH")."'")?>&nbsp;<?=ShowFilterLogicHelp()?>
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