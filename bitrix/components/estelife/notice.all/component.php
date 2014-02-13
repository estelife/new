<?
use notice\VNotice;

if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

//Получаем все ошибки
$arResult['errors']=VNotice::getError();
VNotice::cleanError();

//Получаем все уведомления
$arResult['success']=VNotice::getSuccess();
VNotice::cleanSuccess();

$this->IncludeComponentTemplate();