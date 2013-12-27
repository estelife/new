<?php
use core\database\mysql\VFilter;
use core\database\VDatabase;
use reference\services\VSpecs;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$sTableID = "tbl_estelife_service_concreate_list";
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
	"find_id_exact_match",
	"find_name",
	"find_name_exact_match",
	"find_specialization_id",
	"find_specialization_id_exact_match",
	"find_service_id",
	"find_service_id_exact_match"
);
$lAdmin->InitFilter($arFilterFields);

//====== TABLE HEADERS =========
$headers = array(
	array("id"=>"NAME", "content"=>GetMessage("ESTELIFE_F_TITLE"), "sort"=>"name", "default"=>true),
	array("id"=>"SPECIALIZATION", "content"=>GetMessage("ESTELIFE_F_SPEC"), "sort"=>"specialization", "default"=>true),
	array("id"=>"SERVICE", "content"=>GetMessage("ESTELIFE_F_SERVICE"), "sort"=>"service", "default"=>true),
	array("id"=>"METHOD", "content"=>GetMessage("ESTELIFE_F_METHOD"), "sort"=>"method", "default"=>true),
	array("id"=>"ID", "content"=>GetMessage("ESTELIFE_F_ID"), "sort"=>"id", "default"=>true)
);
$lAdmin->AddHeaders($headers);

$obColl= VDatabase::driver();

$obSpecs=$obColl->createQuery();
$obSpecs->builder()->from('estelife_specializations')->sort('name','asc');
$arSpecs=$obSpecs->select()->all();


//==== Здесь надо зафигачить генерацию списка =======
if(($arID = $lAdmin->GroupAction()) && check_bitrix_sessid()){
	foreach($arID as $ID){
		if(($ID = IntVal($ID))>0 && $_REQUEST['action']=='delete'){
			try{
				$obQuery = $obColl->createQuery();
				$obQuery->builder()->from('estelife_methods')->filter()
					->_eq('id', $ID);
				$obQuery->delete();
			}catch(\core\database\exceptions\VCollectionException $e){}
		}
	}

	LocalRedirect('/bitrix/admin/estelife_service_concreate_list.php?lang='.LANGUAGE_ID);
}

$obQuery=$obColl->createQuery();
$obQuery->builder()->from('estelife_service_concreate','esc');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('esc','service_id')
	->_to('estelife_services','id','esr');
$obJoin->_left()
	->_from('esc','specialization_id')
	->_to('estelife_specializations','id','esp');
$obJoin->_left()
	->_from('esc','method_id')
	->_to('estelife_methods','id','esm');
$obQuery->builder()
	->field('esc.id','id')
	->field('esc.name','name')
	->field('esr.name','service')
	->field('esp.name','specialization')
	->field('esm.name','method');

if($by=='specialization')
	$by='esp.name';
else if($by=='service')
	$by='esr.name';
else if($by=='method')
	$by='esm.name';
else
	$by='esc.'.$by;

$obQuery->builder()->sort($by,$order);
$obFilter=$obQuery->builder()->filter();

if(!empty($find_id))
	$obFilter->_like('esc.id',$find_id,VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);
if(!empty($find_name))
	$obFilter->_like('esc.name',$find_name,VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);
if(!empty($find_specialization_id))
	$obFilter->_eq('esc.specialization_id',$find_specialization_id);
if(!empty($find_service_id))
	$obFilter->_eq('esc.service_id',$find_service_id);
if(!empty($find_method_id))
	$obFilter->_eq('esc.method_id',$find_method_id);

$obResult=$obQuery->select();
$arRecords=$obResult->all();

foreach($arRecords as $arRecord){
	$f_ID=$arRecord['id'];
	$row =& $lAdmin->AddRow($f_ID, $arRecord);

	$row->AddViewField("ID",$arRecord['id']);
	$row->AddViewField("NAME",$arRecord['name']);
	$row->AddViewField('SERVICE',$arRecord['service']);
	$row->AddViewField('SPECIALIZATION',$arRecord['specialization']);
	$row->AddViewField('METHOD',$arRecord['method']);

	$arActions = Array();
	$arActions[] = array("DEFAULT"=>"Y", "ICON"=>"edit", "TITLE"=>GetMessage("ESTELIFE_EDIT_ALT"), "ACTION"=>$lAdmin->ActionRedirect("estelife_service_concreate_edit.php?lang=".LANGUAGE_ID."&ID=$f_ID"), "TEXT"=>GetMessage("ESTELIFE_EDIT"));
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
		"LINK"=>"estelife_service_concreate_edit.php?lang=".LANG,
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
	<script type="text/javascript" src="/bitrix/js/estelife/estelife.js"></script>
	<a name="tb"></a>
	<form name="form1" method="GET" action="<?=$APPLICATION->GetCurPage()?>?">
		<?php
		$oFilter = new CAdminFilter(
			$sTableID."_filter",
			array(
				GetMessage("ESTELIFE_F_ID"),
				GetMessage("ESTELIFE_F_TITLE"),
				GetMessage("ESTELIFE_F_SPEC"),
				GetMessage("ESTELIFE_F_SERVICE")
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
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_SERVICE")?></td>
			<td>
				<select name="find_service_id">
					<option value="0"><?echo GetMessage("ESTELIFE_NOT_IMPORTANT")?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_METHOD")?></td>
			<td>
				<select name="find_method_id">
					<option value="0"><?echo GetMessage("ESTELIFE_NOT_IMPORTANT")?></option>
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