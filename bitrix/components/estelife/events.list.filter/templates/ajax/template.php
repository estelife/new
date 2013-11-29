<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

echo json_encode(array(
	'filter'=>array(
		'countries'=>(isset($arResult['countries'])) ? $arResult['countries'] : array(),
		'date_from'=>$arResult['filter']['date_from']
	)
));
