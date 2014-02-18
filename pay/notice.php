<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('estelife');

$nReceiptId = intval($_POST['userid']);
$nPaymentId = intval($_POST['paymentid']);
$sKey = trim(htmlspecialchars($_POST['key']));
$obProtocol = new \pay\VProtocol();

try {
	$obReceipt = \pay\VReceipt::getById($nReceiptId);
	$obProtocol->setReceipt($obReceipt);
	$obProtocol->setReceipt($obReceipt);

	if(!$obProtocol->checkKey($sKey))
		throw new \pay\VReceiptEx('invalid secret key');

	if(!$obProtocol->checkReceipt())
		throw new \pay\VReceiptEx('invalid receipt');

	$obReceipt->setPaymentId($nPaymentId);
	$obReceipt->updateStatus(\pay\VReceipt::COMPLETED);

	$sResponse = $obProtocol->createResponse(
		'receipt status updated successfully',
		true
	);
} catch(\pay\VReceiptEx $e){
	$sResponse = $obProtocol->createResponse(
		$e->getMessage(),
		false
	);
}

echo $sResponse;
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");