<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

$arData=$arResult['company'];
$arData['training']=(isset($arResult['training'])) ?
	$arResult['training'] : array();
$arData['production']=(isset($arResult['production'])) ?
	$arResult['production'] : array();
echo json_encode(array(
	'item'=>$arData
));