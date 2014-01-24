<?php
use core\database\mysql\VFilter;
use core\database\VDatabase;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$sTableID = "tbl_estelife_request_list";
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
	"find_company_name",
	"find_company_type",
	"find_company_city",
	"find_user_name",
	"find_user_email",
	"find_user_phone",
	"find_verified"
);
$lAdmin->InitFilter($arFilterFields);

InitBVar($find_company_name_exact_match);
InitBVar($find_company_type_exact_match);
InitBVar($find_company_city_exact_match);
InitBVar($find_user_name_exact_match);
InitBVar($find_user_email_exact_match);
InitBVar($find_user_phone_exact_match);
InitBVar($find_verified_exact_match);

$arFilter = Array(
	"company_name"	=> $find_company_name,
	"company_type"	=> $find_company_type,
	"company_city"	=> $find_company_city,
	"user_name"		=>$find_user_name,
	"user_email"	=>$find_user_email,
	"user_phone"	=>$find_user_phone,
	"verified"		=>$find_verified
);


//====== TABLE HEADERS =========
$headers = array(
	array("id"=>"ID", "content"=>GetMessage("ESTELIFE_F_ID"), "sort"=>"id", "default"=>true),
	array("id"=>"CAMPANY_NAME", "content"=>GetMessage("ESTELIFE_F_CAMPANY_NAME"), "sort"=>"company_name", "default"=>true),
	array("id"=>"CAMPANY_TYPE", "content"=>GetMessage("ESTELIFE_F_CAMPANY_TYPE"), "sort"=>"company_type", "default"=>true),
	array("id"=>"CAMPANY_CITY", "content"=>GetMessage("ESTELIFE_F_CAMPANY_CITY"), "sort"=>"company_city", "default"=>true),
/*	array("id"=>"CAMPANY_ID", "content"=>GetMessage("ESTELIFE_F_CAMPANY_ID"), "sort"=>"campany_id", "default"=>true),*/
	array("id"=>"USER_NAME", "content"=>GetMessage("ESTELIFE_F_USER_NAME"), "sort"=>"user_name", "default"=>true),
	array("id"=>"USER_EMAIL", "content"=>GetMessage("ESTELIFE_F_USER_EMAIL"), "sort"=>"user_email", "default"=>true),
	array("id"=>"PHONE", "content"=>GetMessage("ESTELIFE_F_USER_PHONE"), "sort"=>"user_phone", "default"=>true),
	//array("id"=>"USER_ID", "content"=>GetMessage("ESTELIFE_F_USER_ID"), "sort"=>"user_id", "default"=>true),
	array("id"=>"VERIFIED", "content"=>GetMessage("VERIFIED"), "sort"=>"verified", "default"=>true),
);

$lAdmin->AddHeaders($headers);


//==== Здесь надо зафигачить генерацию списка ========
if(($arID = $lAdmin->GroupAction()) && check_bitrix_sessid()){
	foreach($arID as $ID){
		if(($ID = IntVal($ID))>0 && $_REQUEST['action']=='delete'){
			try{
				$obQuery = VDatabase::driver()->createQuery();
				$obQuery->builder()->from('estelife_requests')->filter()
					->_eq('id', $ID);
				$obQuery->delete();
			}catch(\core\database\exceptions\VCollectionException $e){}
		}else if(($ID = IntVal($ID))>0 && $_REQUEST['action']=='verified'){
			try{
				$obQuery = VDatabase::driver()->createQuery();
				$obQuery->builder()->from('estelife_requests')
					->value('verified',1);
				$obQuery->builder()->filter()
					->_eq('id', $ID);
				$obQuery->update();
			}catch(\core\database\exceptions\VCollectionException $e){}
		}
	}
}


$obQuery=VDatabase::driver()->createQuery();
$obQuery->builder()->from('estelife_requests','er');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('er','company_city')
	->_to('iblock_element','ID','ct')
	->_cond()->_eq('ct.IBLOCK_ID',16);
$obQuery->builder()
	->field('ct.NAME','city')
	->field('er.id','id')
	->field('er.company_name','company_name')
	->field('er.company_type','company_type')
	->field('er.company_id','company_id')
	->field('er.user_name','user_name')
	->field('er.user_email','user_email')
	->field('er.user_phone','user_phone')
	->field('er.user_id','user_id')
	->field('er.verified','verified');


