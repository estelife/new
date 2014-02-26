<?php
use core\exceptions\VException;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('estelife');
global $USER;

try {
	$sError = 'Оплата успешно завершена. Нам не удалось в данный момент идентифицировать Вас – это связано с рядом технических особенностей, ';
	$sError .= 'благодаря которым мы защищаем данные пользователей портала.';
	$sError .= 'В течение 10 минут Вы получите уведомление на адрес электронной почты, где будет указана подробная информация о дальнейших действиях.';

	$sComplete = 'Теперь Вам открыт доступ к телемосту.';

	$obSecure = new \pay\VSecure();

	if(!$obSecure->checkProtectedKey())
		throw new VException($sError);

	$arUser = $obSecure->getUserBySecret();

	if(empty($arUser))
		throw new VException($sError);

	$nService = 2;
	$obReceipt = \pay\VReceipt::getByUserService(
		$arUser['ID'],
		$nService
	);

	if($obReceipt->getStatus() != \pay\VReceipt::PERFORMED)
		throw new VException($sError);

	$USER->Update($arUser['ID'],array(
		'ACTIVE' => 'Y'
	));
	$USER->Login(
		$arUser['LOGIN'],
		$arUser['PASSWORD'],
		'Y','N'
	);
	$obReceipt->updateStatus(\pay\VReceipt::COMPLETED);

	\notice\VNotice::registerSuccess('Оплата прошла успешно!', $sComplete);
} catch(VException $e) {
	\notice\VNotice::registerError('Ожидание оплаты!', $e->getMessage());
}

LocalRedirect('/education/');
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");