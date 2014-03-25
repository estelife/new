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
	"find_clinic",
	"find_specialist",
	"find_moderate",
);
$lAdmin->InitFilter($arFilterFields);
$arFilter = Array(
	"id"=>$find_id,
	"name"=>$find_name,
	"clinic"=> $find_clinic,
	"specialist"=>$find_specialist,
	"moderate"=>$find_moderate,
);

//====== TABLE HEADERS =========
$headers = array(
	array("id"=>"id", "content"=>GetMessage("ESTELIFE_F_ID"), "sort"=>"id", "default"=>true),
    array("id"=>"active", "content"=>GetMessage("ESTELIFE_F_ACTIVE"),"sort"=>"active","default"=>true),
    array("id"=>"moderate", "content"=>GetMessage("ESTELIFE_F_MODERATE"),"sort"=>"moderate","default"=>true),
    array("id"=>"user", "content"=>GetMessage("ESTELIFE_F_USER"),"sort"=>"user","default"=>true),
	array("id"=>"clinic", "content"=>GetMessage("ESTELIFE_F_CLINIC"),"sort"=>"clinic","default"=>true),
	array("id"=>"date_add", "content"=>GetMessage("ESTELIFE_F_DATE_ADD"),"sort"=>"date_add","default"=>true),
	array("id"=>"problem", "content"=>GetMessage("ESTELIFE_F_PROBLEM"),"sort"=>"problem","default"=>true),
	array("id"=>"specialist", "content"=>GetMessage("ESTELIFE_F_SPECIALIST"),"sort"=>"specialist","default"=>true),

);
$lAdmin->AddHeaders($headers);

//==== Здесь надо зафигачить генерацию списка ========
$obComment=\core\database\VDatabase::driver();

if(($arID = $lAdmin->GroupAction()) && check_bitrix_sessid()){
	foreach($arID as $ID){
		if(($ID = IntVal($ID))>0 && $_REQUEST['action']=='moderate'){
			$obQuery=$obComment->createQuery();
			$obQuery->builder()
				->from('estelife_clinic_reviews')
				->value('date_moderate', date('Y-m-d H:i:s', time()))
				->filter()
				->_eq('id', $ID);
			if ($obQuery->update()){
				$obQuery=$obComment->createQuery();
				$obQuery->builder()
					->from('estelife_moderate')
					->value('type_id', 2)
					->value('element_id', $ID)
					->value('manager_id', $USER->GetID())
					->value('date', date('Y-m-d H:i:s',time()));
				$obQuery->insert();
			}
		}

		if(($ID = IntVal($ID))>0 && $_REQUEST['action']=='active'){
			$obQuery=$obComment->createQuery();
			$obQuery->builder()
				->from('estelife_clinic_reviews')
				->value('active', 1)
				->filter()
				->_eq('id', $ID);
			$obQuery->update();
		}

		if(($ID = IntVal($ID))>0 && $_REQUEST['action']=='deactive'){
			$obQuery=$obComment->createQuery();
			$obQuery->builder()
				->from('estelife_clinic_reviews')
				->value('active', 0)
				->filter()
				->_eq('id', $ID);
			$obQuery->update();
		}
	}
}

$obQuery=$obComment->createQuery();
$obQuery->builder()
	->from('estelife_clinic_reviews', 'ecr');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
    ->_from('ecr', 'problem_id')
    ->_to('estelife_clinic_problems', 'id', 'ecp');
$obJoin->_left()
    ->_from('ecr', 'specialist_id')
    ->_to('estelife_professionals', 'id', 'ep');
$obJoin->_left()
	->_from('ep', 'user_id')
	->_to('user', 'ID', 'u');
$obJoin->_left()
    ->_from('ecr', 'clinic_id')
    ->_to('estelife_clinics', 'id', 'ec');
$obJoin->_left()
    ->_from('ecr', 'user_id')
    ->_to('user', 'ID', 'uu');

if($by=='active')
	$obQuery->builder()->sort('ecr.active',$order);
else if($by=='moderate')
    $obQuery->builder()->sort('ecr.date_moderate',$order);
else if($by=='clinic')
	$obQuery->builder()->sort('ec.name',$order);
else if($by=='user')
    $obQuery->builder()->sort('uu.NAME',$order);
else if($by=='problem')
	$obQuery->builder()->sort('ecp.title',$order);
else if($by=='specialist')
	$obQuery->builder()->sort('u.NAME',$order);
else if($by=='id')
	$obQuery->builder()->sort('ecr.id',$order);
else if($by=='date_add')
	$obQuery->builder()->sort('ecr.date_add',$order);

$obFilter=$obQuery->builder()
	->field('ecr.id')
	->field('ecr.date_add')
	->field('ecr.active')
    ->field('ecr.specialist_name')
    ->field('ecr.problem_name')
	->field('ecr.date_moderate')
	->field('ec.name', 'clinic_name')
	->field('u.NAME', 'name')
	->field('u.LAST_NAME', 'last_name')
	->field('u.LOGIN', 'login')
    ->field('uu.NAME', 'user_name')
    ->field('uu.LAST_NAME', 'user_last_name')
    ->field('uu.LOGIN', 'user_login')
    ->field('ecp.title', 'problem')
	->sort('ecr.date_add','desc')
	->filter();

