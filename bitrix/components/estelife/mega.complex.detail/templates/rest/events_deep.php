<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

$arPath = explode('/', GetPagePath());
$arPath = array_splice($arPath, 1, -1);

preg_match('/^([a-z]{2})([0-9]+)$/',$arPath[0], $mathcesEvent);
$sEventHall =  preg_match_all('/(.*)-(.*)/', $arPath[1], $mathcesHall);

if($sEventHall == 1){
	$sHallDate = $mathcesHall[2][0];
	$sYear = date('Y');
	$sHallDate .='.'.$sYear;

	$APPLICATION->IncludeComponent(
		"estelife:events.hall",
		"",
		array(
			"HALL"=>$mathcesHall[1][0],
			'DATE'=>$sHallDate,
			'EVENT_ID'=>$mathcesEvent[2],
		),
		false
	);

}else{
	$APPLICATION->IncludeComponent(
		"estelife:events.program",
		"",
		array(
			'ID'=>$mathcesEvent[2],
		),
		false
	);
}
