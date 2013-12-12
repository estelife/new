<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

echo json_encode(array(
	'filter'=>array(
		'cities'=>(isset($arResult['cities'])) ? $arResult['cities'] : array()
	)
));
