<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

echo json_encode(array(
	'cities'=>$arResult['cities'],
	'metro'=>(isset($arResult['metro'])) ? $arResult['metro'] : array(),
	'specializations'=>$arResult['specializations'],
	'service'=>(isset($arResult['service'])) ? $arResult['service'] : array(),
	'methods'=>(isset($arResult['methods'])) ? $arResult['methods'] : array(),
	'concreate'=>(isset($arResult['concreate'])) ? $arResult['concreate'] : array(),
	'filter'=>$arResult['filter'],
	'empty'=>$arResult['empty'],
));
