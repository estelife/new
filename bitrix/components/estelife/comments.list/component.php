<?php
use core\database\mysql\VFilter;
use core\database\VDatabase;
use core\exceptions\VFormException;
use core\types\VString;
use core\utils\forms\VForm;
use geo\VGeo;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

CModule::IncludeModule("estelife");
$arResult['auth']=false;

if ($USER->IsAuthorized())
	$arResult['auth']=true;


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

$arTypes=array_values($APPLICATION->IncludeComponent(
	'estelife:system-settings',
	'',
	array('filter'=>'types')
));
$arResult['type_string'] = $arTypes[$nType];

//Создание формы
$obForm = new VForm('comments', '#comment');
$obForm->createTokenField('form_token');
$obHidden = $obForm->createHiddenField('id');
$obHidden->setValue($nElementId);
$obHidden = $obForm->createHiddenField('type');
$obHidden->setValue($nType);
$obTextarea = $obForm->createTextareaField('comment', 'comment');
$obSubmit = $obForm->createSubmitField('send_comment');
$obSubmit->setAttributes(array('class'=>'submit'));
$arValues=array(
	'comment'=>'',
	'send_comment'=>'Комментировать'
);
$obForm->setValues($arValues);

//Обработка формы для добавления комментария
if ($_SERVER["REQUEST_METHOD"]=="POST" && $arResult['auth']){
	try{
		$obError=new VFormException();
		$obForm->setValues($_POST, array('comment'=>'isIsset, notEmpty, strlen[<=1000]'), array('comment'=>'strip_tags, trim', 'form_token'=>'strip_tags, trim', 'id'=>'intval', 'type'=>'intval'));
		$arValues = $obForm->getValues();

		if ($obForm->checkToken($arValues['form_token'])){
			if ($obComments->setComment($arValues['comment'], $arValues['type'], $arValues['id']))
				$arResult['success']=true;
		}
	}catch(VFormException $e){
		$arResult['error']=$e->getFieldErrors();
	}
}
$arResult['form'] = $obForm;

//Получение комментариев
$arResult['comments']=$obComments->getComments($nType, $nElementId, $nCount);
if (!empty($arResult['comments'])){
	foreach ($arResult['comments'] as &$val){
		$val['date_create']=date('H:i, d.m.Y', strtotime($val['date_create']));
		if (empty($val['name']))
			$val['name']=$val['login'];
		elseif (empty($val['last_name']))
			$val['name']=$val['name'];
		else
			$val['name']=$val['last_name'].' '.$val['name'];
	}
}

//Получение количества комментариев и количества пользователей
$nCountComments=$obComments->getCountComments($nType, $nElementId);
$nCountUsers=$obComments->getCountUsers($nType, $nElementId);
$arResult['count']=$nCountUsers.' пользовател'.VString::spellAmount($nCountUsers, 'ь,я,ей').' оставил'.VString::spellAmount($nCountUsers, ',и,и').' '.$nCountComments.' комментари'.VString::spellAmount($nCountComments, 'й,я,ев');

unset($nCountComments,$nCountUsers);

$this->IncludeComponentTemplate();
