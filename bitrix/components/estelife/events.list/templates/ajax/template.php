<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

echo json_encode(array(
	'complete'=>array(
		'filter'=>array_filter($_GET,function($mValue){
			return !empty($mValue);
		}),
		'list'=>(!empty($arResult['events'])) ?
			array_values($arResult['events']) : array(),
		'nav'=>$arResult['nav'],
		'block_color'=>'blue'
	)
));