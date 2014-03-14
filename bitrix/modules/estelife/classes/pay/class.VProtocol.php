<?php
namespace pay;
use core\database\VDatabase;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 18.02.14
 */
final class VProtocol {
	private $sSecretKey;
	private $nSourceId;
	private $nProjectId;

	/**
	 * @var VReceipt
	 */
	private $obReceipt;

	public function __construct(){
		$this->sSecretKey = 'mjbdkrSE09eIChst';
		$this->nSourceId = 6628;
		$this->nProjectId = 6628;
	}

	public function getSourceId(){
		return $this->nSourceId;
	}

	public function getProjectId(){
		return $this->nProjectId;
	}

	public function setReceipt(VReceipt $obReceipt){
		$this->obReceipt = $obReceipt;
	}

	public function checkKey($fAmount,$nPaymentId,$sKey){
		$nReceiptId = $this->obReceipt->getReceiptId();
		$sSalt = $fAmount.$nReceiptId.$nPaymentId.$this->sSecretKey;
		return ($sKey == md5($sSalt));
	}

	public function createResponse($sMessage,$bSuccess){
		$sCode=$bSuccess ? 'YES' : 'NO';
		return '<?xml version="1.0" encoding="utf-8"?>
			<result>
				<code>'.$sCode.'</code>
				<comment>'.mb_substr($sMessage,0,400,'utf-8').'</comment>
			</result>';
	}

	public function checkReceipt(){
		return ($this->obReceipt->getPaymentId() == 0);
	}
}