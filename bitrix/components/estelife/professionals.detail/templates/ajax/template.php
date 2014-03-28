<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

echo \core\types\VString::jsonSafeEncode(array(
	'detail'=>$arResult['professional'],
	'crumb'=>array(
		array(
			'name'=>'Главная',
			'link'=>'/'
		),
		array(
			'name'=>'Специалисты',
			'link'=>'/professionals/',
		),
		array(
			'name'=>$arResult['professional']['name'],
			'link'=>'#'
		)
	),
));