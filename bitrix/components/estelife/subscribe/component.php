<?php
use core\exceptions\VException;
use core\types\VArray;
use core\types\VString;
use subscribe\owners\VCreator;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

CModule::IncludeModule("estelife");
$arResult=array();

if ($_SERVER['REQUEST_METHOD']=='POST'){
	try{
		$obPost=new VArray($_POST);
		$obParams=new VArray($obPost->one('params',array()));

		if ($obPost->blank('email'))
			throw new VException("Укажите email");

		if (!VString::isEmail($obPost->one('email')))
			throw new VException("Email введен некорректно");

		if ($obPost->blank('type'))
			throw new VException("Тип не указан");

		$nElementId=$obParams->blank('always') ? intval($obParams->one('id')) : 0;
		$sEmail=strip_tags(trim($obPost->one('email')));
		$nType=intval($obPost->one('type'));
		$arFilter=array();

		if(!$obParams->blank('city_id'))
			$arFilter['city']=intval($obParams->one('city_id'));

		$obOwner=VCreator::getByEmail($sEmail);
		$obOwner->setEvent($nType,$nElementId,$arFilter);

		$arResult['complete']=1;
		$arResult['error']=null;
	}catch(VException $e){
		$arResult['error'] = 'При оформлении подписки произошла ошибка';
		$arResult['complete']=0;
	}
}

$this->IncludeComponentTemplate();