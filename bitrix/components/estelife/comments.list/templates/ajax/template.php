<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

bitrix\ERESULT::$DATA[bitrix\ERESULT::$KEY] = $arResult;
//echo json_encode(array(
//	'comments'=>$arResult,
//));