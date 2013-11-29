<?php
use core\database\mysql\VFilter;
use core\database\VDatabase;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$sTableID = "tbl_estelife_activity_list";
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
	"find_short_name",
	"find_country_id",
	"find_city_id"
);
$lAdmin->InitFilter($arFilterFields);
$arFilter = Array(
	"id"			=> $find_id,
	"short_name"		=> $find_short_name,
	"country_id"	=> $find_country_id,
	"city_id"		=> $find_city_id
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

//====== TABLE HEADERS =========
$headers = array(
	array("id"=>"short_name", "content"=>GetMessage("ESTELIFE_F_FULL_NAME"), "sort"=>"short_name", "default"=>true),
	array("id"=>"country", "content"=>GetMessage("ESTELIFE_F_COUNTRY"),"sort"=>"country_id","default"=>true),
	array("id"=>"city", "content"=>GetMessage("ESTELIFE_F_CITY"),"sort"=>"city","default"=>true),
	array("id"=>"id", "content"=>GetMessage("ESTELIFE_F_ID"), "sort"=>"id", "default"=>true)
);
$lAdmin->AddHeaders($headers);

//==== Здесь надо зафигачить генерацию списка ========

if(($arID = $lAdmin->GroupAction()) && check_bitrix_sessid()){
	foreach($arID as $ID){
		if(($ID = IntVal($ID))>0 && $_REQUEST['action']=='delete'){
			try{
				$obEvents= VDatabase::driver();
				$obQuery = $obEvents->createQuery();
				$obQuery->builder()->from('estelife_events')->filter()
					->_eq('id', $ID);
				$obQuery->delete();
			}catch(\core\database\exceptions\VCollectionException $e){}
		}
	}
}

if($by=='country')
	$by='ecn.'.$by;
else if($by=='city')
	$by='ect.'.$by;
else
	$by='ee.'.$by;

$obQuery=\core\database\VDatabase::driver()->createQuery();
$obJoin=$obQuery->builder()
	->from('estelife_events','ee')
	->join();
$obJoin->_left()
	->_from('ee','country_id')
	->_to('iblock_element','ID','ecn')
	->_cond()
	->_eq('ecn.IBLOCK_ID',15);
$obJoin->_left()
	->_from('ee','city_id')
	->_to('iblock_element','ID','ect')
	->_cond()
	->_eq('ect.IBLOCK_ID',16);
$obJoin->_left()
	->_from('ee','id')
	->_to('estelife_event_types','event_id','eet');
$obFilter=$obQuery->builder()
	->sort($by,$order)
	->field('ee.id','id')
	->field('ee.short_name','short_name')
	->field('ecn.NAME','country')
	->field('ect.NAME','city')
	->filter();

$obFilter->_or()
	->_ne('eet.type',3);
$obFilter->_or()
	->_isNull('eet.type');

if(!empty($arFilter['id']))
	$obFilter->_like('ee.id',$arFilter['id'],VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);

if(!empty($arFilter['short_name']))
	$obFilter->_like('ee.short_name',$arFilter['short_name'],VFilter::LIKE_AFTER|VFilter::LIKE_BEFORE);

if(!empty($arFilter['countr_id']))
	$obFilter->_eq('ecn.country_id',$arFilter['country_id']);

if(!empty($arFilter['city_id']))
	$obFilter->_eq('ect.city_id',$arFilter['city_id']);

$obQuery->builder()->group('ee.id');
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
	$row->AddViewField("short_name",$arRecord['short_name']);
	$row->AddViewField("country",$arRecord['country']);
	$row->AddViewField("city",$arRecord['city']);

	$arActions = Array();
	$arActions[] = array("DEFAULT"=>"Y", "ICON"=>"edit", "TITLE"=>GetMessage("ESTELIFE_EDIT_ALT"), "ACTION"=>$lAdmin->ActionRedirect("estelife_activity_edit.php?lang=".LANGUAGE_ID."&ID=$f_ID"), "TEXT"=>GetMessage("ESTELIFE_EDIT"));
	$arActions[] = array("ICON"=>"delete", "TITLE"=>GetMessage("ESTELIFE_DELETE_ALT"),"ACTION"=>"javascript:if(confirm('".GetMessage("ESTELIFE_CONFIRM_DELETE")."')) window.location='?lang=".LANGUAGE_ID."&action=delete&ID=$f_ID&".bitrix_sessid_get()."'","TEXT"=>GetMessage("ESTELIFE_DELETE"));
	$row->AddActions($arActions);
}

//получение куки
$arCookie = json_decode($_COOKIE['activity'], true);


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
$aMenu[] = array(
	"TEXT"	=>GetMessage("ESTELIFE_CREATE"),
	"TITLE"=>GetMessage("ESTELIFE_CREATE_TITLE"),
	"LINK"=>"estelife_activity_edit.php?lang=".LANG,
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

	<?php if (!empty($arCookie)):?>
		<div class="adm-info-message-wrap adm-info-message-red">
			<div class="adm-info-message">
				<div class="adm-info-message-title">Вы не заполнили полностью следующие мероприятия:</div>
				<?php foreach ($arCookie as $val):?>
					<a href="/bitrix/admin/estelife_activity_edit.php?lang=ru&ID=<?=$val['id']?>" target="_blank"><?=$val['name']?></a><br />
				<?php endforeach?>
				<div class="adm-info-message-icon"></div>
			</div>
		</div>
	<?php endif?>

	<a name="tb"></a>
	<form name="form1" method="GET" action="<?=$APPLICATION->GetCurPage()?>?">
		<?php
		$oFilter = new CAdminFilter(
			$sTableID."_filter",
			array(
				GetMessage("ESTELIFE_F_ID"),
				GetMessage("ESTELIFE_F_FULL_NAME"),
				GetMessage("ESTELIFE_F_COUNTRY"),
				GetMessage("ESTELIFE_F_CITY")
			)
		);
		$oFilter->Begin();
		?>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_ID")?></td>
			<td><input type="text" name="find_id" size="47" value="<?echo htmlspecialcharsbx($find_id)?>"></td>
		</tr>
		<tr>
			<td><b><?echo GetMessage("ESTELIFE_F_FULL_NAME")?></b></td>
			<td><input type="text" name="find_short_name" size="47" value="<?echo htmlspecialcharsbx($find_short_name)?>"></td>
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
		<?
		$oFilter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage()));
		$oFilter->End();
		#############################################################
		?>
	</form>

<?
$lAdmin->DisplayList();
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");