<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

echo json_encode(array(
	'complete'=>array(
		'filter'=>array_filter($_GET,function($mValue){
			return !empty($mValue);
		}),
		'list'=>(!empty($arResult['org'])) ?
			array_values($arResult['org']) : array(),
		'nav'=>$arResult['nav']
	)
));