$obQueryCities=VDatabase::driver()->createQuery();
$obQueryCities->builder()->from('iblock_element');
$obQueryCities->builder()->filter()->_eq('IBLOCK_ID',16);

$obResultCities=$obQueryCities->select();
$obResultCities=new CAdminResult(
	$obResultCities->bxResult(),
	$sTableID
);


$obFilter=$obQuery->builder()->filter();

if($_GET && $_GET['set_filter'] == 'Y'){

	if(!empty($arFilter['company_name']))
		$obFilter->_like('er.company_name',$arFilter['company_name'],VFilter::LIKE_BEFORE|VFilter::LIKE_AFTER);
	if(!empty($arFilter['company_type']))
		$obFilter->_eq('er.company_type',$arFilter['company_type']);
	if(!empty($arFilter['company_city']))
		$obFilter->_like('er.company_city',$arFilter['company_city'],VFilter::LIKE_BEFORE|VFilter::LIKE_AFTER);
	if(!empty($arFilter['user_name']))
		$obFilter->_like('er.user_name',$arFilter['user_name'],VFilter::LIKE_BEFORE|VFilter::LIKE_AFTER);
	if(!empty($arFilter['user_email']))
		$obFilter->_like('er.user_email',$arFilter['user_email'],VFilter::LIKE_BEFORE|VFilter::LIKE_AFTER);
	if(!empty($arFilter['user_phone']))
		$obFilter->_like('er.user_phone',$arFilter['user_phone'],VFilter::LIKE_BEFORE|VFilter::LIKE_AFTER);
	if(!empty($arFilter['verified'])){
		if($arFilter['verified'] == 'yes'){
			$obFilter->_eq('er.verified',1);
		}else if($arFilter['verified'] == 'no'){
			$obFilter->_eq('er.verified',0);
		}
	}
}

if($by=='company_name')
	$obQuery->builder()->sort('company_name',$order);
elseif($by=='company_type')
	$obQuery->builder()->sort('company_type',$order);
elseif($by=='company_city')
	$obQuery->builder()->sort('company_city',$order);
elseif($by=='user_name')
	$obQuery->builder()->sort('user_name',$order);
elseif($by=='user_email')
	$obQuery->builder()->sort('user_email',$order);
elseif($by=='user_phone')
	$obQuery->builder()->sort('user_phone',$order);
elseif($by=='user_id')
	$obQuery->builder()->sort('user_id',$order);
else
	$obQuery->builder()->sort('verified',$order);



$obResult=$obQuery->select();
$obResult=new CAdminResult(
	$obResult->bxResult(),
	$sTableID
);


$arTypes = array(
	1=>'Клиника',
	2=>'Продюсер',
	3=>'Учебный центр',
	4=>'Спонсор'
);

$arVerified = array(
	1=>"<span style='color:green;'>Да</a>",
	0=>"<a style='color:red;'>Нет</a>"
);


$obResult->NavStart();
$lAdmin->NavText($obResult->GetNavPrint(GetMessage('ESTELIFE_PAGES')));


while($arRecord=$obResult->Fetch()){
	$f_ID=$arRecord['id'];
	$row =& $lAdmin->AddRow($f_ID,$arRecord);

	$type = $arTypes[$arRecord['company_type']];
	$uid = $arRecord['user_id'];
	$userName = $arRecord['user_name'];

	if($uid !=0){
		$user = "<a href='/bitrix/admin/user_edit.php?ID=$uid'>$userName</a>";
	}else{
		$user = $userName;
	}
/*	if( !=0){
		echo 'ok';
	}*/


	$row->AddViewField("ID",$arRecord['id']);
	$row->AddViewField("CAMPANY_NAME", $arRecord['company_name']);
	$row->AddViewField("CAMPANY_TYPE", $type);
	$row->AddViewField("CAMPANY_CITY", $arRecord['city']);
	$row->AddViewField("USER_NAME", $user);
	$row->AddViewField("USER_EMAIL", $arRecord['user_email']);
	$row->AddViewField("PHONE", $arRecord['user_phone']);
	//$row->AddViewField("USER_ID", $arRecord['user_id']);
	$row->AddViewField("VERIFIED", $arVerified[$arRecord['verified']]);


	$arActions = Array();
	$arActions[] = array("ICON"=>"edit", "TITLE"=>GetMessage("ESTELIFE_VERIFIED_ALT"),"ACTION"=>"javascript: window.location='?lang=".LANGUAGE_ID."&action=verified&ID=$f_ID&".bitrix_sessid_get()."'","TEXT"=>GetMessage("ESTELIFE_VERIFIED_ALT"));
	$arActions[] = array("ICON"=>"delete", "TITLE"=>GetMessage("ESTELIFE_DELETE_ALT"),"ACTION"=>"javascript:if(confirm('".GetMessage("ESTELIFE_CONFIRM_DELETE")."')) window.location='?lang=".LANGUAGE_ID."&action=delete&ID=$f_ID&".bitrix_sessid_get()."'","TEXT"=>GetMessage("ESTELIFE_DELETE"));
	$row->AddActions($arActions);

}

