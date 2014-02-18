<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('estelife');

$sResponse = '';
$nReceiptId = intval($_POST['userid']);
$sKey = trim(htmlspecialchars($_POST['key']));
$obProtocol = new \pay\VProtocol();

try{
	$obReceipt = \pay\VReceipt::getById($nReceiptId);
	$obProtocol->setReceipt($obReceipt);

	if(!$obProtocol->checkKey(0,0,$sKey))
		throw new \pay\VReceiptEx('invalid secret key');

	if(!$obProtocol->checkReceipt())
		throw new \pay\VReceiptEx('invalid receipt');

	$obReceipt->updateStatus(\pay\VReceipt::PERFORMED);
	$obReceipt->saveChanges();

	$sResponse = $obProtocol->createResponse(
		'receipt successfully tested',
		true
	);
}catch(\pay\VReceiptEx $e){
	$sResponse = $obProtocol->createResponse(
		$e->getMessage(),
		false
	);
}

echo $sResponse;
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");