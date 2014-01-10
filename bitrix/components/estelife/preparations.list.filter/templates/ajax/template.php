<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

echo json_encode(array(
	'countries'=>(isset($arResult['countries'])) ? $arResult['countries'] : array(),
	'filter'=>$arResult['filter']
));
