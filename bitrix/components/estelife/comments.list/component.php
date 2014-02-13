<?php
use core\database\mysql\VFilter;
use core\database\VDatabase;
use core\exceptions\VFormException;
use core\types\VArray;
use core\types\VString;
use geo\VGeo;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

CModule::IncludeModule("estelife");

if (isset($arParams['count']))
	$nCount=intval($arParams['count']);
else
	$nCount=5;

if ($nCount==0){
	$nCount=false;
	$arResult['all']=true;
}

$arTypes=$arData=$APPLICATION->IncludeComponent(
	"estelife:system-settings","",
	array('filter'=>'types')
);


if (isset($arParams['type']) && !empty($arParams['type']))
	if (\core\validate\VValidate::isNumeric($arParams['type'])){
		$nType=intval($arParams['type']);
	}else{
		$arTypes=array_flip($arTypes);
		$nType=$arTypes[$arParams['type']];
	}

if (isset($arParams['element_id']) && !empty($arParams['element_id']))
	$nElementId=intval($arParams['element_id']);

$obComments=new \comments\VComment();
$arResult['element_id']=$nElementId;
$arResult['type']=$nType;

//Обработка формы для добавления комментария
if ($_SERVER["REQUEST_METHOD"]=="POST" && !empty($_POST['send_comment'])){
	try{
		$obError=new VFormException();
		if (isset($_POST['first_name']) && !empty($_POST['first_name'])){
			$sName=trim(strip_tags($_POST['first_name']));
		}else
			$obError->setFieldError('Укажите Ваше имя.','first_name');

		if (isset($_POST['last_name']) && !empty($_POST['last_name'])){
			$sLastName=trim(strip_tags($_POST['last_name']));
		}else
			$obError->setFieldError('Укажите Вашу фамилию.','last_name');

		if (isset($_POST['comment']) && !empty($_POST['comment'])){
			$sComment=trim(strip_tags($_POST['comment']));
		}else
			$obError->setFieldError('Укажите текст комментария.','comment');

		if (strlen($_POST['comment'])<=1000){
			$sComment=trim(strip_tags($_POST['comment']));
		}else
			$obError->setFieldError('Текст комментария слишком длинный.','comment');

		$obError->raise();

		if (\core\validate\VValidate::isNumeric($_POST['type'])){
			$nType=intval($_POST['type']);
		}else{
			$nType=$arTypes[$_POST['type']];
		}
		$nElementId=intval($_POST['id']);

		if ($obComments->setComment($sComment, $sName.' '.$sLastName, $nType, $nElementId))
			$arResult['success']=true;


	}catch(VFormException $e){
		$arResult['error']=$e->getFieldErrors();
	}
}

//Получение комментариев
$arResult['comments']=$obComments->getComments($nType, $nElementId, $nCount);
if (!empty($arResult['comments'])){
	foreach ($arResult['comments'] as &$val){
		$val['date_create']=date('H:i, d.m.Y', strtotime($val['date_create']));
	}
}

//Получение количества комментариев и количества пользователей
$nCountComments=$obComments->getCountComments($nType, $nElementId);
$nCountUsers=$obComments->getCountUsers($nType, $nElementId);
$arResult['count']=$nCountUsers.' пользовател'.VString::spellAmount($nCountUsers, 'ь,я,ей').' оставил'.VString::spellAmount($nCountUsers, ',и,и').' '.$nCountComments.' комментари'.VString::spellAmount($nCountComments, 'й,я,ев');

unset($nCountComments,$nCountUsers);

$this->IncludeComponentTemplate();