<?php
use core\exceptions\VFormException;
use core\exceptions\VException;
use core\types\VString;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("estelife");

global $USER,$DB;

$obProtocol = new \pay\VProtocol();
$arResult['service_id'] = 2;
$arResult['is_login'] = $USER->IsAuthorized();
$arResult['source_id'] = $obProtocol->getProjectId();
$arResult['project_id'] = $obProtocol->getSourceId();
$arResult['form_action'] = 'https://www.onlinedengi.ru/wmpaycheck.php';
$arResult['amount'] = 1000;

if($_SERVER['REQUEST_METHOD']=='POST'){
	try {
		if($arResult['is_login'])
			throw new VException('Только не авторизанный пользователь может совершать попытку регистрации');

		$nModeType = intval($_POST['mode_type']);
		$arUser = array();
		$obError=new VFormException();

		if (isset($_POST['name']) && !empty($_POST['name']))
			$arUser['NAME']=trim(strip_tags($_POST['name']));
		else
			$obError->setFieldError('Укажите ФИО.','name');

		if (isset($_POST['login']) && VString::isEmail($_POST['login']))
			$arUser['LOGIN']=$arUser['EMAIL']=trim(strip_tags($_POST['login']));
		else
			$obError->setFieldError('E-mail указан неверно или уже существует.','login');

		if (isset($_POST['password']) && VString::isPassword($_POST['password']))
			$arUser['PASSWORD']=$arUser['CONFIRM_PASSWORD']=trim(strip_tags($_POST['password']));
		else
			$obError->setFieldError('Пароль указан неверно.','password');

		$obCheckLogin = CUser::GetList($b, $o, array("=EMAIL" => $arUser["EMAIL"]));

		if($obCheckLogin->Fetch())
			$obError->setFieldError('E-mail указан неверно или уже существует.','login');

		$obError->raise();

		//Регистрация пользователя
		$bConfirmReq = COption::GetOptionString("main", "new_user_registration_email_confirmation", "N") == "Y";

		$arUser["CHECKWORD"] = randString(8);
		$arUser["~CHECKWORD_TIME"] = $DB->CurrentTimeFunction();
		$arUser["ACTIVE"] = $bConfirmReq ? "N": "Y";
		$arUser["CONFIRM_CODE"] = $bConfirmReq? randString(8): "";
		$arUser["LID"] = SITE_ID;

		$arUser["USER_IP"] = $_SERVER["REMOTE_ADDR"];
		$arUser["USER_HOST"] = @gethostbyaddr($REMOTE_ADDR);

		$nDefGroup = COption::GetOptionString("main", "new_user_registration_def_group", "");

		if($nDefGroup != "")
			$arUser["GROUP_ID"] = explode(",", $nDefGroup);

		$user = new CUser();
		$ID = $user->Add($arUser);
		$ID = intval($ID);

		if($ID > 0){
			$bRegisterDone = true;
			$arUser["USER_ID"] = $ID;
			$arEventFields = $arUser;
			unset($arEventFields["PASSWORD"]);
			unset($arEventFields["CONFIRM_PASSWORD"]);

			CEvent::Send("NEW_USER", SITE_ID, $arEventFields);

			if($bConfirmReq)
				CEvent::Send("NEW_USER_CONFIRM", SITE_ID, $arEventFields);
		}else
			throw new \request\exceptions\VRequest($user->LAST_ERROR);

		$arResult['nickname'] = $ID;

		$obSecure = new \pay\VSecure();
		$obSecure->createProtectedKey();
		$obSecure->createUserSecrete($arResult['nickname'],$arUser['EMAIL'],$arUser['NAME']);

		$arResult['user'] = $arUser;

		$obReceipt = \pay\VReceipt::create(
			$arResult['nickname'],
			$arResult['service_id'],
			$arResult['amount']
		);
		$arResult['receipt_id'] = $obReceipt->getReceiptId();

		$arQuery = array(
			'project'=>$arResult['project_id'],
			'source'=>$arResult['source_id'],
			'amount'=>$arResult['amount'],
			'nickname'=>$arResult['receipt_id'],
			'mode_type'=>$nModeType
		);
		LocalRedirect($arResult['form_action'].'?'.http_build_query($arQuery));
	} catch(VFormException $e) {
		$arResult['errors'] = $e->getFieldErrors();
	} catch(\pay\VReceiptEx $e){
		\notice\VNotice::registerError('Ошибка создания квитанции!', 'Пожалуйста, повторите попытку.');
	} catch(\core\exceptions\VException $e){
		\notice\VNotice::registerError('Ошибка регистрации', $e->getMessage());
	}
}

if($arResult['is_login']){
	$arResult['nickname'] = $USER->GetID();

	try {
		$obReceipt = \pay\VReceipt::getByUserService($arResult['nickname'],$arResult['service_id']);
		$arResult['receipt_id'] = $obReceipt->getReceiptId();

		if($obReceipt->getStatus() == \pay\VReceipt::COMPLETED)
			LocalRedirect('/education/');
	}catch(\pay\VReceiptEx $e){
		$obReceipt = \pay\VReceipt::create(
			$arResult['nickname'],
			$arResult['service_id'],
			$arResult['amount']
		);
		$arResult['receipt_id'] = $obReceipt->getReceiptId();
	}

	$obSecure = new \pay\VSecure();
	$obSecure->createProtectedKey();
	$obSecure->createUserSecrete($arResult['nickname'],$arUser['EMAIL'],$arUser['USER_NAME']);
}

$this->IncludeComponentTemplate();