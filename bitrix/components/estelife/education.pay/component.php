<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("estelife");

$obProtocol = new \pay\VProtocol();
$arResult['source_id'] = $obProtocol->getProjectId();
$arResult['project_id'] = $obProtocol->getSourceId();
$arResult['receipt_id'] = 2;

$this->IncludeComponentTemplate();