<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

$APPLICATION->IncludeComponent(
	"estelife:preparations.detail",
	"ajax",
	array(
		"PREFIX"=>$arResult['PREFIX'],
		'ID'=>$arResult['ID']
	),
	false
);