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


//===== FILTER ==========
$arFilterFields = Array(
	"find_email",
	"find_active",
	"find_type"
);
$lAdmin->InitFilter($arFilterFields);

InitBVar($find_email_exact_match);
InitBVar($find_active_exact_match);
InitBVar($find_type_exact_match);

$arFilter = Array(
	"email"						=> $find_email,
	"active"					=> $find_active,
	"type"						=> $find_type
);


//====== TABLE HEADERS =========
$headers = array(
	array("id"=>"ID", "content"=>GetMessage("ESTELIFE_F_ID"), "sort"=>"id", "default"=>true),
	array("id"=>"ACTIVE", "content"=>GetMessage("ESTELIFE_F_ACTIVE"), "sort"=>"active", "default"=>true),
	array("id"=>"EMAIL", "content"=>GetMessage("ESTELIFE_F_EMAIL"), "sort"=>"email", "default"=>true),
	array("id"=>"TYPE", "content"=>GetMessage("ESTELIFE_F_TYPE"), "sort"=>"type", "default"=>true),
	array("id"=>"DATE", "content"=>GetMessage("ESTELIFE_F_DATE"), "sort"=>"date", "default"=>true),
);
$lAdmin->AddHeaders($headers);

//==== Здесь надо зафигачить генерацию списка ========
if(($arID = $lAdmin->GroupAction()) && check_bitrix_sessid()){
	foreach($arID as $ID){
		if(($ID = IntVal($ID))>0 && $_REQUEST['action']=='delete'){
			try{
				$obQuery = VDatabase::driver()->createQuery();
				$obQuery->builder()->from('estelife_subscribe')->filter()
					->_eq('id', $ID);
				$obQuery->delete();
			}catch(\core\database\exceptions\VCollectionException $e){}
		}
	}
}

$obQuery=VDatabase::driver()->createQuery();
$obQuery->builder()->from('estelife_subscribe_events', 'se');
$obJoin=$obQuery
	->builder()
	->join();
$obJoin->_left()
	->_from('se','subscribe_user_id')
	->_to('estelife_subscribe_owners','user_id','su');

$obFilter=$obQuery
	->builder()
	->filter();

if($_GET && $_GET['set_filter'] == 'Y'){
	if(!empty($arFilter['email']))
		$obFilter->_like('su.email',$arFilter['email'],VFilter::LIKE_BEFORE|VFilter::LIKE_AFTER);
	if($arFilter['active'] != 'all')
		$obFilter->_eq('se.event_active',$arFilter['active']);
	if($arFilter['type'] != 'all')
		$obFilter->_eq('se.type',$arFilter['type']);
}

if($by=='email')
	$obQuery->builder()->sort('su.email',$order);
elseif($by=='active')
	$obQuery->builder()->sort('se.event_active',$order);
elseif($by=='type')
	$obQuery->builder()->sort('se.type',$order);
elseif($by=='date')
	$obQuery->builder()->sort('su.date_send',$order);
else
	$obQuery->builder()->sort('se.'.$by,$order);

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


	if($arRecord['event_active'] == 1){
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
			GetMessage("ESTELIFE_F_EMAIL"),
			GetMessage("ESTELIFE_F_TYPE"),
			GetMessage("ESTELIFE_F_ACTIVE")
		)
	);
	$oFilter->Begin();
	?>
	<tr>
		<td><?echo GetMessage("ESTELIFE_F_EMAIL")?></td>
		<td><input type="text" name="find_email" size="30" value="<?echo htmlspecialcharsbx($find_email)?>"></td>
	</tr>

	<tr>
		<td><?echo GetMessage("ESTELIFE_F_ACTIVE")?></td>
		<td>
			<select name="find_active" value="<?echo htmlspecialcharsbx($find_active)?>">
				<option value="all"><?echo GetMessage("ESTELIFE_NOT_IMPORTANT")?></option>
						<option value="1">Да</option>
						<option value="0">Нет</option>
			</select>
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("ESTELIFE_F_TYPE")?></td>
		<td>
			<select name="find_type" value="<?echo htmlspecialcharsbx($find_type)?>">
				<option value="all"><?echo GetMessage("ESTELIFE_NOT_IMPORTANT")?></option>
				<? foreach($types as $type_key=>$type){ ?>
					<option value="<?=$type_key;?>"><?=$type;?></option>
				<? } ?>
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
