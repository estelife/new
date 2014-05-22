#!/usr/bin/php
<?php
$_SERVER["DOCUMENT_ROOT"] = str_replace('/cron','', __DIR__);
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
@set_time_limit(0);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/estelife/prolog.php");

CModule::IncludeModule('estelife');

$obQuery = \core\database\VDatabase::driver()->createQuery();
$obBuilder = $obQuery->builder();
$obBuilder->from('estelife_apparatus')
	->field('id')
	->field('detail_text')
	->field('registration')
	->field('action')
	->field('undesired')
	->field('evidence')
	->field('contra')
	->field('advantages')
	->field('func')
	->field('security')
	->field('procedure')
	->field('protocol')
	->field('specs')
	->field('equipment')
	->field('effect')
	->field('specialist')
	->field('patient');
$arApps = $obQuery->select()->all();

foreach ($arApps as $arValue) {
	$arValue['detail_text'] = nl2br($arValue['detail_text']);
	$arValue['registration'] = nl2br($arValue['registration']);
	$arValue['action'] = nl2br($arValue['action']);
	$arValue['undesired'] = nl2br($arValue['undesired']);
	$arValue['evidence'] = nl2br($arValue['evidence']);
	$arValue['contra'] = nl2br($arValue['contra']);
	$arValue['advantages'] = nl2br($arValue['advantages']);
	$arValue['func'] = nl2br($arValue['func']);
	$arValue['security'] = nl2br($arValue['security']);
	$arValue['procedure'] = nl2br($arValue['procedure']);
	$arValue['protocol'] = nl2br($arValue['protocol']);
	$arValue['specs'] = nl2br($arValue['specs']);
	$arValue['equipment'] = nl2br($arValue['equipment']);
	$arValue['effect'] = nl2br($arValue['effect']);
	$arValue['specialist'] = nl2br($arValue['specialist']);
	$arValue['patient'] = nl2br($arValue['patient']);

	$nId = $arValue['id'];
	unset($arValue['id']);

	$obBuilder = $obQuery->builder();
	$obBuilder->from('estelife_apparatus');
	$obBuilder->values($arValue);
	$obBuilder->filter()->_eq('id', $nId);
	$obQuery->update();
}

$obBuilder = $obQuery->builder();
$obBuilder->from('estelife_pills')
	->field('id')
	->field('detail_text')
	->field('registration')
	->field('action')
	->field('undesired')
	->field('evidence')
	->field('structure')
	->field('effect')
	->field('form')
	->field('contra')
	->field('usage')
	->field('storage')
	->field('advantages')
	->field('area')
	->field('security')
	->field('mix')
	->field('protocol')
	->field('specs')
	->field('specialist')
	->field('effect');
$arApps = $obQuery->select()->all();

foreach ($arApps as $arValue) {
	$arValue['detail_text'] = nl2br($arValue['detail_text']);
	$arValue['registration'] = nl2br($arValue['registration']);
	$arValue['action'] =  nl2br($arValue['action']);
	$arValue['undesired'] = nl2br($arValue['undesired']);
	$arValue['evidence'] = nl2br($arValue['evidence']);
	$arValue['structure'] = nl2br($arValue['structure']);
	$arValue['effect'] = nl2br($arValue['effect']);
	$arValue['form'] = nl2br($arValue['form']);
	$arValue['contra'] = nl2br($arValue['contra']);
	$arValue['usage'] = nl2br($arValue['usage']);
	$arValue['storage'] = nl2br($arValue['storage']);
	$arValue['advantages'] = nl2br($arValue['advantages']);
	$arValue['area'] = nl2br($arValue['area']);
	$arValue['security'] = nl2br($arValue['security']);
	$arValue['mix'] = nl2br($arValue['mix']);
	$arValue['protocol'] = nl2br($arValue['protocol']);
	$arValue['specs'] = nl2br($arValue['specs']);
	$arValue['specialist'] = nl2br($arValue['specialist']);
	$arValue['effect'] = nl2br($arValue['effect']);

	$nId = $arValue['id'];
	unset($arValue['id']);

	$obBuilder = $obQuery->builder();
	$obBuilder->from('estelife_pills');
	$obBuilder->values($arValue);
	$obBuilder->filter()->_eq('id', $nId);
	$obQuery->update();
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");