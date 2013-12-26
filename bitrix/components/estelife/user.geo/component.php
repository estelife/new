<?php
use core\database\mysql\VFilter;

use geo\VGeo;

CModule::IncludeModule("estelife");

if (isset($arParams['SET']) && intval($arParams['SET'])>0){
	$arResult['city'] = VGeo::getInstance()->setGeo($arParams['SET']);
}else
	$arResult['city'] = VGeo::getInstance()->getGeo();

$this->IncludeComponentTemplate();