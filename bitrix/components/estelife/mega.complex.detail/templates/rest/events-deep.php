<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

$arPath = explode('/', GetPagePath());
$arPath = array_splice($arPath, 1, -1);

preg_match('/^([a-z]{2})([0-9]+)$/',$arPath[1], $mathcesEvent);
$sEventHall =  preg_match_all('/(.*)-(.*)/', $arPath[2], $mathcesHall);

if($sEventHall == 1){
	$sHallDate = $mathcesHall[2][0];
	$sYear = date('Y');
	$sHallDate .='.'.$sYear;

	$APPLICATION->IncludeComponent(
		"estelife:events.hall",
		"ajax",
		array(
			"HALL"=>$mathcesHall[1][0],
			'DATE'=>$sHallDate,
			'EVENT_ID'=>$arResult['ID'],
		),
		false
	);

}else{
	$APPLICATION->IncludeComponent(
		"estelife:events.program",
		"ajax",
		array(
			'EVENT_ID'=>$arResult['ID'],
		),
		false
	);
}
