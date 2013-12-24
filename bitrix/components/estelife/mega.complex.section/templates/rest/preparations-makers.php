<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

$APPLICATION->IncludeComponent(
	"estelife:preparations-makers.list",
	"ajax",
	array(
		"PAGE_COUNT" => 10,
	),
	false
);