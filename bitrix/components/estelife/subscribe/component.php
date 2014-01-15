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
		$arData['filter'] = '';
	else{
		if (!empty($_POST['params']))
			$arData['filter'] = serialize($_POST['params']);
	}
	$arData['active'] = 1;

	if (empty($_POST['type']))
		throw new VException("Тип не указан");
	else
		$arData['type'] = $_POST['type'];

	$obSubscribe = VDatabase::driver();

	//Проверка на существование подписки
	$obQuery=$obSubscribe->createQuery();
	$obQuery->builder()->from('estelife_subscribe')
		->filter()
			->_eq('email', $arData['email'])
			->_eq('type', $arData['type']);
	$arSubs = $obQuery->select()->assoc();

	$obQuery=$obSubscribe->createQuery();
	$obQuery->builder()->from('estelife_subscribe')
		->value('email', $arData['email'])
		->value('type', $arData['type'])
		->value('filter', $arData['filter'])
		->value('active', $arData['active']);
	if (!empty($arSubs) && $arSubs['id']>0){
		$obQuery->builder()->filter()
			->_eq('id',$arSubs['id']);
		$obQuery->update();
		$nSubs = $arSubs['id'];
	}else{
		$nSubs = $obQuery->insert()->insertId();
	}

	if ($nSubs>0)
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