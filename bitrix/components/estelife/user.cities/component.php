<?php
$arResult['cities']['active'] = array(
	'359'=>'Москва',
	'358'=>'Санкт-Петербург',
);
$arResult['cities']['passive'] = array(
	'Новосибирск',
	'Екатеринбург',
	'Нижний Новгород',
	'Казань',
	'Самара',
	'Омск',
	'Челябинск',
	'Ростов-на-Дону',
	'Уфа',
	'Волгоград',
	'Красноярск',
	'Пермь',
	'Воронеж',
	'Тюмень'
);

$arCity = \geo\VGeo::getInstance()->getGeo();
if (!empty($arCity)){
	$arResult['city'] = $arCity['ID'];
}

$this->IncludeComponentTemplate();