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
	"find_name",
	"find_description",
	"find_with_video",
	"find_type_id",
	"find_hall_id",
	"find_section_id",
);
$lAdmin->InitFilter($arFilterFields);


$arFilter = Array(
	"name"			=> $find_name,
	"description"	=> $find_description,
	"video"			=>$find_with_video,
	"type"			=>$find_type_id,
	"hall"			=>$find_hall_id,
	"section"		=>$find_section_id,
);

//====== TABLE HEADERS =========
$headers = array(
	array("id"=>"NAME", "content"=>GetMessage("ESTELIFE_F_TITLE"), "sort"=>"name", "default"=>true),
	array("id"=>"SHORT_DESCRIPTION", "content"=>GetMessage("ESTELIFE_F_SHORT_DESCRIPTION"), "sort"=>"short_description", "default"=>true),
	array("id"=>"WITH_VIDEO", "content"=>GetMessage("ESTELIFE_F_WITH_VIDEO"), "sort"=>"with_video", "default"=>true),
	array("id"=>"FROM_TIME", "content"=>GetMessage("ESTELIFE_F_FROM_TIME"), "sort"=>"from_time", "default"=>true),
	array("id"=>"TO_TIME", "content"=>GetMessage("ESTELIFE_F_TO_TIME"), "sort"=>"to_time", "default"=>true),
	array("id"=>"DURATION", "content"=>GetMessage("ESTELIFE_F_DURATION"), "sort"=>"duration", "default"=>true),
	array("id"=>"TYPE", "content"=>GetMessage("ESTELIFE_F_TYPE"), "sort"=>"type", "default"=>true),
	array("id"=>"HALL", "content"=>GetMessage("ESTELIFE_F_HALL"), "sort"=>"hall", "default"=>true),
	array("id"=>"SECTION", "content"=>GetMessage("ESTELIFE_F_SECTION"), "sort"=>"section", "default"=>true),
);
$lAdmin->AddHeaders($headers);

$arFilterData=array();
$obQuery=\core\database\VDatabase::driver()->createQuery();
$obQuery->builder()
	->from('estelife_activity_types')
	->field('id')
	->field('name');
$arFilterData['types']=$obQuery->select()->all();

$obQuery->builder()
	->from('estelife_event_halls')
	->field('id')
	->field('name');
$arFilterData['halls']=$obQuery->select()->all();

$obQuery->builder()
	->from('estelife_event_sections')
	->field('id')
	->field('name');
$arFilterData['sections']=$obQuery->select()->all();

$obActivities= VDatabase::driver();

//==== Здесь надо зафигачить генерацию списка ========
if(($arID = $lAdmin->GroupAction()) && check_bitrix_sessid()){
	foreach($arID as $ID){
		if(($ID = IntVal($ID))>0 && $_REQUEST['action']=='delete'){
			try{
				$obQuery = $obActivities->createQuery();
				$obQuery->builder()->from('estelife_event_activities')->filter()
					->_eq('id', $ID);
				$obQuery->delete();
			}catch(\core\database\exceptions\VCollectionException $e){}
		}
	}
}

$obQuery=$obActivities->createQuery();
$obQuery->builder()->from('estelife_event_activities','ea');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ea', 'type_id')
	->_to('estelife_activity_types', 'id', 'type');
$obJoin->_left()
	->_from('ea','hall_id')
	->_to('estelife_event_halls', 'id', 'eh');
$obJoin->_left()
	->_from('ea','section_id')
	->_to('estelife_event_sections', 'id', 'es');
$obQuery->builder()
	->field('ea.id','id')
	->field('ea.name','name')
	->field('ea.short_description', 'description')
	->field('ea.with_video','video')
	->field('ea.time_from','time_from')
	->field('ea.time_to','time_to')
	->field('ea.duration','duration')
	->field('type.name','type')
	->field('eh.name','hall')
	->field('es.name','section');

$obFilter=$obQuery->builder()->filter();

//работает некорректно
if(!empty($arFilter['name']))
	$obFilter->_like('ea.name',$arFilter['name'],VFilter::LIKE_BEFORE|VFilter::LIKE_AFTER);
if(!empty($arFilter['description']))
	$obFilter->_like('ea.short_description',$arFilter['description'],VFilter::LIKE_BEFORE|VFilter::LIKE_AFTER);
if(!empty($arFilter['video'])){
	$nFVideo = ($arFilter['video'] == 'yes')? 1:0;
	$obFilter->_eq('ea.with_video',$nFVideo);
}
if(!empty($arFilter['type'])){
	$obFilter->_eq('ea.type_id',$arFilter['type']);
}
if(!empty($arFilter['hall'])){
	$obFilter->_eq('ea.hall_id',$arFilter['hall']);
}
if(!empty($arFilter['section'])){
	$obFilter->_eq('ea.section_id',$arFilter['section']);
}


