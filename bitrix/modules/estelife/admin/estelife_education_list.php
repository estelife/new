<?php
use core\database\mysql\VFilter;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
global $APPLICATION;

$sTableID = "tbl_estelife_education_list";
$oSort = new CAdminSorting($sTableID, "id", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

ClearVars();
$sModuleRight = $APPLICATION->GetGroupRight("estelife");

if($sModuleRight<="D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

CModule::IncludeModule("estelife");
IncludeModuleLangFile(__FILE__);
$lAdmin->InitFilter(array(
	'find_id',
	'find_name',
	'find_date_from',
	'find_date_to'
));
$lAdmin->AddHeaders(array(
	array("id"=>"NAME", "content"=>GetMessage("ESTELIFE_F_NAME"), "sort"=>"name", "default"=>true),
	array("id"=>"DATE", "content"=>GetMessage("ESTELIFE_F_DATE"), "sort"=>"date", "default"=>true),
	array("id"=>"ID", "content"=>GetMessage("ESTELIFE_F_ID"), "sort"=>"id", "default"=>true)
));

$obQuery = \core\database\VDatabase::driver()
	->createQuery();

if(($arID = $lAdmin->GroupAction()) && check_bitrix_sessid()){
	$arDeleted = array();

	foreach($arID as $ID){
		if(($ID = IntVal($ID))>0 && $_REQUEST['action']=='delete')
			$arDeleted[] = $ID;
	}

	if(!empty($arDeleted)){
		$obQuery->builder()
			->from('estelife_education')
			->filter()
			->_in('id', $arDeleted);
		$obQuery->delete();
	}
}

$obJoin = $obQuery->builder()
	->from('estelife_education','education')
	->field('education.id','id')
	->field('education.name','name')
	->field('education.date','date')
	->field('pay.id','pay_id')
	->field(
		$obQuery->builder()->_count('pay.id'),
		'pay_count'
	)
	->group('education.id')
	->sort('education.'.$by, $order)
	->join();

$obJoinFilter = $obJoin->_left()
	->_from('education','id')
	->_to('estelife_pay_receipts','service_id','pay')
	->_cond();
$obJoinFilter->_or()
	->_eq('pay.status',3);
$obJoinFilter->_or()
	->_isNull('pay.status');

$obFilter = $obQuery->builder()->filter();

if(!empty($find_id))
	$obFilter->_eq('education.id',$find_id);

if(!empty($find_name))
	$obFilter->_like('education.name',$find_name,VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);

if(!empty($find_date_from)){
	$sDateFrom = date('Y-m-d 00:00:00', strtotime($find_date_from));
	$obFilter->_lte('education.date',$sDateFrom);
}

if(!empty($find_date_to)){
	$sDateTo = date('Y-m-d 23:59:59', strtotime($find_date_to));
	$obFilter->_gte('education.date',$sDateTo);
}

$obResult = $obQuery->select();
$obResult = new CAdminResult(
	$obResult->bxResult(),
	$sTableID
);
$obResult->NavStart();
$lAdmin->NavText($obResult->GetNavPrint(GetMessage('ESTELIFE_PAGES')));

while($arRecord=$obResult->GetNext()){
	$row =& $lAdmin->AddRow($arRecord['id'],$arRecord);

	if(!empty($arRecord['pay_id']))
		$arRecord['name'] .= ' [<a href="#">'.$arRecord['pay_count'].'</a>]';

	$row->AddViewField("ID",$arRecord['id']);
	$row->AddViewField("NAME",$arRecord['name']);
	$row->AddViewField("DATE",$arRecord['date']);

	$arActions = Array();
	$arActions[] = array(
		"DEFAULT"=>"Y",
		"ICON"=>"edit",
		"TITLE"=>GetMessage("ESTELIFE_EDIT_ALT"),
		"ACTION"=>$lAdmin->ActionRedirect("estelife_education_edit.php?lang=".LANGUAGE_ID."&ID=".$arRecord['id']),
		"TEXT"=>GetMessage("ESTELIFE_EDIT")
	);
	$arActions[] = array(
		"ICON"=>"delete",
		"TITLE"=>GetMessage("ESTELIFE_DELETE_ALT"),
		"ACTION"=>"javascript:if(confirm('".GetMessage("ESTELIFE_CONFIRM_DELETE")."')) window.location='?lang=".LANGUAGE_ID."&action=delete&ID=".$arRecord['id']."&".bitrix_sessid_get()."'",
		"TEXT"=>GetMessage("ESTELIFE_DELETE"));
	$row->AddActions($arActions);
}

$lAdmin->AddFooter(
	array(
		array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>1),
		array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"),
	)
);
$lAdmin->AddGroupActionTable(Array(
	"delete"=>GetMessage("FORM_DELETE_L"),
));
$aMenu = array();
$aMenu[] = array(
	"TEXT"	=>GetMessage("ESTELIFE_CREATE"),
	"TITLE"=>GetMessage("ESTELIFE_CREATE_TITLE"),
	"LINK"=>"estelife_education_edit.php?lang=".LANG,
	"ICON" => "btn_new"
);
$aContext = $aMenu;
$lAdmin->AddAdminContextMenu($aContext);
$lAdmin->CheckListMode();
$APPLICATION->SetTitle(GetMessage("ESTELIFE_HEAD_TITLE"));

require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
	<form name="find_form" method="GET" action="<?=$APPLICATION->GetCurPage()?>?">
		<?php
		$oFilter = new CAdminFilter(
			$sTableID."_filter",
			array(
				GetMessage("ESTELIFE_F_ID"),
				GetMessage("ESTELIFE_F_NAME"),
				GetMessage("ESTELIFE_F_DATE"),
			)
		);
		$oFilter->Begin();
		?>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_ID")?></td>
			<td><input type="text" name="find_id" size="47" value="<?echo htmlspecialcharsbx($find_id)?>"></td>
		</tr>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_NAME")?></td>
			<td><input type="text" name="find_name" size="47" value="<?echo htmlspecialcharsbx($find_name)?>"></td>
		</tr>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_DATE")?></td>
			<td><?echo CalendarPeriod("find_date_from", htmlspecialcharsex($find_date_from), "find_date_to", htmlspecialcharsex($find_date_to), "find_form")?></td>
		</tr>
		<?php
		$oFilter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage()));
		$oFilter->End();
		?>
	</form>
<?php
$lAdmin->DisplayList();
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/estelife/prolog.php");