<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

bitrix\ERESULT::$DATA['SAME_ARTICLES']=array_values($arResult["ITEMS"]);