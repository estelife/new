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
	$arValue['detail_text'] = htmlentities(nl2br(html_entity_decode($arValue['detail_text'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['registration'] = htmlentities(nl2br(html_entity_decode($arValue['registration'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['action'] = htmlentities(nl2br(html_entity_decode($arValue['action'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['undesired'] = htmlentities(nl2br(html_entity_decode($arValue['undesired'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['evidence'] = htmlentities(nl2br(html_entity_decode($arValue['evidence'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['contra'] = htmlentities(nl2br(html_entity_decode($arValue['contra'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['advantages'] = htmlentities(nl2br(html_entity_decode($arValue['advantages'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['func'] = htmlentities(nl2br(html_entity_decode($arValue['func'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['security'] = htmlentities(nl2br(html_entity_decode($arValue['security'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['procedure'] = htmlentities(nl2br(html_entity_decode($arValue['procedure'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['protocol'] = htmlentities(nl2br(html_entity_decode($arValue['protocol'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['specs'] = htmlentities(nl2br(html_entity_decode($arValue['specs'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['equipment'] = htmlentities(nl2br(html_entity_decode($arValue['equipment'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['effect'] = htmlentities(nl2br(html_entity_decode($arValue['effect'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['specialist'] = htmlentities(nl2br(html_entity_decode($arValue['specialist'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['patient'] = htmlentities(nl2br(html_entity_decode($arValue['patient'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');

	$nId = $arValue['id'];
	unset($arValue['id']);

	$obBuilder = $obQuery->builder();
	$obBuilder->from('estelife_apparatus');
	$obBuilder->values($arValue);
	$obBuilder->filter()->_eq('id', $nId);
	$obQuery->update();
}

$obBuilder = $obQuery->builder();
$obBuilder->from('estelife_preparations')
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
	$arValue['detail_text'] = htmlentities(nl2br(html_entity_decode($arValue['detail_text'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['registration'] = htmlentities(nl2br(html_entity_decode($arValue['registration'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['action'] =  htmlentities(nl2br(html_entity_decode($arValue['action'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['undesired'] = htmlentities(nl2br(html_entity_decode($arValue['undesired'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['evidence'] = htmlentities(nl2br(html_entity_decode($arValue['evidence'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['structure'] = htmlentities(nl2br(html_entity_decode($arValue['structure'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['effect'] = htmlentities(nl2br(html_entity_decode($arValue['effect'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['form'] = htmlentities(nl2br(html_entity_decode($arValue['form'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['contra'] = htmlentities(nl2br(html_entity_decode($arValue['contra'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['usage'] = htmlentities(nl2br(html_entity_decode($arValue['usage'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['storage'] = htmlentities(nl2br(html_entity_decode($arValue['storage'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['advantages'] = htmlentities(nl2br(html_entity_decode($arValue['advantages'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['area'] = htmlentities(nl2br(html_entity_decode($arValue['area'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['security'] = htmlentities(nl2br(html_entity_decode($arValue['security'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['mix'] = htmlentities(nl2br(html_entity_decode($arValue['mix'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['protocol'] = htmlentities(nl2br(html_entity_decode($arValue['protocol'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['specs'] = htmlentities(nl2br(html_entity_decode($arValue['specs'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['specialist'] = htmlentities(nl2br(html_entity_decode($arValue['specialist'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['effect'] = htmlentities(nl2br(html_entity_decode($arValue['effect'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');

	$nId = $arValue['id'];
	unset($arValue['id']);

	$obBuilder = $obQuery->builder();
	$obBuilder->from('estelife_preparations');
	$obBuilder->values($arValue);
	$obBuilder->filter()->_eq('id', $nId);
	$obQuery->update();
}

$obBuilder = $obQuery->builder();
$obBuilder->from('estelife_threads')
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
	$arValue['detail_text'] = htmlentities(nl2br(html_entity_decode($arValue['detail_text'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['registration'] = htmlentities(nl2br(html_entity_decode($arValue['registration'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['action'] =  htmlentities(nl2br(html_entity_decode($arValue['action'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['undesired'] = htmlentities(nl2br(html_entity_decode($arValue['undesired'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['evidence'] = htmlentities(nl2br(html_entity_decode($arValue['evidence'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['structure'] = htmlentities(nl2br(html_entity_decode($arValue['structure'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['effect'] = htmlentities(nl2br(html_entity_decode($arValue['effect'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['form'] = htmlentities(nl2br(html_entity_decode($arValue['form'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['contra'] = htmlentities(nl2br(html_entity_decode($arValue['contra'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['usage'] = htmlentities(nl2br(html_entity_decode($arValue['usage'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['storage'] = htmlentities(nl2br(html_entity_decode($arValue['storage'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['advantages'] = htmlentities(nl2br(html_entity_decode($arValue['advantages'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['area'] = htmlentities(nl2br(html_entity_decode($arValue['area'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['security'] = htmlentities(nl2br(html_entity_decode($arValue['security'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['mix'] = htmlentities(nl2br(html_entity_decode($arValue['mix'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['protocol'] = htmlentities(nl2br(html_entity_decode($arValue['protocol'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['specs'] = htmlentities(nl2br(html_entity_decode($arValue['specs'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['specialist'] = htmlentities(nl2br(html_entity_decode($arValue['specialist'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['effect'] = htmlentities(nl2br(html_entity_decode($arValue['effect'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');

	$nId = $arValue['id'];
	unset($arValue['id']);

	$obBuilder = $obQuery->builder();
	$obBuilder->from('estelife_threads');
	$obBuilder->values($arValue);
	$obBuilder->filter()->_eq('id', $nId);
	$obQuery->update();
}

$obBuilder = $obQuery->builder();
$obBuilder->from('estelife_implants')
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
	$arValue['detail_text'] = htmlentities(nl2br(html_entity_decode($arValue['detail_text'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['registration'] = htmlentities(nl2br(html_entity_decode($arValue['registration'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['action'] =  htmlentities(nl2br(html_entity_decode($arValue['action'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['undesired'] = htmlentities(nl2br(html_entity_decode($arValue['undesired'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['evidence'] = htmlentities(nl2br(html_entity_decode($arValue['evidence'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['structure'] = htmlentities(nl2br(html_entity_decode($arValue['structure'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['effect'] = htmlentities(nl2br(html_entity_decode($arValue['effect'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['form'] = htmlentities(nl2br(html_entity_decode($arValue['form'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['contra'] = htmlentities(nl2br(html_entity_decode($arValue['contra'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['usage'] = htmlentities(nl2br(html_entity_decode($arValue['usage'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['storage'] = htmlentities(nl2br(html_entity_decode($arValue['storage'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['advantages'] = htmlentities(nl2br(html_entity_decode($arValue['advantages'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['area'] = htmlentities(nl2br(html_entity_decode($arValue['area'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['security'] = htmlentities(nl2br(html_entity_decode($arValue['security'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['mix'] = htmlentities(nl2br(html_entity_decode($arValue['mix'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['protocol'] = htmlentities(nl2br(html_entity_decode($arValue['protocol'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['specs'] = htmlentities(nl2br(html_entity_decode($arValue['specs'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['specialist'] = htmlentities(nl2br(html_entity_decode($arValue['specialist'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');
	$arValue['effect'] = htmlentities(nl2br(html_entity_decode($arValue['effect'], ENT_QUOTES, 'utf-8')), ENT_QUOTES, 'utf-8');

	$nId = $arValue['id'];
	unset($arValue['id']);

	$obBuilder = $obQuery->builder();
	$obBuilder->from('estelife_implants');
	$obBuilder->values($arValue);
	$obBuilder->filter()->_eq('id', $nId);
	$obQuery->update();
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");