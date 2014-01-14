<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if(!empty($arResult['iblock'])){
	foreach($arResult['iblock'] as &$val){
		if(!empty($val['AUTHOR'])){
			$val['AUTHOR']=preg_replace('#^([^\s]+)#ui','$1<br />',trim($val['AUTHOR']));
		}
	}
}