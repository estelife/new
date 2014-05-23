<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

global $APPLICATION;
$nClinicId = isset($_GET['clinic_id']) ? intval($_GET['clinic_id']) : 0;

$APPLICATION->IncludeComponent(
	"estelife:review.list",
	"ajax",
	array(
		'clinic_id' => $nClinicId
	),
	false
);