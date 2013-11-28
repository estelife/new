<?php
use core\database\mysql\VFilter;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$sTableID = "tbl_estelife_clinic_list";
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
	"find_specialization_id",
	"find_specialization_id_exact_match",
	"find_service_id",
	"find_service_id_exact_match",
	"find_service_concreate_id",
	"find_service_concreate_id_exact_match",
	"find_city_id",
	"find_city_id_exact_match",
	"find_metro_id",
	"find_metro_id_exact_match"
);
$lAdmin->InitFilter($arFilterFields);

InitBVar($find_id_exact_match);
InitBVar($find_name_exact_match);
InitBVar($find_specialization_id_exact_match);
InitBVar($find_service_id_exact_match);
InitBVar($find_service_concreate_id_exact_match);
InitBVar($find_city_id_exact_match);
InitBVar($find_metro_id_exact_match);
$arFilter = Array(
	"id"						=> $find_id,
	"name"						=> $find_name,
	"specialization_id"			=> $find_specialization_id,
	"service_id"			=> $find_service_id,
	"service_concreate_id"			=> $find_service_concreate_id,
	"city_id"=> $find_city_id,
	"metro_id"=> $find_metro_id
);

//====== TABLE HEADERS =========
$headers = array(
	array("id"=>"NAME", "content"=>GetMessage("ESTELIFE_F_TITLE"), "sort"=>"name", "default"=>true),
	array("id"=>"CITY", "content"=>GetMessage("ESTELIFE_F_CITY"), "sort"=>"city", "default"=>true),
	array("id"=>"METRO", "content"=>GetMessage("ESTELIFE_F_METRO"), "sort"=>"metro", "default"=>true),
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

$obQuery=\core\database\VDatabase::driver()->createQuery();
$obQuery->builder()
	->from('estelife_specializations')
	->field('id')
	->field('name');
$arFilterData['specs']=$obQuery->select()->all();

$obColl=new \clinics\VClinics();

//==== Здесь надо зафигачить генерацию списка ========
if(($arID = $lAdmin->GroupAction()) && check_bitrix_sessid()){
	foreach($arID as $ID){
		if(($ID = IntVal($ID))>0 && $_REQUEST['action']=='delete'){
			try{
				$obRecord=$obColl->record($ID);
				$obColl->delete($obRecord);
			}catch(\core\database\exceptions\VCollectionException $e){}
		}
	}
}

$obQuery=$obColl->createQuery();
$obQuery->builder()->from('estelife_clinics','ec');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ec','city_id')
	->_to('iblock_element','ID','ct')
	->_cond()->_eq('ct.IBLOCK_ID',16);
$obJoin->_left()
	->_from('ec','metro_id')
	->_to('iblock_element','ID','mt')
	->_cond()->_eq('mt.IBLOCK_ID',17);
$obQuery->builder()
	->field('ct.NAME','city')
	->field('mt.NAME','metro')
	->field('ec.id','id')
	->field('ec.name','name');
$obFilter=$obQuery->builder()->filter();

if(!empty($arFilter['id']))
	$obFilter->_like('ec.id',$arFilter['id'],VFilter::LIKE_BEFORE|VFilter::LIKE_AFTER);
if(!empty($arFilter['name']))
	$obFilter->_like('ec.name',$arFilter['name'],VFilter::LIKE_BEFORE|VFilter::LIKE_AFTER);
if(!empty($arFilter['city_id']))
	$obFilter->_eq('ec.city_id',$arFilter['city_id']);
if(!empty($arFilter['metro_id']))
	$obFilter->_eq('ec.metro_id',$arFilter['metro_id']);

if($by=='city')
	$obQuery->builder()->sort('ct.NAME',$order);
elseif($by=='metro')
	$obQuery->builder()->sort('mt.NAME',$order);
else
	$obQuery->builder()->sort('ec.'.$by,$order);

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

	$row->AddViewField("ID",$arRecord['id']);
	$row->AddViewField("NAME",$arRecord['name']);
	$row->AddViewField('CITY',$arRecord['city']);
	$row->AddViewField('METRO',$arRecord['metro']);

	$arActions = Array();
	$arActions[] = array("DEFAULT"=>"Y", "ICON"=>"edit", "TITLE"=>GetMessage("ESTELIFE_EDIT_ALT"), "ACTION"=>$lAdmin->ActionRedirect("estelife_clinic_edit.php?lang=".LANGUAGE_ID."&ID=$f_ID"), "TEXT"=>GetMessage("ESTELIFE_EDIT"));
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
		"LINK"=>"estelife_clinic_edit.php?lang=".LANG,
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
				GetMessage("ESTELIFE_F_CITY"),
				GetMessage("ESTELIFE_F_METRO"),
				GetMessage("ESTELIFE_F_SPEC"),
				GetMessage("ESTELIFE_F_SERVICE"),
				GetMessage("ESTELIFE_F_SERVICE_CONCREATE")
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
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_METRO")?></td>
			<td>
				<select name="find_metro_id" value="<?echo htmlspecialcharsbx($find_metro_id)?>">
					<option value="0"><?echo GetMessage("ESTELIFE_NOT_IMPORTANT")?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_SPEC")?></td>
			<td>
				<select name="find_specialization_id" value="<?echo htmlspecialcharsbx($find_specialization_id)?>">
					<option value="0"><?echo GetMessage("ESTELIFE_NOT_IMPORTANT")?></option>
					<?php if(!empty($arFilterData['specs'])): ?>
						<?php foreach($arFilterData['specs'] as $arSpec): ?>
							<option value="<?=$arSpec['id']?>"><?=$arSpec['name']?></option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_SERVICE")?></td>
			<td>
				<select name="find_service_id" value="<?echo htmlspecialcharsbx($find_service_id)?>">
					<option value="0"><?echo GetMessage("ESTELIFE_NOT_IMPORTANT")?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_SERVICE_CONCREATE")?></td>
			<td>
				<select name="find_service_concreate_id" value="<?echo htmlspecialcharsbx($find_service_concreate_id)?>">
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