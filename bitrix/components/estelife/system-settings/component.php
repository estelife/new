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
		"sp" => "organizers",
		"sh" => "search",
		"ex" => "experts",
		"au" => "auth",
		"rg" => "register",
		"fr" => "forgotpswd",
		"cm" => "comments",
		"th" => "threads",
		"im" => "implants",
		"pf" => "professionals"
	),
	'types' => array(
		0=>"",
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
		16=>"au",
		17=>"rg",
		18=>"fr",
		19=>"cm",
		20=>"th",
		21=>"im",
		22=>"pf"
	),
	'current_city' => \geo\VGeo::getInstance()->getGeo(),
	'cities'=>array(
		0=>array(
			'id'=>359,
			'name'=>'Москва'
		),
		1=>array(
			'id'=>358,
			'name'=>'Санкт-Петербург'
		),
		2=>array(
			'id'=>1512,
			'name'=>'Новосибирск'
		),
		3=>array(
			'id'=>1497,
			'name'=>'Екатеринбург'
		),
		4=>array(
			'id'=>1498,
			'name'=>'Нижний Новгород'
		),
		5=>array(
			'id'=>1493,
			'name'=>'Казань'
		),
		6=>array(
			'id'=>1502,
			'name'=>'Самара'
		),
		7=>array(
			'id'=>1496,
			'name'=>'Омск'
		),
		8=>array(
			'id'=>2587,
			'name'=>'Челябинск'
		),
		9=>array(
			'id'=>1501,
			'name'=>'Ростов-на-Дону'
		),
		10=>array(
			'id'=>1494,
			'name'=>'Уфа'
		),
		11=>array(
			'id'=>1507,
			'name'=>'Волгоград'
		),
		12=>array(
			'id'=>1510,
			'name'=>'Красноярск'
		),
		13=>array(
			'id'=>1488,
			'name'=>'Пермь'
		),
		14=>array(
			'id'=>1491,
			'name'=>'Воронеж'
		),
		15=>array(
			'id'=>1492,
			'name'=>'Иркутск'
		),
		16=>array(
			'id'=>2983,
			'name'=>'Тюмень'
		)
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