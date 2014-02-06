<?
use core\exceptions\VException;
use core\exceptions\VFormException;
use core\types\VString;

if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

if (isset($_REQUEST['backurl']) && !empty($_REQUEST['backurl']))
	$arResult["backurl"] = trim(strip_tags($_REQUEST['backurl']));
else
	$arResult["backurl"]='/';

if ($USER->IsAuthorized())
	LocalRedirect($arResult["backurl"]);

global $USER;
$arResult["values"] = array();
$bRegisterDone = false;

// register user
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["register"]) && !$USER->IsAuthorized()){
	try{
		$obError=new VFormException();

		if (isset($_POST['name']) && !empty($_POST['name']))
			$arResult['values']['USER_NAME']=trim(strip_tags($_POST['name']));
		else
			$obError->setFieldError('Укажите ФИО.','name');

		if (isset($_POST['login']) && VString::isEmail($_POST['login']))
			$arResult['values']['LOGIN']=$arResult['values']['EMAIL']=trim(strip_tags($_POST['login']));
		else
			$obError->setFieldError('E-mail указан неверно или уже существует.','login');

		if (isset($_POST['password']) && VString::isPassword($_POST['password']))
			$arResult['values']['PASSWORD']=$arResult['values']['CONFIRM_PASSWORD']=trim(strip_tags($_POST['password']));
		else
			$obError->setFieldError('Пароль указан неверно.','password');

		$obCheckLogin = CUser::GetList($b, $o, array("=EMAIL" => $arResult["values"]["EMAIL"]));
		if($obCheckLogin->Fetch())
			$obError->setFieldError('E-mail указан неверно или уже существует.','login');

		$obError->raise();

		//Регистрация пользователя
		$bConfirmReq = COption::GetOptionString("main", "new_user_registration_email_confirmation", "N") == "Y";

		$arResult['values']["CHECKWORD"] = randString(8);
		$arResult['values']["~CHECKWORD_TIME"] = $DB->CurrentTimeFunction();
		$arResult['values']["ACTIVE"] = $bConfirmReq? "N": "Y";
		$arResult['values']["CONFIRM_CODE"] = $bConfirmReq? randString(8): "";
		$arResult['values']["LID"] = SITE_ID;

		$arResult['values']["USER_IP"] = $_SERVER["REMOTE_ADDR"];
		$arResult['values']["USER_HOST"] = @gethostbyaddr($REMOTE_ADDR);
		$arResult['values']["BACK_URL"] = $arResult["backurl"];

		$nDefGroup = COption::GetOptionString("main", "new_user_registration_def_group", "");
		if($nDefGroup != "")
			$arResult['values']["GROUP_ID"] = explode(",", $nDefGroup);

		$user = new CUser();
		$ID = $user->Add($arResult["values"]);

		if (intval($ID) > 0){
			$bRegisterDone = true;
			$arResult['values']["USER_ID"] = $ID;
			$arEventFields = $arResult['values'];
			unset($arEventFields["PASSWORD"]);
			unset($arEventFields["CONFIRM_PASSWORD"]);

			CEvent::Send("NEW_USER", SITE_ID, $arEventFields);
			if($bConfirmReq)
				CEvent::Send("NEW_USER_CONFIRM", SITE_ID, $arEventFields);
		}else
			throw new \request\exceptions\VRequest($user->LAST_ERROR);

	}catch(VFormException $e){
		$arResult['errors']=$e->getFieldErrors();
	}catch(VException $e){
		$arResult['errors']=array(
			'message'=>$e->getMessage(),
			'code'=>$e->getCode()
		);
	}
}

$this->IncludeComponentTemplate();