<?php
use core\database\mysql\VFilter;
use core\database\VDatabase;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$sTableID = "tbl_estelife_entry_list";
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
	"find_user_id",
	"find_country_id",
	"find_city_id",
	"find_short_description"
);
$lAdmin->InitFilter($arFilterFields);

$arFilter = Array(
	"id"			=> $find_id,
	"user_id"		=>$find_user_id,
	"country_id"	=> $find_country_id,
	"city_id"		=> $find_city_id,
	"description"	=> $find_short_description,
);


$arFilterData=array();
$obQuery=\core\database\VDatabase::driver()->createQuery();
$obQuery->builder()
	->from('iblock_element')
	->field('ID')
	->field('NAME')
	->filter()
	->_eq('IBLOCK_ID',15);
$arFilterData['countries']=$obQuery->select()->all();

$obQuery->builder()
	->from('user')
	->field('ID')
	->field('NAME');
$arFilterData['users']=$obQuery->select()->all();

//====== TABLE HEADERS =========
$headers = array(
	array("id"=>"ID", "content"=>GetMessage("ESTELIFE_F_ID"), "sort"=>"id", "default"=>true),
	array("id"=>"USER_ID", "content"=>GetMessage("ESTELIFE_F_USER_ID"), "sort"=>"user_id", "default"=>true),
	array("id"=>"COUNTRY", "content"=>GetMessage("ESTELIFE_F_COUNTRY"), "sort"=>"country", "default"=>true),
	array("id"=>"CITY", "content"=>GetMessage("ESTELIFE_F_CITY"), "sort"=>"city", "default"=>true),
	array("id"=>"SHORT_DESCRIPTION", "content"=>GetMessage("ESTELIFE_F_DESCRIPTION"), "sort"=>"short_description", "default"=>true),
);

$lAdmin->AddHeaders($headers);


//==== Здесь надо зафигачить генерацию списка ========
if(($arID = $lAdmin->GroupAction()) && check_bitrix_sessid()){
	foreach($arID as $ID){
		if(($ID = IntVal($ID))>0 && $_REQUEST['action']=='delete'){
			try{
				$obQuery = VDatabase::driver()->createQuery();
				$obQuery->builder()->from('estelife_professionals')->filter()
					->_eq('id', $ID);
				$obQuery->delete();
			}catch(\core\database\exceptions\VCollectionException $e){}
		}
	}
}


$obQuery=VDatabase::driver()->createQuery();
$obJoin=$obQuery->builder()
	->from('estelife_professionals','ep')
	->join();
$obJoin->_left()
	->_from('ep','country_id')
	->_to('iblock_element','ID','ecn')
	->_cond()
	->_eq('ecn.IBLOCK_ID',15);
$obJoin->_left()
	->_from('ep','city_id')
	->_to('iblock_element','ID','ect')
	->_cond()
	->_eq('ect.IBLOCK_ID',16);
$obJoin->_left()
	->_from('ep','user_id')
	->_to('user','ID','u');

$obFilter=$obQuery->builder()
	->sort($by,$order)
	->field('ep.id','id')
	->field('ep.user_id','user_id')
	->field('u.NAME','user_name')
	->field('ecn.NAME','country')
	->field('ect.NAME','city')
	->field('ep.short_description','short_description')
	->field('ep.full_description','full_description')
	->filter();




if($_GET && $_GET['set_filter'] == 'Y'){

	if(!empty($arFilter['id']))
		$obFilter->_eq('ep.id',$arFilter['id']);
	if(!empty($arFilter['user_id']))
		$obFilter->_eq('ep.user_id',$arFilter['user_id']);
	if(!empty($arFilter['country_id']))
		$obFilter->_eq('ecn.ID',$arFilter['country_id']);
	if(!empty($arFilter['city_id']))
		$obFilter->_eq('ect.ID',$arFilter['city_id']);
	if(!empty($arFilter['description']))
		$obFilter->_like('ep.short_description',$arFilter['description'],VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);
}


