<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

echo json_encode(array(
	'active'=>$arResult['cities']['active'],
	'passive'=>$arResult['cities']['passive'],
	'city'=>$arResult['city'],
));