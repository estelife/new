<?php
CModule::IncludeModule('estelife');
use core\exceptions\VException;
use core\exceptions\VFormException;
use core\types\VString;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (isset($_REQUEST['backurl']) && !empty($_REQUEST['backurl']))
	$arResult["backurl"] = trim(strip_tags($_REQUEST['backurl']));
else
	$arResult["backurl"]='/';

global $USER;
$USER = new CUser;

if(isset($_GET['logout']) && $_GET['logout']=='yes'){
	CUser::Logout();
	LocalRedirect($arResult['backurl']);
}

if ($USER->IsAuthorized())
	LocalRedirect($arResult['backurl']);

//Конфирм регистарции
if (isset($_GET['confirm_registration']) && $_GET['confirm_registration']=='yes'){
	try{
		if ($_GET['confirm_user_id']>0)
			$nUserId=intval($_GET['confirm_user_id']);
		else
			throw new \request\exceptions\VRequest('Ссылка для подтверждения регистрации не действительна.');

		if (!empty($_GET['confirm_code']))
			$sConfirmCode=trim(strip_tags($_GET['confirm_code']));
		else
			throw new \request\exceptions\VRequest('Ссылка для подтверждения регистрации не действительна.');

		if(!$arUser = CUser::GetByID($nUserId)->Fetch())
			throw new \request\exceptions\VRequest('Ссылка для подтверждения регистрации не действительна.');

		if ($arUser['ACTIVE']=='Y')
			throw new \request\exceptions\VRequest('Регистрация пользователя уже подтверждена.');

		if ($arUser['CONFIRM_CODE']!=$sConfirmCode)
			throw new \request\exceptions\VRequest('Ссылка для подтверждения регистрации не действительна.');

		$obError->raise();

		if (!$USER->Update($arUser["ID"], array("ACTIVE" => "Y", "CONFIRM_CODE" => "")))
			throw new \request\exceptions\VRequest('Ошибка подтверждения регистрации. Обратитесь к администратору.');
		else{
			if($USER->Authorize($arUser["ID"]))
				LocalRedirect($arResult["backurl"]);
			else
				throw new \request\exceptions\VRequest('Ошибка авторизации пользователя после подтверждения регистрации. Обратитесь к администратору.');
		}
	}catch(VException $e){
		$arResult['errors']['confirm']=$e->getMessage();
	}
}

//Авторизация
if(!$USER->IsAuthorized()){
	if($_SERVER['REQUEST_METHOD']=='POST'){
		try{
			$obError=new VFormException();

			if (isset($_POST['login']) && !empty($_POST['login']))
				$sLogin=trim(strip_tags($_POST['login']));
			else
				$obError->setFieldError('Неверный логин или пароль.','auth');

			if (isset($_POST['password']) && !empty($_POST['password']))
				$sPass=trim(strip_tags($_POST['password']));
			else
				$obError->setFieldError('Неверный логин или пароль.','auth');

			if (isset($_POST['remember']) && intval($_POST['remember'])==1)
				$bRemember='Y';
			else
				$bRemember='N';

			$bAuth=false;

			if (!VString::isEmail($sLogin)){
				$bAuth=$USER->Login($sLogin, $sPass, $bRemember);
			}else{
				$arUser = CUser::GetList($by="id", $order="desc", array('EMAIL'=>$sLogin))->Fetch();
				if (!empty($arUser)){
					$bAuth=$USER->Login($arUser['LOGIN'], $sPass, $bRemember);
				}
			}
			if ($bAuth['TYPE']=='ERROR')
				$obError->setFieldError($bAuth['MESSAGE'],'auth');

			$obError->raise();

			if ($bAuth==true)
				LocalRedirect($arResult["backurl"]);


		}catch(VFormException $e){
			$arResult['errors']=$e->getFieldErrors();
		}
	}
}

$this->IncludeComponentTemplate();
