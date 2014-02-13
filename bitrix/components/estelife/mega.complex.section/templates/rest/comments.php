<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

$APPLICATION->IncludeComponent(
	"estelife:comments.list",
	"ajax",
	array(
		'count'=>$_REQUEST['count'],
		'element_id'=>$_REQUEST['id'],
		'type'=>$_REQUEST['type']
	),
	false
);