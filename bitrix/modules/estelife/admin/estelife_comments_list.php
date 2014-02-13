<?php
use core\database\mysql\VFilter;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$sTableID = "tbl_estelife_comments_list";
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
	"find_name",
	"find_user_id",
	"find_date_create_to",
	"find_date_create_from",
	"find_moderate",
);
$lAdmin->InitFilter($arFilterFields);
$arFilter = Array(
	"id"=>$find_id,
	"name"=>$find_name,
	"user_id"=> $find_user_id,
	"date_create_to"=>$find_date_create_to,
	"date_create_from"=>$find_date_create_from,
	"moderate"=>$find_moderate
);

//====== TABLE HEADERS =========
$headers = array(
	array("id"=>"id", "content"=>GetMessage("ESTELIFE_F_ID"), "sort"=>"id", "default"=>true),
	array("id"=>"name", "content"=>GetMessage("ESTELIFE_F_NAME"), "sort"=>"name", "default"=>true),
	array("id"=>"user_id", "content"=>GetMessage("ESTELIFE_F_USER_ID"), "sort"=>"user_id", "default"=>true),
	array("id"=>"moderate", "content"=>GetMessage("ESTELIFE_F_MODERATE"),"sort"=>"moderate","default"=>true),
	array("id"=>"active", "content"=>GetMessage("ESTELIFE_F_ACTIVE"),"sort"=>"active","default"=>true),
	array("id"=>"date_create", "content"=>GetMessage("ESTELIFE_F_DATE_CREATE"),"sort"=>"date_create","default"=>true),
	array("id"=>"text", "content"=>GetMessage("ESTELIFE_F_TEXT"),"sort"=>"text","default"=>true),

);
$lAdmin->AddHeaders($headers);

//==== Здесь надо зафигачить генерацию списка ========
$obComment=\core\database\VDatabase::driver();

if(($arID = $lAdmin->GroupAction()) && check_bitrix_sessid()){
	foreach($arID as $ID){
		if(($ID = IntVal($ID))>0 && $_REQUEST['action']=='delete'){
			$obQuery=$obComment->createQuery();
			$obQuery->builder()
				->from('estelife_comments')
				->filter()
				->_eq('id', $ID);
			$obQuery->delete();
		}

		if(($ID = IntVal($ID))>0 && $_REQUEST['action']=='moderate'){
			$obQuery=$obComment->createQuery();
			$obQuery->builder()
				->from('estelife_comments')
				->value('moderate', 1)
				->filter()
				->_eq('id', $ID);
			$obQuery->update();
		}

		if(($ID = IntVal($ID))>0 && $_REQUEST['action']=='active'){
			$obQuery=$obComment->createQuery();
			$obQuery->builder()
				->from('estelife_comments')
				->value('active', 1)
				->filter()
				->_eq('id', $ID);
			$obQuery->update();
		}

		if(($ID = IntVal($ID))>0 && $_REQUEST['action']=='deactive'){
			$obQuery=$obComment->createQuery();
			$obQuery->builder()
				->from('estelife_comments')
				->value('active', 0)
				->filter()
				->_eq('id', $ID);
			$obQuery->update();
		}
	}
}

$obQuery=$obComment->createQuery();
$obJoin=$obQuery->builder()
	->from('estelife_comments');
if($by=='active'){
	$obQuery->builder()->sort('active',$order);
}else if($by=='moderate')
	$obQuery->builder()->sort('moderate',$order);
else if($by=='user_id')
	$obQuery->builder()->sort('user_id',$order);
else if($by=='name')
	$obQuery->builder()->sort('name',$order);
else if($by=='id')
	$obQuery->builder()->sort('id',$order);
else if($by=='text')
	$obQuery->builder()->sort('text',$order);
else if($by=='date_create')
	$obQuery->builder()->sort('date_create',$order);

$obFilter=$obQuery->builder()
	->sort('moderate','desc')
	->sort('date_create','desc')
	->filter();

if(!empty($arFilter['id']))
	$obFilter->_eq('id',$arFilter['id']);

if(!empty($arFilter['name']))
	$obFilter->_like('name',$arFilter['name'],VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);

if(strlen($arFilter['moderate'])>0)
	$obFilter->_eq('moderate',$arFilter['moderate']);

if(!empty($arFilter['user_id']))
	$obFilter->_eq('user_id',$arFilter['user_id']);

if(!empty($arFilter['date_create_to']))
	$obFilter->_gte('date_create',date('Y-m-d H:i:s', strtotime($arFilter['date_create_to'])));

if(!empty($arFilter['date_create_from']))
	$obFilter->_lte('date_create',date('Y-m-d H:i:s', strtotime($arFilter['date_create_from'])));

