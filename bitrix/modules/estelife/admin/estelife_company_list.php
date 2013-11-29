<?php


use core\database\mysql\VFilter;
use core\database\VDatabase;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$sTableID = "tbl_estelife_company_list";
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
	"find_name"
);
$lAdmin->InitFilter($arFilterFields);

InitBVar($find_id_exact_match);
InitBVar($find_name_exact_match);
InitBVar($find_city_exact_match);
$arFilter = Array(
	"id"		=> $find_id,
	"name"		=> $find_name,
	"city_id"		=> $find_city_id,
);

//====== TABLE HEADERS =========
$headers = array(
	array("id"=>"NAME", "content"=>GetMessage("ESTELIFE_F_TITLE"), "sort"=>"name", "default"=>true),
	array("id"=>"ID", "content"=>GetMessage("ESTELIFE_F_ID"), "sort"=>"id", "default"=>true),
);
$lAdmin->AddHeaders($headers);

$arFilterData=array();
$obQuery=\core\database\VDatabase::driver()->createQuery();
$obQuery->builder()
	->from('iblock_element')
	->field('ID')
	->field('NAME')
	->filter()
	->_eq('IBLOCK_ID',16);
$arFilterData['cities']=$obQuery->select()->all();

$obCompanies= VDatabase::driver();

//==== Здесь надо зафигачить генерацию списка ========
if(($arID = $lAdmin->GroupAction()) && check_bitrix_sessid()){
	foreach($arID as $ID){
		if(($ID = IntVal($ID))>0 && $_REQUEST['action']=='delete'){
			try{
				$obQuery = $obCompanies->createQuery();
				$obQuery->builder()->from('estelife_companies')->filter()
					->_eq('id', $ID);
				$obQuery->delete();
			}catch(\core\database\exceptions\VCollectionException $e){}
		}
	}
}

$obQuery=$obCompanies->createQuery();
$obQuery->builder()->from('estelife_companies','ec');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ec', 'id')
	->_to('estelife_company_types', 'company_id', 'type');
$obJoin->_left()
	->_from('ec','id')
	->_to('estelife_company_geo', 'company_id', 'ecg');
$obJoin->_left()
	->_from('type','id')
	->_to('estelife_company_geo', 'company_id', 'ectg');
$obQuery->builder()
	->field('ec.id','id')
	->field('ec.name','name');
$obFilter=$obQuery->builder()->filter();

if(!empty($arFilter['id']))
	$obFilter->_like('ec.id',$arFilter['id'],VFilter::LIKE_BEFORE|VFilter::LIKE_AFTER);
if(!empty($arFilter['name']))
	$obFilter->_like('ec.name',$arFilter['name'],VFilter::LIKE_BEFORE|VFilter::LIKE_AFTER);
if(!empty($arFilter['city_id'])){
	$obFilter->_or()->_eq('ecg.city_id',$arFilter['city_id']);
	$obFilter->_or()->_eq('ectg.city_id',$arFilter['city_id']);
}

$obQuery->builder()->sort('ec.'.$by,$order);

$obQuery->builder()->group('ec.id');
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
	$row->AddViewField("NAME",$arRecord['name']);

	$arActions = Array();
	$arActions[] = array(
		"DEFAULT"=>"Y",
		"ICON"=>"edit",
		"TITLE"=>GetMessage("ESTELIFE_EDIT_ALT"),
		"ACTION"=>$lAdmin->ActionRedirect("estelife_company_edit.php?lang=".LANGUAGE_ID."&ID=$f_ID"),
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
		"LINK"=>"estelife_company_edit.php?lang=".LANG,
		"ICON" => "btn_new"
	);

	$aContext = $aMenu;
	$lAdmin->AddAdminContextMenu($aContext);
//}

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("ESTELIFE_HEAD_TITLE"));


require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>

	<a name="tb"></a>
	<form name="form1" method="GET" action="<?=$APPLICATION->GetCurPage()?>?">
		<?php
		$oFilter = new CAdminFilter(
			$sTableID."_filter",
			array(
				GetMessage("ESTELIFE_F_ID"),
				GetMessage("ESTELIFE_F_TITLE"),
				GetMessage("ESTELIFE_F_CITY"),
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
			<td><?echo GetMessage("ESTELIFE_F_CITY")?></td>
			<td>
				<select name="find_city_id" value="<?echo htmlspecialcharsbx($find_city_id)?>">
					<option value="0"><?echo GetMessage("ESTELIFE_NOT_IMPORTANT")?></option>
					<?php if(!empty($arFilterData['cities'])): ?>
						<?php foreach($arFilterData['cities'] as $arCity): ?>
							<option value="<?=$arCity['ID']?>"><?=$arCity['NAME']?></option>
						<?php endforeach; ?>
					<?php endif; ?>
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