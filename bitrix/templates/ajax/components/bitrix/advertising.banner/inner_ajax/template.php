<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

if(!empty($arResult['BANNER']))
	bitrix\ERESULT::$DATA['BANNER'][]='<div class="adv adv-out right">'.$arResult['BANNER'].'</div>';
