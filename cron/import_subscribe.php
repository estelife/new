<?php
use core\database\VDatabase;
use subscribe\owners\VCreator;

$_SERVER["DOCUMENT_ROOT"] = realpath(__DIR__.'/../');
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
@set_time_limit(0);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/estelife/prolog.php");

CModule::IncludeModule('estelife');

$obQuery = VDatabase::driver()
	->createQuery();
$obQuery->builder()
	->from('estelife_subscribe');
$arData = $obQuery
	->select()
	->all();

if(!empty($arData)){
	foreach($arData as $arValue){
		$obOwner=VCreator::getByEmail($arValue['email']);
		$obOwner->setEvent($arValue['type'],0,$arValue['filter']);
	}
}

$obQuery->builder()
	->from('estelife_subscribe_owners')
	->value('active',1);
$obQuery->update();

$obQuery->builder()
	->from('estelife_subscribe_events')
	->value('active',1);
$obQuery->update();

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");