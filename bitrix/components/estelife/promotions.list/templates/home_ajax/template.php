<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

HOME::$DATA[HOME::$KEY]=array(
	'akzii'=>$arResult['akzii'],
	'link'=>$arResult['link'],
	'city_name'=>$arResult['city']['NAME']
);