if($by=='name')
	$obQuery->builder()->sort('ea.name',$order);
elseif($by=='short_description')
	$obQuery->builder()->sort('ea.short_description',$order);
elseif($by=='with_video')
	$obQuery->builder()->sort('ea.with_video',$order);
elseif($by=='from_time')
	$obQuery->builder()->sort('ea.time_from',$order);
elseif($by=='to_time')
	$obQuery->builder()->sort('ea.time_to',$order);
elseif($by=='duration')
	$obQuery->builder()->sort('ea.duration',$order);
elseif($by=='type')
	$obQuery->builder()->sort('type.name',$order);
elseif($by=='hall')
	$obQuery->builder()->sort('eh.name',$order);
elseif($by=='section')
	$obQuery->builder()->sort('es.name',$order);

$obQuery->builder()->group('ea.id');
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

	$sVideo = ($arRecord['video'] == 1)?'да':'нет';
	$sTimeFrom = date('H:i',strtotime($arRecord['time_from']));
	$sTimeTo = date('H:i',strtotime($arRecord['time_to']));

	$row->AddViewField("ID",$arRecord['id']);
	$row->AddViewField("NAME",$arRecord['name']);
	$row->AddViewField("SHORT_DESCRIPTION",$arRecord['description']);
	$row->AddViewField("WITH_VIDEO",$sVideo);
	$row->AddViewField("FROM_TIME",$sTimeFrom);
	$row->AddViewField("TO_TIME",$sTimeTo);
	$row->AddViewField("DURATION",$arRecord['duration']);
	$row->AddViewField("TYPE",$arRecord['type']);
	$row->AddViewField("HALL",$arRecord['hall']);
	$row->AddViewField("SECTION",$arRecord['section']);

	$arActions = Array();
	$arActions[] = array(
		"DEFAULT"=>"Y",
		"ICON"=>"edit",
		"TITLE"=>GetMessage("ESTELIFE_EDIT_ALT"),
		"ACTION"=>$lAdmin->ActionRedirect("estelife_event_activities_edit.php?lang=".LANGUAGE_ID."&ID=$f_ID"),
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
	"LINK"=>"estelife_event_activities_edit.php?lang=".LANG,
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
				GetMessage("ESTELIFE_F_TITLE"),
				GetMessage("ESTELIFE_F_SHORT_DESCRIPTION"),
				GetMessage("ESTELIFE_F_WITH_VIDEO"),
				GetMessage("ESTELIFE_F_TYPE"),
				GetMessage("ESTELIFE_F_HALL"),
				GetMessage("ESTELIFE_F_SECTION"),
			)
		);
		$oFilter->Begin();
		?>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_TITLE")?></td>
			<td><input type="text" name="find_name" size="47" value="<?echo htmlspecialcharsbx($find_name)?>"></td>
		</tr>
		<tr>
			<td><b><?echo GetMessage("ESTELIFE_F_SHORT_DESCRIPTION")?></b></td>
			<td><input type="text" name="find_description" size="47" value="<?echo htmlspecialcharsbx($find_description)?>"></td>
		</tr>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_WITH_VIDEO")?></td>
			<td>
				<select name="find_with_video" value="<?echo htmlspecialcharsbx($find_with_video)?>">
					<option value="0"><?echo GetMessage("ESTELIFE_NOT_IMPORTANT")?></option>
					<option value="yes">Да</option>
					<option value="no">Нет</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_TYPE")?></td>
			<td>
				<select name="find_type_id" value="<?echo htmlspecialcharsbx($find_type_id)?>">
					<option value="0"><?echo GetMessage("ESTELIFE_NOT_IMPORTANT")?></option>
					<?php if(!empty($arFilterData['types'])): ?>
						<?php foreach($arFilterData['types'] as $arType): ?>
							<option value="<?=$arType['id']?>"><?=$arType['name']?></option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_HALL")?></td>
			<td>
				<select name="find_hall_id" value="<?echo htmlspecialcharsbx($find_hall_id)?>">
					<option value="0"><?echo GetMessage("ESTELIFE_NOT_IMPORTANT")?></option>
					<?php if(!empty($arFilterData['halls'])): ?>
						<?php foreach($arFilterData['halls'] as $arHall): ?>
							<option value="<?=$arHall['id']?>"><?=$arHall['name']?></option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_SECTION")?></td>
			<td>
				<select name="find_section_id" value="<?echo htmlspecialcharsbx($find_section_id)?>">
					<option value="0"><?echo GetMessage("ESTELIFE_NOT_IMPORTANT")?></option>
					<?php if(!empty($arFilterData['sections'])): ?>
						<?php foreach($arFilterData['sections'] as $arSection): ?>
							<option value="<?=$arSection['id']?>"><?=$arSection['name']?></option>
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