<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule('estelife');

$arResult=array(
	'directions' => array(
		"ns" => "novosti",
		"pt" => "podcast",
		"ar" => "articles",
		"pr" => "promotions",
		"cl" => "clinics",
		"am" => "apparatuses-makers",
		"pm" => "preparations-makers",
		"ap" => "apparatuses",
		"ps" => "preparations",
		"tc" => "training-centers",
		"tr" => "trainings",
		"ev" => "events",
		"sp" => "sponsors",
		"sh" => "search",
	),
	'current_city' => \geo\VGeo::getInstance()->getGeo()
);

if(isset($arParams['filter'])){
	$arTemp=array();

	if(is_array($arParams['filter'])){
		foreach($arParams['filter'] as $sKey)
			if(isset($arResult[$sKey]))
				$arTemp[$sKey]=$arResult[$sKey];


	}else if(isset($arResult[$arParams['filter']]))
		$arTemp=$arResult[$arParams['filter']];

	$arResult=$arTemp;
}

return $arResult;