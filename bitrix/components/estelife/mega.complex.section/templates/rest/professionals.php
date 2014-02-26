<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

$APPLICATION->IncludeComponent(
	"estelife:professionals.list",
	"ajax",
	array(
		"PAGE_COUNT" => 12,
	),
	false
);