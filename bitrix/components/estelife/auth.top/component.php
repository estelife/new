<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if ($USER->IsAuthorized())
	$arResult['auth']=true;
else
	$arResult['auth']=false;

$arResult['backurl']=urlencode($_SERVER['REQUEST_URI']);

$this->IncludeComponentTemplate();
