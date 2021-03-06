<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('estelife');

$nReceiptId = intval($_POST['userid']);
$nPaymentId = intval($_POST['paymentid']);
$fAmount = number_format(floatval($_POST['amount']),2,'.','');
$sKey = trim($_POST['key']);
$obProtocol = new \pay\VProtocol();

try {
	$obReceipt = \pay\VReceipt::getById($nReceiptId);
	$obProtocol->setReceipt($obReceipt);
	$obProtocol->setReceipt($obReceipt);

	if(!$obProtocol->checkKey($fAmount,$nPaymentId,$sKey))
		throw new \pay\VReceiptEx('invalid secret key');

	if(!$obProtocol->checkReceipt())
		throw new \core\exceptions\VException('receipt will be payed');

	$obReceipt->setPaymentId($nPaymentId);
	$obReceipt->updateStatus(\pay\VReceipt::COMPLETED);
	$obReceipt->saveChanges();

	$sResponse = $obProtocol->createResponse(
		'receipt status updated successfully',
		true
	);
} catch(\pay\VReceiptEx $e){
	$sResponse = $obProtocol->createResponse(
		$e->getMessage(),
		false
	);
} catch (\core\exceptions\VException $e) {
	$sResponse = $obProtocol->createResponse(
		$e->getMessage(),
		true
	);
}

echo $sResponse;
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");