<?php
use core\exceptions\VFormException;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if($_SERVER['REQUEST_METHOD']=='POST'){
	try{
		$obError=new VFormException();

		if (isset($_POST['email']) && \core\types\VString::isEmail($_POST['email']))
			$arResult['email']=trim(strip_tags($_POST['email']));
		else
			$obError->setFieldError('E-mail указан неверно.','email');

		$arResult['type']=intval($_POST['type']);
		$obError->raise();

		//Дальше сохранение.
		if (!VUser::setSubscribe($arResult['type'], $arResult['email'], 0))
			$arResult['errors']['email']='Ошибка подписки';
		else
			$arResult['success']=true;

	}catch(VFormException $e){
		$arResult['errors']=$e->getFieldErrors();
	}
}


$this->IncludeComponentTemplate();