<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

echo json_encode(array(
	'countries'=>(isset($arResult['countries'])) ? $arResult['countries'] : array(),
	'types'=>(isset($arResult['types'])) ? $arResult['types'] : array(),
	'filter'=>$arResult['filter'],
	'empty'=>$arResult['empty'],
));
