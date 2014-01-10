<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

$APPLICATION->IncludeComponent(
	"estelife:preparations-makers.detail",
	"ajax",
	array(
		"PREFIX"=>$arResult['PREFIX'],
		'ID'=>$arResult['ID']
	),
	false
);