<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

$APPLICATION->IncludeComponent(
	"estelife:preparations.list",
	"ajax",
	array(
		"PAGE_COUNT" => 10,
		"TYPE"=>3,
		"COMPONENT"=>'list'
	),
	false
);