$arCities = array();

while($arRecordCities=$obResultCities->Fetch()){
	$cityId = $arRecordCities['ID'];
	$cityName = $arRecordCities['NAME'];

	$arCities[$cityId] = $cityName;
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
	"verified"=>GetMessage("ESTELIFE_VERIFIED_ALT"),
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
				GetMessage("ESTELIFE_F_CAMPANY_NAME"),
				GetMessage("ESTELIFE_F_CAMPANY_TYPE"),
				GetMessage("ESTELIFE_F_CAMPANY_CITY"),
				GetMessage("ESTELIFE_F_USER_NAME"),
				GetMessage("ESTELIFE_F_USER_EMAIL"),
				GetMessage("ESTELIFE_F_USER_PHONE"),
				GetMessage("VERIFIED"),
			)
		);
		$oFilter->Begin();
		?>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_CAMPANY_CITY")?></td>
			<td>
				<select name="find_company_city" value="<?echo htmlspecialcharsbx($find_company_city)?>">
					<option value="0"><?echo GetMessage("ESTELIFE_NOT_IMPORTANT")?></option>
					<? foreach($arCities as $cKey=>$cValue){ ?>
						<option value="<?=$cKey;?>"><?echo $cValue;?></option>
					<? } ?>
				</select>
			</td>
		</tr>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_CAMPANY_TYPE")?></td>
			<td>
				<select name="find_company_type" value="<?echo htmlspecialcharsbx($find_company_type)?>">
					<option value="0"><?echo GetMessage("ESTELIFE_NOT_IMPORTANT")?></option>
					<? foreach($arTypes as $key=>$value){ ?>
						<option value="<?=$key;?>"><?echo $value;?></option>
					<? } ?>
				</select>
			</td>
		</tr>
		<tr>
			<td><?echo GetMessage("VERIFIED")?></td>
			<td>
				<select name="find_verified" value="<?echo htmlspecialcharsbx($find_verified)?>">
					<option value="0"><?echo GetMessage("ESTELIFE_NOT_IMPORTANT")?></option>
					<option value="yes">Да</option>
					<option value="no">Нет</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_CAMPANY_NAME")?></td>
			<td><input type="text" name="find_company_name" size="30" value="<?echo htmlspecialcharsbx($find_company_name)?>"></td>
		</tr>



		<tr>
			<td><?echo GetMessage("ESTELIFE_F_USER_NAME")?></td>
			<td><input type="text" name="find_user_name" size="30" value="<?echo htmlspecialcharsbx($find_user_name)?>"></td>
		</tr>

		<tr>
			<td><?echo GetMessage("ESTELIFE_F_USER_EMAIL")?></td>
			<td><input type="text" name="find_user_email" size="30" value="<?echo htmlspecialcharsbx($find_user_email)?>"></td>
		</tr>

		<tr>
			<td><?echo GetMessage("ESTELIFE_F_USER_PHONE")?></td>
			<td><input type="text" name="find_user_phone" size="30" value="<?echo htmlspecialcharsbx($find_user_phone)?>"></td>
		</tr>




		<?
		$oFilter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage()));
		$oFilter->End();
		#############################################################
		?>
	</form>

<?


$lAdmin->DisplayList();