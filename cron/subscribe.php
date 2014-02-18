<?php
use subscribe\owners\VCreator;
use subscribe\events as events;

$_SERVER["DOCUMENT_ROOT"] = realpath(__DIR__.'/../');
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
@set_time_limit(0);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule('estelife');
CModule::IncludeModule('iblock');

$nDate=time()-86400*7;
$sType=isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : '';

switch($sType){
	case 'promotions':
		$obAggregator=new events\VPromotions();
		break;
	case 'training':
		$obAggregator=new events\VTrainings();
		break;
}

VCreator::getByDateSend($nDate)
	->getEvents($obAggregator);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");