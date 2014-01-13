<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

echo json_encode(array(
	'countries'=>(isset($arResult['countries'])) ? $arResult['countries'] : array(),
	'cities'=>(isset($arResult['cities'])) ? $arResult['cities'] : array(),
	'filter'=>$arResult['filter'],
	'empty'=>$arResult['empty']
));
