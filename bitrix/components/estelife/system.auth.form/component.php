<?php

use core\types\VString;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arParamsToDelete = array(
	"login",
	"logout",
	"register",
	"forgot_password",
	"change_password",
	"confirm_registration",
	"confirm_code",
	"confirm_user_id",
	"logout_butt",
	"auth_service_id",
);

$sCurrentUrl = $APPLICATION->GetCurPageParam("", $arParamsToDelete);

$arResult["backurl"] = $sCurrentUrl;
$arResult['errors'] = false;

if(!$USER->IsAuthorized()){
	global $USER;
	$arResult["form_type"] = "login";

	$arResult["STORE_PASSWORD"] = COption::GetOptionString("main", "store_password", "Y") == "Y" ? "Y" : "N";
//	$arResult["NEW_USER_REGISTRATION"] = COption::GetOptionString("main", "new_user_registration", "N") == "Y" ? "Y" : "N";
//
//	if(defined("AUTH_404"))
//		$arResult["AUTH_URL"] = htmlspecialcharsback(POST_FORM_ACTION_URI);
//	else
//		$arResult["AUTH_URL"] = $APPLICATION->GetCurPageParam("login=yes", array_merge($arParamsToDelete, array("logout_butt", "backurl")));

	$arParams["register_url"] = ($arParams["register_url"] <> ''? $arParams["register_url"] : $sCurrentUrl);
	$arParams["forgot_password_url"] = ($arParams["forgot_password_url"] <> ''? $arParams["forgot_password_url"] : $arParams["register_url"]);

	$sUrl = urlencode($APPLICATION->GetCurPageParam("", array_merge($arParamsToDelete, array("backurl"))));

	$custom_reg_page = COption::GetOptionString('main', 'custom_register_page');
	$arResult["AUTH_REGISTER_URL"] = ($custom_reg_page <> ''? $custom_reg_page : $arParams["register_url"].(strpos($arParams["register_url"], "?") !== false? "&" : "?")."register=yes&backurl=".$sUrl);
	$arResult["AUTH_FORGOT_PASSWORD_URL"] = $arParams["FORGOT_PASSWORD_URL"].(strpos($arParams["FORGOT_PASSWORD_URL"], "?") !== false? "&" : "?")."forgot_password=yes&backurl=".$sUrl;

	if (isset($_POST['login']) && !empty($_POST['login']))
		$sLogin=trim(strip_tags($_POST['login']));
	else
		$arResult['errors']['login']='Укажите логин';

	if (isset($_POST['password']) && !empty($_POST['password']))
		$sPass=trim(strip_tags($_POST['password']));
	else
		$arResult['errors']['login']='Укажите логин';

	if (isset($_POST['remember']))
		$bRemember='Y';
	else
		$bRemember='N';

	//Поиск пользователя по email
	$USER = new CUser;
	$bAuth=false;

	if (!VString::isEmail($sLogin)){
		$bAuth = $USER->Login($sLogin, $sPass, $bRemember);
	}else{
		$arUser = CUser::GetList($by="id", $order="desc", array('EMAIL'=>$sLogin))->Fetch();
		if (!empty($arUser)){
			$bAuth = $USER->Login($arUser['LOGIN'], $sPass, $bRemember);
		}
	}


	var_dump($bAuth); die();

//	if ($bAuth==true)
//		LocalRedirect($arResult["backurl"]);
}
else
{
	$arResult["form_type"] = "logout";

	$arResult["auth_url"] = $currentUrl;
	$arResult["profile_url"] = $arParams["profile_url"].(strpos($arParams["profile_url"], "?") !== false? "&" : "?")."backurl=".urlencode($sCurrentUrl);

	$arRes = array();
	foreach($arResult as $key=>$value)
	{
		$arRes[$key] = htmlspecialcharsbx($value);
		$arRes['~'.$key] = $value;
	}
	$arResult = $arRes;

	$arResult["user_name"] = htmlspecialcharsEx($USER->GetFormattedName(false, false));
	$arResult["user_login"] = htmlspecialcharsEx($USER->GetLogin());

	$arResult["GET"] = array();
	foreach($_GET as $vname=>$vvalue)
		if(!is_array($vvalue) && $vname!="backurl" && $vname != "login" && $vname != "auth_service_id")
			$arResult["GET"][htmlspecialcharsbx($vname)] = htmlspecialcharsbx($vvalue);
}

$this->IncludeComponentTemplate();
