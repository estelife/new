<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

$APPLICATION->IncludeComponent(
	"estelife:promotions.list",
	"ajax",
	array(
		"PAGE_COUNT" => 21,
	),
	false
);