if(!empty($arFilter['id']))
	$obFilter->_eq('ecr.id',$arFilter['id']);

if(!empty($arFilter['clinic']))
	$obFilter->_like('ec.name',$arFilter['clinic'],VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);;

if(!empty($arFilter['name'])){
    $obFilter->_or()->_like('uu.LAST_NAME',$arFilter['name'],VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);
    $obFilter->_or()->_like('uu.NAME',$arFilter['name'],VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);
}

if(!empty($arFilter['specialist'])){
    $obFilter->_or()->_like('u.LAST_NAME',$arFilter['specialist'],VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);
    $obFilter->_or()->_like('u.NAME',$arFilter['specialist'],VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);
    $obFilter->_or()->_like('ecr.specialist_name',$arFilter['specialist'],VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);
}

if($arFilter['moderate']==1)
    $obFilter->_notNull('ecr.date_moderate');
elseif($arFilter['moderate']==2)
    $obFilter->_isNull('ecr.date_moderate');


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
    if (!empty($arRecord['specialist_name'])){
        $arRecord['name'] = $arRecord['specialist_name'];
    }else{
        if (empty($arRecord['name']))
            $arRecord['name']=$arRecord['login'];
        elseif (empty($arRecord['last_name']))
            $arRecord['name']=$arRecord['name'];
        else
            $arRecord['name']=$arRecord['last_name'].' '.$arRecord['name'];
    }

	$row->AddViewField("specialist",$arRecord['name']);
    if (empty($arRecord['user_name']))
        $arRecord['user_name']=$arRecord['login'];
    elseif (empty($arRecord['user_last_name']))
        $arRecord['user_name']=$arRecord['user_name'];
    else
        $arRecord['user_name']=$arRecord['user_last_name'].' '.$arRecord['user_name'];
    $row->AddViewField("user",$arRecord['user_name']);
	$nActive=$arRecord['active'];
	$arRecord['active']=($arRecord['active']>0) ? 'Да' : 'Нет';
	$row->AddViewField("active",$arRecord['active']);
    $arRecord['moderate']=(!is_null($arRecord['date_moderate'])) ? 'Да' : '<span class="moderate">Нет</span>';
    $row->AddViewField("moderate",$arRecord['moderate']);
	$row->AddViewField("date_add",$arRecord['date_add']);
    $row->AddViewField("clinic",$arRecord['clinic_name']);
    if (!empty($arRecord['problem_name']))
        $arRecord['problem'] = $arRecord['problem_name'];
    $row->AddViewField("problem",$arRecord['problem']);


	$arActions = Array();
	$arActions[] = array("DEFAULT"=>"Y", "ICON"=>"edit", "TITLE"=>GetMessage("ESTELIFE_EDIT"), "ACTION"=>$lAdmin->ActionRedirect("estelife_clinic_reviews_edit.php?lang=".LANGUAGE_ID."&ID=$f_ID"), "TEXT"=>GetMessage("ESTELIFE_EDIT"));
	$arActions[] = array("ICON"=>"edit", "TITLE"=>GetMessage("ESTELIFE_MODERATE_ALT"),"ACTION"=>"javascript:window.location='?lang=".LANGUAGE_ID."&action=moderate&ID=$f_ID&".bitrix_sessid_get()."'","TEXT"=>GetMessage("ESTELIFE_MODERATE"));
	if ($nActive>0){
		$arActions[] = array(
			"ICON"=>"delete",
			"TITLE"=>GetMessage("ESTELIFE_DEACTIVE_ALT"),
			"ACTION"=>"javascript:if(confirm('".GetMessage("ESTELIFE_CONFIRM_DEACTIVE")."')) window.location='?lang=".LANGUAGE_ID."&action=deactive&ID=$f_ID&".bitrix_sessid_get()."'",
			"TEXT"=>GetMessage("ESTELIFE_DEACTIVE")
		);
	}else{
			$arActions[] = array(
				"ICON"=>"edit",
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
	<form name="form1" method="GET" action="<?=$APPLICATION->GetCurPage()?>?">
		<?php
		$oFilter = new CAdminFilter(
			$sTableID."_filter",
			array(
				GetMessage("ESTELIFE_F_ID"),
				GetMessage("ESTELIFE_F_NAME"),
				GetMessage("ESTELIFE_F_CLINIC"),
                GetMessage("ESTELIFE_F_SPECIALIST"),
				GetMessage("ESTELIFE_F_MODERATE"),
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
			<td><b><?echo GetMessage("ESTELIFE_F_CLINIC")?></b></td>
			<td><input type="text" name="find_clinic" size="47" value="<?echo htmlspecialcharsbx($find_clinic)?>"></td>
		</tr>
        <tr>
            <td><b><?echo GetMessage("ESTELIFE_F_SPECIALIST")?></b></td>
            <td><input type="text" name="find_specialist" size="47" value="<?echo htmlspecialcharsbx($find_specialist)?>"></td>
        </tr>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_MODERATE")?></td>
			<td>
				<select name="find_moderate" value="<?echo htmlspecialcharsbx($find_moderate)?>">
					<option value="">Выберите вариант</option>
					<option value="1">Да</option>
					<option value="2">Нет</option>
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