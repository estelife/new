<?php
use core\database\VDatabase;
use core\exceptions\VException;
use core\types\VString;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$arData=array();
if (isset($_REQUEST) && !empty($_REQUEST)){
	CModule::IncludeModule("estelife");
	try{
	//обработка формы
	if (empty($_POST['email']))
		throw new VException("Укажите email");
	else
		$arData['email']=strip_tags(trim($_POST['email']));

	if (!VString::isEmail($arData['email']))
		throw new VException("Email введен некорректно");

	if (isset($_POST['always']) && intval($_POST['always'])==1)
		$arData['filter'] = serialize($_POST['params']['city_id']);
	else{
		if (!empty($_POST['params']))
			$arData['filter'] = serialize($_POST['params']);
	}
	$arData['active'] = 1;

	if (empty($_POST['type']))
		throw new VException("Тип не указан");
	else
		$arData['type'] = $_POST['type'];


			$nSubsInsert = subscribe\VUser::setSubscribe($_POST['type'],$_POST['email'],$_POST['always'],$arData['filter']);


	if ($nSubsInsert>0)
		$arResult['complete']=1;
	else
		$arResult['complete']=0;

	$arResult['error']=null;
	}catch(VException $e){
		$arResult['error'] = $e->getMessage();
		$arResult['complete']=0;
	}
}
$this->IncludeComponentTemplate();