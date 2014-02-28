<?php
$arCities=array_values($APPLICATION->IncludeComponent(
	'estelife:system-settings',
	'',
	array('filter'=>'cities')
));

if (!empty($arCities)){
	$i=0;
	$k=0;
	foreach ($arCities as $val){
		if ($i%5==0){
			$k++;
		}
		$arResult['cities']['active'][$k][]=$val;
		$i++;
	}
}

$arResult['cities']['passive'] = array(
);

$arCity = \geo\VGeo::getInstance()->getGeo();
if (!empty($arCity)){
	$arResult['city'] = $arCity['ID'];
}

$this->IncludeComponentTemplate();