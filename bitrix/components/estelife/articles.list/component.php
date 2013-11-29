<?php
use core\exceptions\VException;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();



try{
	if (empty($arParams['SECTIONS_ID']) || empty($arParams['SECTIONS_NAME']) || count($arParams['SECTIONS_ID']) != count($arParams['SECTIONS_NAME']))
		throw new VException("Ошибка в задании параметров");

	CModule::IncludeModule("iblock");
	CModule::IncludeModule("estelife");





}catch(VException $e){
	echo $e->getMessage(), "\n";
}
$this->IncludeComponentTemplate();