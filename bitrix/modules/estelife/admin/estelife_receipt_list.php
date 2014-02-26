<?php
use core\database\mysql\VFilter;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
global $APPLICATION;

$sTableID = "tbl_estelife_receipt_list";
$oSort = new CAdminSorting($sTableID, "id", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

ClearVars();
$sModuleRight = $APPLICATION->GetGroupRight("estelife");

if($sModuleRight<"F")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

CModule::IncludeModule("estelife");
IncludeModuleLangFile(__FILE__);
$lAdmin->InitFilter(array(
	'find_id',
	'find_user',
	'find_user_id',
	'find_user_email',
	'find_education_id',
	'find_education',
	'find_payment_id',
	'find_status',
	'find_date_create_from',
	'find_date_create_to',
	'find_date_change_from',
	'find_date_change_to'
));
$lAdmin->AddHeaders(array(
	array("id"=>"USER", "content"=>GetMessage("ESTELIFE_F_USER"), "sort"=>"user", "default"=>true),
	array("id"=>"USER_EMAIL", "content"=>GetMessage("ESTELIFE_F_USER_EMAIL"), "sort"=>"user_email", "default"=>true),
	array("id"=>"EDUCATION", "content"=>GetMessage("ESTELIFE_F_EDUCATION"), "sort"=>"education", "default"=>true),
	array("id"=>"STATUS", "content"=>GetMessage("ESTELIFE_F_STATUS"), "sort"=>"status", "default"=>true),
	array("id"=>"DATE_CREATE", "content"=>GetMessage("ESTELIFE_F_DATE_CREATE"), "sort"=>"date_create", "default"=>true),
	array("id"=>"DATE_CHANGE", "content"=>GetMessage("ESTELIFE_F_DATE_CHANGE"), "sort"=>"date_change", "default"=>true),
	array("id"=>"PAYMENT_ID", "content"=>GetMessage("ESTELIFE_F_PAYMENT_ID"), "sort"=>"payment_id", "default"=>true),
	array("id"=>"AMOUNT", "content"=>GetMessage("ESTELIFE_F_AMOUNT"), "sort"=>"amount", "default"=>true),
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
			->from('estelife_pay_receipts')
			->filter()
			->_in('id', $arDeleted);
		$obQuery->delete();
	}
}

$obJoin = $obQuery->builder()
	->from('estelife_pay_receipts','receipt')
	->field('receipt.id','id')
	->field('receipt.date_create','date_create')
	->field('receipt.date_change','date_change')
	->field('receipt.status','status')
	->field('receipt.payment_id','payment_id')
	->field('receipt.amount','amount')
	->field('education.id','education_id')
	->field('education.name','education_name')
	->field('receipt_user.ID','user_id')
	->field('receipt_user.NAME','user_name')
	->field('receipt_user.EMAIL','user_email')
	->join();

$obJoin->_left()
	->_from('receipt','service_id')
	->_to('estelife_education','id','education');
$obJoin->_left()
	->_from('receipt','user_id')
	->_to('user','ID','receipt_user');

if($by == 'education')
	$obQuery->builder()->sort('education.name', $order);
else if($by == 'user')
	$obQuery->builder()->sort('receipt_user.NAME', $order);
else if($by == 'user_email')
	$obQuery->builder()->sort('receipt_user.EMAIL', $order);
else
	$obQuery->builder()->sort('receipt.'.$by, $order);

$obFilter = $obQuery->builder()->filter();

if(!empty($find_id))
	$obFilter->_eq('receipt.id',$find_id);

if(!empty($find_date_create_from)){
	$sDateFrom = date('Y-m-d 00:00:00', strtotime($find_date_create_from));
	$obFilter->_lte('receipt.date_create',$sDateFrom);
}

if(!empty($find_date_create_to)){
	$sDateTo = date('Y-m-d 23:59:59', strtotime($find_date_create_to));
	$obFilter->_gte('receipt.date_create',$sDateTo);
}

if(!empty($find_date_change_from)){
	$sDateFrom = date('Y-m-d 00:00:00', strtotime($find_date_change_from));
	$obFilter->_lte('receipt.date_change',$sDateFrom);
}

if(!empty($find_date_change_to)){
	$sDateTo = date('Y-m-d 23:59:59', strtotime($find_date_change_to));
	$obFilter->_gte('receipt.date_change',$sDateTo);
}

if(!empty($find_status)){
	$obFilter->_eq('receipt.id',intval($find_status));
}

if(!empty($find_payment_id)){
	$obFilter->_eq('receipt.payment_id',intval($find_payment_id));
}

if(!empty($find_user_id)) {
	$obFilter->_eq('receipt.user_id',intval($find_user_id));
}

if(!empty($find_education_id)){
	$obFilter->_eq('receipt.service_id',intval($find_education_id));
}

if(!empty($find_user)){
	$obFilter->_like('user.NAME',$find_user_name,VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);
}

if(!empty($find_education)){
	$obFilter->_like('education.name',$find_education,VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);
}

$arStatuses = array(
	1 => 'Создан',
	2 => 'В обработке',
	3 => 'Выполнен'
);
$obResult = $obQuery->select();
$obResult = new CAdminResult(
	$obResult->bxResult(),
	$sTableID
);
$obResult->NavStart();
$lAdmin->NavText($obResult->GetNavPrint(GetMessage('ESTELIFE_PAGES')));

while($arRecord=$obResult->GetNext()){
	$f_ID = $arRecord['id'];

	$arRecord['user_name'] = !empty($arRecord['user_name']) ?
		$arRecord['user_name'] :
		$arRecord['user_email'];
	$arRecord['user_name'] = '<a href="/bitrix/admin/user_edit.php?ID='.$arRecord['user_id'].'" target="_blank">'.$arRecord['user_name'].' ['.$arRecord['user_id'].']</a>';
	$arRecord['user_email'] = '<a href="mailto:'.$arRecord['user_email'].'" target="_blank">'.$arRecord['user_email'].'</a>';
	$arRecord['education_name'] = '<a href="/bitrix/admin/estelife_education_edit.php?lang=ru&ID='.$arRecord['education_id'].'" target="_blank">'.$arRecord['education_name'].' ['.$arRecord['education_id'].']</a>';

	$row =& $lAdmin->AddRow($arRecord['id'],$arRecord);
	$row->AddViewField("ID",$arRecord['id']);
	$row->AddViewField("USER",$arRecord['user_name']);
	$row->AddViewField("USER_EMAIL",$arRecord['user_email']);
	$row->AddViewField("EDUCATION",$arRecord['education_name']);
	$row->AddViewField("DATE_CREATE",$arRecord['date_create']);
	$row->AddViewField("DATE_CHANGE",$arRecord['date_change']);
	$row->AddViewField("STATUS",$arStatuses[$arRecord['status']]);
	$row->AddViewField("PAYMENT_ID",$arRecord['payment_id']);
	$row->AddViewField("AMOUNT",$arRecord['amount']);

	$arActions = Array();
//	$arActions[] = array(
//		"ICON"=>"delete",
//		"TITLE"=>GetMessage("ESTELIFE_DELETE_ALT"),
//		"ACTION"=>"javascript:if(confirm('".GetMessage("ESTELIFE_CONFIRM_DELETE")."')) window.location='?lang=".LANGUAGE_ID."&action=delete&ID=".$arRecord['id']."&".bitrix_sessid_get()."'",
//		"TEXT"=>GetMessage("ESTELIFE_DELETE"));
	$row->AddActions($arActions);

	$arActions = Array();
	$arActions[] = array(
		"DEFAULT"=>"Y",
		"TITLE"=>GetMessage("ESTELIFE_CHANGE_IN_DEV"),
		"ACTION"=>$lAdmin->ActionRedirect("estelife_receipt_list.php?lang=".LANGUAGE_ID."&ID=$f_ID"."&STATUS=2"),
		"TEXT"=>GetMessage("ESTELIFE_CHANGE_IN_DEV")
	);
	$arActions[] = array(
		"DEFAULT"=>"Y",
		"TITLE"=>GetMessage("ESTELIFE_CHANGE_COMPLETED"),
		"ACTION"=>$lAdmin->ActionRedirect("estelife_receipt_list.php?lang=".LANGUAGE_ID."&ID=$f_ID"."&STATUS=3"),
		"TEXT"=>GetMessage("ESTELIFE_CHANGE_COMPLETED")
	);

	$row->AddActions($arActions);

}

//Установка статуса
if(isset($_GET['STATUS']) && isset($_GET['ID'])){
	$obQuery->builder()->from('estelife_pay_receipts')
		->value('status', intval($_GET['STATUS']));

	$obQuery->builder()->filter()
		->_eq('id',$_GET['ID']);
	$obQuery->update();
	LocalRedirect('/bitrix/admin/estelife_receipt_list.php?lang='.LANGUAGE_ID);
}


$lAdmin->AddFooter(
	array(
		array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>1),
		array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"),
	)
);
/*$lAdmin->AddGroupActionTable(Array(
	"delete"=>GetMessage("FORM_DELETE_L"),
));*/


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
				GetMessage("ESTELIFE_F_USER"),
				GetMessage("ESTELIFE_F_USER_ID"),
				GetMessage("ESTELIFE_F_USER_EMAIL"),
				GetMessage("ESTELIFE_F_EDUCATION"),
				GetMessage("ESTELIFE_F_EDUCATION_ID"),
				GetMessage("ESTELIFE_F_PAYMENT_ID"),
				GetMessage("ESTELIFE_F_STATUS"),
				GetMessage("ESTELIFE_F_DATE_CREATE"),
				GetMessage("ESTELIFE_F_DATE_CHANGE")
			)
		);
		$oFilter->Begin();
		?>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_ID")?></td>
			<td><input type="text" name="find_id" size="47" value="<?echo htmlspecialcharsbx($find_id)?>"></td>
		</tr>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_USER")?></td>
			<td><input type="text" name="find_user" size="47" value="<?echo htmlspecialcharsbx($find_user)?>"></td>
		</tr>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_USER_ID")?></td>
			<td><input type="text" name="find_user_id" size="47" value="<?echo htmlspecialcharsbx($find_user_id)?>"></td>
		</tr>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_USER_EMAIL")?></td>
			<td><input type="text" name="find_user_email" size="47" value="<?echo htmlspecialcharsbx($find_user_email)?>"></td>
		</tr>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_EDUCATION")?></td>
			<td><input type="text" name="find_education" size="47" value="<?echo htmlspecialcharsbx($find_education)?>"></td>
		</tr>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_EDUCATION_ID")?></td>
			<td><input type="text" name="find_education_id" size="47" value="<?echo htmlspecialcharsbx($find_education_id)?>"></td>
		</tr>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_PAYMENT_ID")?></td>
			<td><input type="text" name="find_payment_id" size="47" value="<?echo htmlspecialcharsbx($find_payment_id)?>"></td>
		</tr>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_STATUS")?></td>
			<td>
				<select name="find_status">
					<option value="0"><?echo GetMessage("ESTELIFE_FIND_ALL")?></option>
					<?php foreach($arStatuses as $nId=>$sStatus): ?>
						<option value="<?=$nId?>"<?=($find_status==$nId) ? ' selected="true"' : ''?>><?=$sStatus?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_DATE_CREATE")?></td>
			<td><?echo CalendarPeriod("find_date_create_from", htmlspecialcharsex($find_date_create_from), "find_date_create_to", htmlspecialcharsex($find_date_create_to), "find_form")?></td>
		</tr>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_DATE_CHANGE")?></td>
			<td><?echo CalendarPeriod("find_date_change_from", htmlspecialcharsex($find_date_change_from), "find_date_change_to", htmlspecialcharsex($find_date_change_to), "find_form")?></td>
		</tr>
		<?php
		$oFilter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage()));
		$oFilter->End();
		?>
	</form>
<?php
$lAdmin->DisplayList();
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/estelife/prolog.php");