$obResult=$obQuery->select();
$obResult=new CAdminResult(
	$obResult->bxResult(),
	$sTableID
);
$obResult->NavStart();
$lAdmin->NavText($obResult->GetNavPrint(GetMessage('ESTELIFE_PAGES')));

while($arRecord=$obResult->Fetch()){
	$f_ID=$arRecord['id'];
	$row =& $lAdmin->AddRow($f_ID, $arRecord);

	$row->AddViewField("id",$arRecord['id']);
	$row->AddViewField("name",$arRecord['name']);
	if ($arRecord['user_id']<=0)
		$arRecord['user_id']='Гость';
	$row->AddViewField("user_id",$arRecord['user_id']);
	$arRecord['moderate']=($arRecord['moderate']>0) ? 'Да' : '<span class="moderate">Нет</span>';
	$row->AddViewField("moderate",$arRecord['moderate']);
	$nActive=$arRecord['active'];
	$arRecord['active']=($arRecord['active']>0) ? 'Да' : 'Нет';
	$row->AddViewField("active",$arRecord['active']);
	$row->AddViewField("date_create",$arRecord['date_create']);
	$row->AddViewField("text",$arRecord['text']);


	$arActions = Array();
	$arActions[] = array("ICON"=>"delete", "TITLE"=>GetMessage("ESTELIFE_DELETE_ALT"),"ACTION"=>"javascript:if(confirm('".GetMessage("ESTELIFE_CONFIRM_DELETE")."')) window.location='?lang=".LANGUAGE_ID."&action=delete&ID=$f_ID&".bitrix_sessid_get()."'","TEXT"=>GetMessage("ESTELIFE_DELETE"));
	$arActions[] = array("ICON"=>"edit", "TITLE"=>GetMessage("ESTELIFE_MODERATE_ALT"),"ACTION"=>"javascript:if(confirm('".GetMessage("ESTELIFE_CONFIRM_MODERATE")."')) window.location='?lang=".LANGUAGE_ID."&action=moderate&ID=$f_ID&".bitrix_sessid_get()."'","TEXT"=>GetMessage("ESTELIFE_MODERATE"));
	if ($nActive>0){
		$arActions[] = array(
			"ICON"=>"setting",
			"TITLE"=>GetMessage("ESTELIFE_DEACTIVE_ALT"),
			"ACTION"=>"javascript:if(confirm('".GetMessage("ESTELIFE_CONFIRM_DEACTIVE")."')) window.location='?lang=".LANGUAGE_ID."&action=deactive&ID=$f_ID&".bitrix_sessid_get()."'",
			"TEXT"=>GetMessage("ESTELIFE_DEACTIVE")
		);
	}else{
			$arActions[] = array(
				"ICON"=>"setting",
				"TITLE"=>GetMessage("ESTELIFE_ACTIVE_ALT"),
				"ACTION"=>"javascript:if(confirm('".GetMessage("ESTELIFE_CONFIRM_ACTIVE")."')) window.location='?lang=".LANGUAGE_ID."&action=active&ID=$f_ID&".bitrix_sessid_get()."'","TEXT"=>GetMessage("ESTELIFE_ACTIVE")
			);
	}
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
				GetMessage("ESTELIFE_F_ID"),
				GetMessage("ESTELIFE_F_NAME"),
				GetMessage("ESTELIFE_F_USER_ID"),
				GetMessage("ESTELIFE_F_DATE_CREATE"),
				GetMessage("ESTELIFE_F_MODERATE")
			)
		);
		$oFilter->Begin();
		?>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_ID")?></td>
			<td><input type="text" name="find_id" size="47" value="<?echo htmlspecialcharsbx($find_id)?>"></td>
		</tr>
		<tr>
			<td><b><?echo GetMessage("ESTELIFE_F_NAME")?></b></td>
			<td><input type="text" name="find_name" size="47" value="<?echo htmlspecialcharsbx($find_name)?>"></td>
		</tr>
		<tr>
			<td><b><?echo GetMessage("ESTELIFE_F_USER_ID")?></b></td>
			<td><input type="text" name="find_user_id" size="47" value="<?echo htmlspecialcharsbx($find_user_id)?>"></td>
		</tr>

		<tr>
			<td><?echo GetMessage("ESTELIFE_F_DATE_CREATE")?></td>
			<td>
				<?echo CalendarPeriod("find_date_create_to", "", "find_date_create_from", "", "form1", "N")?>
			</td>
		</tr>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_MODERATE")?></td>
			<td>
				<select name="find_moderate">
					<option value=""><?echo GetMessage("ESTELIFE_NOT_IMPORTANT")?></option>
					<option value="1"<?=($find_moderate==1 ? ' selected="true"' : '')?>>Да</option>
					<option value="0"<?=($find_moderate==0 ? ' selected="true"' : '')?>>Нет</option>
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