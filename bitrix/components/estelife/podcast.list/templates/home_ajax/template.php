<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

HOME::$DATA[HOME::$KEY]=array(
	'SECTION_NAME'=>$arResult['SECTION_NAME'],
	'FIRST'=>$arResult['FIRST'],
	'ELEMENTS'=>$arResult['ELEMENTS']
);