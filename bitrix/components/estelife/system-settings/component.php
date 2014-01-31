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
		"ex" => "experts",
	),
	'current_city' => \geo\VGeo::getInstance()->getGeo(),
	'types' => array(
		1=>"ns",
		2=>"pt",
		3=>"ar",
		4=>"pr",
		5=>"cl",
		6=>"am",
		7=>"pm",
		8=>"ap",
		9=>"ps",
		10=>"tc",
		11=>"tr",
		12=>"ev",
		13=>"sp",
		14=>"sh",
		15=>"ex",
	)
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