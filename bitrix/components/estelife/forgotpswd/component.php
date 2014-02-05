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

if ($USER->IsAuthorized())
	LocalRedirect($arResult["backurl"]);

$arResult['success']=false;
$arResult['change_password']=false;

//Смена пароля
if (isset($_GET['change_password']) && $_GET['change_password']=="yes"){
	try{
		$obError=new VFormException();

		if (isset($_GET['user_id']) && $_GET['user_id']>0)
			$nUserId=intval($_GET['user_id']);
		else
			throw new \request\exceptions\VRequest('Ссылка для смены пароля не действительна.');

		if (isset($_GET['user_checkword']) && !empty($_GET['user_checkword']))
			$sCheckword=trim(strip_tags($_GET['user_checkword']));
		else
			throw new \request\exceptions\VRequest('Ссылка для смены пароля не действительна.');

		if(!$arUser=CUser::GetByID($nUserId)->Fetch())
			throw new \request\exceptions\VRequest('Ссылка для смены пароля не действительна.');

		if (md5($arUser['CHECKWORD'].$arUser['LOGIN'].'estelifefpswd'.$arUser['EMAIL'])!=$sCheckword)
			throw new \request\exceptions\VRequest('Ссылка для смены пароля не действительна.');

		if (strtotime($arUser['TIMESTAMP_X'])-time()>=2*60*60)
			throw new \request\exceptions\VRequest('Ссылка для смены пароля не действительна.');

		$arResult['change_password']=true;
	}catch(VException $e){
		$arResult['global_errors']['confirm']=$e->getMessage();
	}
}

if(!$USER->IsAuthorized()){
	if($_SERVER['REQUEST_METHOD']=='POST'){
		$obError=new VFormException();

		if (isset($_POST['send_check_forgotpswd']) && intval($_POST['send_check_forgotpswd'])==1){
			try{
				//Отправка письма на смену пароля
				if (isset($_POST['email']) && VString::isEmail($_POST['email']))
					$arResult['values']['EMAIL']=trim(strip_tags($_POST['email']));
				else
					$obError->setFieldError('E-mail указан неверно.','email');

				$obError->raise();

				//Поиск пользователя в базе
				if ($arUser = CUser::GetList($by='id', $order='desc', array('EMAIL'=>$sEmail))->Fetch()){
					$arResult['values']['CHECKWORD']=md5($arUser['CHECKWORD'].$arUser['LOGIN'].'estelifefpswd'.$arUser['EMAIL']);
					$arResult['values']['USER_ID']=$arUser['ID'];

					if (CEvent::Send('USER_PASS_REQUEST', SITE_ID, $arResult['values'])){
						$nTime=date('Y-m-d H:i:s',time());
						if ($USER->Update($arUser['ID'], array('TIMESTAMP'=>$nTime)))
							$arResult['success']='На Ваш e-mail отправлено письмо с инструкциями смены пароля.';
					}
				}
			}catch(VFormException $e){
				$arResult['errors']=$e->getFieldErrors();
			}
		}elseif (isset($_POST['change_password']) && intval($_POST['change_password'])==1){
			try{
				//Смена пароля
				if (isset($_POST['pswd']) && VString::isPassword($_POST['pswd']))
					$arResult['VALUES']['PASSWORD']=trim(strip_tags($_POST['pswd']));
				else
					$obError->setFieldError('Пароль указан неверно.','pswd');

				if (isset($_POST['c_pswd']) && VString::isPassword($_POST['c_pswd']))
					$arResult['VALUES']['CONFIRM_PASSWORD']=trim(strip_tags($_POST['c_pswd']));
				else
					$obError->setFieldError('Подтверждение пароля указано неверно.','pswd');

				if ($arResult['VALUES']['CONFIRM_PASSWORD']!=$arResult['VALUES']['PASSWORD'])
					$obError->setFieldError('Подтверждение пароля и пароль не совпадают.','pswd');

				$obError->raise();

				$USER = new CUser;
				$USER->Update($arUser['ID'], $arResult['VALUES']);
			}catch(VFormException $e){
				$arResult['global_errors']=$e->getFieldErrors();
			}
		}
	}
}

$this->IncludeComponentTemplate();
