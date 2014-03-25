<?php
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)
	die();

$arResult['show_form'] = isset($_GET['review_form']);
$arResult['clinic_id'] = isset($arParams['clinic_id']) ? intval($arParams['clinic_id']) : 0;

$this->IncludeComponentTemplate();