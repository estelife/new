#!/usr/bin/php
<?php
$sFileAddr=dirname(__FILE__);
$sFileAddr=str_replace('/cron','',$sFileAddr);
$_SERVER["DOCUMENT_ROOT"] = $sFileAddr;

$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
@set_time_limit(0);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/estelife/prolog.php");

CModule::IncludeModule('estelife');
CModule::IncludeModule('iblock');


const FIRST=1;

$obData = \core\database\VDatabase::driver();
$arUsers = subscribe\VUser::getAllUsers();

foreach($arUsers as $arUser){
	$sUserEmail = $arUser['email'];
	$nUserId = $arUser['id'];

	$arElements = subscribe\TrainigsFactory::getAll($arUser);
	//$arElements = subscribe\ClinicFactory::getAll($arUser);

	$arFields = subscribe\VDirector::TrainingsSend($arElements,$sUserEmail);
	//$arFields = subscribe\VDirector::ClinicSend($arElements,$sUserEmail);


	if(FIRST == 1){
		if(!empty($arFields)){
			CEvent::Send("SEND_SUBSCRIBE_TRAINING", "s1", $arFields,"Y",62);
			//CEvent::Send("SEND_SUBSCRIBE_CLINICS", "s1", $arFields,"Y",60);
		}
	}else{
		if(!empty($arFields)){
			CEvent::Send("SEND_SUBSCRIBE_TRAINING", "s1", $arFields,"Y",63);
			//CEvent::Send("SEND_SUBSCRIBE_CLINICS", "s1", $arFields,"Y",61);
		}
	}
}


require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