if($by=='id'){
	$obQuery->builder()->sort('ep.id',$order);
}else if($by=='user_id')
	$obQuery->builder()->sort('u.NAME',$order);
else if($by=='country')
	$obQuery->builder()->sort('ecn.NAME',$order);
else if($by=='city')
	$obQuery->builder()->sort('ect.NAME',$order);
else if($by=='short_description')
	$obQuery->builder()->sort('ep.short_description',$order);


$obFilter=$obQuery->builder()
	->sort($by,$order)
	->field('ep.id','id')
	->field('ep.user_id','user_id')
	->field('ecn.NAME','country')
	->field('ect.NAME','city')
	->field('ep.short_description','short_description')
	->field('ep.full_description','full_description')
	->filter();


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
	$row->AddViewField("USER_ID", $arRecord['user_name']);
	$row->AddViewField("COUNTRY", $arRecord['country']);
	$row->AddViewField("CITY", $arRecord['city']);
	$row->AddViewField("SHORT_DESCRIPTION", $arRecord['short_description']);

	$arActions = Array();
	$arActions[] = array("DEFAULT"=>"Y", "ICON"=>"edit", "TITLE"=>GetMessage("ESTELIFE_EDIT_ALT"), "ACTION"=>$lAdmin->ActionRedirect("estelife_professionals_edit.php?lang=".LANGUAGE_ID."&ID=$f_ID"), "TEXT"=>GetMessage("ESTELIFE_EDIT"));
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
$aMenu = array();
$aMenu[] = array(
	"TEXT"	=>GetMessage("ESTELIFE_CREATE"),
	"TITLE"=>GetMessage("ESTELIFE_CREATE_TITLE"),
	"LINK"=>"estelife_professionals_edit.php?lang=".LANG,
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
				GetMessage("ESTELIFE_F_ID"),
				GetMessage("ESTELIFE_F_USER_ID"),
				GetMessage("ESTELIFE_F_COUNTRY"),
				GetMessage("ESTELIFE_F_CITY"),
				GetMessage("ESTELIFE_F_DESCRIPTION"),
			)
		);
		$oFilter->Begin();
		?>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_ID")?></td>
			<td><input type="text" name="find_id" size="30" value="<?echo htmlspecialcharsbx($find_id)?>"></td>
		</tr>

		<tr>
			<td><?echo GetMessage("ESTELIFE_F_USER_ID")?></td>
			<td>
				<select name="find_user_id">
					<option value="0"><?echo GetMessage("ESTELIFE_NOT_IMPORTANT")?></option>
					<?php if(!empty($arFilterData['users'])): ?>
						<?php foreach($arFilterData['users'] as $arUser): ?>
							<option value="<?=$arUser['ID']?>"<?=($arUser['ID']==$find_user_id ? ' selected="true"' : '')?>><?=$arUser['NAME']?></option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_COUNTRY")?></td>
			<td>
				<select name="find_country_id">
					<option value="0"><?echo GetMessage("ESTELIFE_NOT_IMPORTANT")?></option>
					<?php if(!empty($arFilterData['countries'])): ?>
						<?php foreach($arFilterData['countries'] as $arCountry): ?>
							<option value="<?=$arCountry['ID']?>"<?=($arCountry['ID']==$find_country_id ? ' selected="true"' : '')?>><?=$arCountry['NAME']?></option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_CITY")?></td>
			<td>
				<select name="find_city_id">
					<option value="0"><?echo GetMessage("ESTELIFE_NOT_IMPORTANT")?></option>
					<?php if(!empty($arFilterData['cities'])): ?>
						<?php foreach($arFilterData['cities'] as $arCity): ?>
							<option value="<?=$arCity['ID']?>"<?=($arCity['ID']==$find_city_id ? ' selected="true"' : '')?>><?=$arCity['NAME']?></option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_DESCRIPTION")?></td>
			<td><input type="text" name="find_short_description" size="30" value="<?echo htmlspecialcharsbx($find_short_description)?>"></td>
		</tr>
		<?
		$oFilter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage()));
		$oFilter->End();
		#############################################################
		?>
	</form>

<?


$lAdmin->DisplayList();