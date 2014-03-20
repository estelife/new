<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

if (!empty($arResult['form']['fields'])){
	foreach ($arResult['form']['fields'] as &$val){
		$val = (string)$val;
	}
}
bitrix\ERESULT::$DATA['comments']['form']=$arResult['form'];
