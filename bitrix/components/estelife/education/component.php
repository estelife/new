<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

global $USER;

$arResult['service_id'] = 2;
$arResult['is_login'] = $USER->IsAuthorized();
$arResult['allow'] = true;

try {
	if(!$arResult['is_login'])
		throw new \core\exceptions\VException('Необходимо авторизоваться.');

	$nUserId = $USER->GetID();
	$obReceipt = \pay\VReceipt::getByUserService($nUserId,$arResult['service_id']);

	if($obReceipt->getStatus() != \pay\VReceipt::COMPLETED)
		throw new \core\exceptions\VException('Необходимо оплатить.');
} catch(\pay\VReceiptEx $e) {
	//\notice\VNotice::registerError('Доступ закрыт.','Необходимо произвести оплату');
	$arResult['allow'] = false;
} catch(\core\exceptions\VException $e) {
	//\notice\VNotice::registerError('Доступ закрыт.',$e->getMessage());
	$arResult['allow'] = false;
}

$this->IncludeComponentTemplate();