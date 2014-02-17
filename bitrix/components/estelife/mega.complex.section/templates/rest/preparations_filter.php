<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

$nType=1;

if(isset(bitrix\ERESULT::$DATA['PTYPE']))
	$nType=intval(bitrix\ERESULT::$DATA['PTYPE']);

//if (isset($_REQUEST['ptype']))
//	$nType=intval($_REQUEST['ptype']);

$APPLICATION->IncludeComponent(
	"estelife:preparations.list.filter",
	"ajax",
	array(
		'TYPE'=>$nType,
	),
	false
);