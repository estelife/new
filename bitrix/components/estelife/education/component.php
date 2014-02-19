<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("estelife");
global $USER;

$arResult['service_id'] = 2;
$arResult['is_login'] = $USER->IsAuthorized();
$arResult['allow'] = true;

try {
	if(!$arResult['is_login']){
		$obSecure = new \pay\VSecure();

		if(!$obSecure->checkProtectedKey())
			throw new \core\exceptions\VException('Необходимо авторизоваться.');

		$arUser = $obSecure->getUserBySecret();

		if(empty($arUser))
			throw new \core\exceptions\VException('Необходимо авторизоваться.');

		$obReceipt = \pay\VReceipt::getByUserService(
			$arUser['ID'],
			$arResult['service_id']
		);

		if($obReceipt->getStatus() != \pay\VReceipt::PERFORMED)
			throw new \core\exceptions\VException('Необходимо авторизоваться.');

		$USER->Update($arUser['ID'],array(
			'ACTIVE' => 'Y'
		));
		$USER->Login(
			$arUser['LOGIN'],
			$arUser['PASSWORD'],
			'Y','N'
		);
		$obReceipt->updateStatus(\pay\VReceipt::COMPLETED);

		\notice\VNotice::registerSuccess('Оплата прошла успешно!', 'Теперь Вам открыт доступ к телемосту.');
		LocalRedirect('/education/');
	}

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