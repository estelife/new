<?php
namespace pay;
use core\database\VDatabase;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 18.02.14
 */
class VReceipt {
	private $nReceiptId;
	private $nPaymentId;
	private $fAmount;
	private $nUserId;
	private $nServiceId;
	private $nStatus;
	private $bChanged;

	const CREATED = 1;
	const PERFORMED = 2;
	const COMPLETED = 3;

	final protected function __construct($nReceiptId,$nUserId,$nServiceId,$nPaymentId,$fAmount,$nStatus){
		$this->nReceiptId = intval($nReceiptId);
		$this->nServiceId = intval($nServiceId);
		$this->nUserId = intval($nUserId);
		$this->fAmount = floatval($fAmount);
		$this->nPaymentId = intval($nPaymentId);
		$this->nStatus = intval($nStatus);
	}

	public function getReceiptId(){
		return $this->nReceiptId;
	}

	public function getPaymentId(){
		return $this->nPaymentId;
	}

	public function getAmount(){
		return $this->fAmount;
	}

	public function getUserId(){
		return $this->nUserId;
	}

	public function getServiceId(){
		return $this->nServiceId;
	}

	public function getStatus(){
		return $this->nStatus;
	}

	public function updateStatus($nStatus){
		$this->bChanged = true;
		$this->nStatus = intval($nStatus);
	}

	final public function setPaymentId($nPaymentId){
		if($this->nPaymentId > 0)
			throw new VReceiptEx('you can not change jobs already identifier payment');

		$this->bChanged = true;
		$this->nPaymentId = intval($nPaymentId);
	}

	public function saveChanges(){
		if($this->bChanged){
			$obQuery = VDatabase::driver()->createQuery();
			$obQuery->builder()
				->from('estelife_pay_receipts')
				->value('status',$this->nStatus)
				->value('payment_id',$this->nPaymentId)
				->filter()
				->_eq('id',$this->nReceiptId);
			$obQuery->update();
			$this->bChanged=false;
		}
	}

	final public static function getByUserService($nUserId,$nServiceId){
		$nUserId=intval($nUserId);
		$nServiceId=intval($nServiceId);
		$obQuery = VDatabase::driver()->createQuery();
		$obQuery->builder()
			->from('estelife_pay_receipts')
			->filter()
			->_eq('user_id',$nUserId)
			->_eq('service_id',$nServiceId);
		$arReceipt = $obQuery->select()->assoc();

		if(empty($arReceipt))
			throw new VReceiptEx('receipt of such data was not found');

		return new self(
			$arReceipt['id'],
			$arReceipt['user_id'],
			$arReceipt['service_id'],
			$arReceipt['payment_id'],
			$arReceipt['amount'],
			$arReceipt['status']
		);
	}

	final public static function getById($nReceiptId){
		$nReceiptId=intval($nReceiptId);
		$obQuery = VDatabase::driver()->createQuery();
		$obQuery->builder()
			->from('estelife_pay_receipts')
			->filter()
			->_eq('id',$nReceiptId);
		$arReceipt = $obQuery->select()->assoc();

		if(empty($arReceipt))
			throw new VReceiptEx('receipt of such data was not found');

		return new self(
			$arReceipt['id'],
			$arReceipt['user_id'],
			$arReceipt['service_id'],
			$arReceipt['payment_id'],
			$arReceipt['amount'],
			$arReceipt['status']
		);
	}

	/**
	 * Создает новую квитанцию
	 * @param $nUserId
	 * @param $nServiceId
	 * @param $fAmount
	 * @return VReceipt
	 * @throws VReceiptEx
	 */
	final public static function create($nUserId,$nServiceId,$fAmount){
		$nUserId = intval($nUserId);
		$nServiceId = intval($nServiceId);
		$fAmount = floatval($fAmount);

		$obQuery = VDatabase::driver()->createQuery();
		$obQuery->builder()
			->from('estelife_pay_receipts')
			->value('user_id',$nUserId)
			->value('service_id',$nServiceId)
			->value('amount',$fAmount)
			->value('status',self::CREATED);

		$obResult = $obQuery->insert();
		$nReceiptId = $obResult->insertId();

		if(empty($nReceiptId))
			throw new VReceiptEx('error creating receipts');

		return new self(
			$nReceiptId,
			$nUserId,
			$nServiceId,
			0,
			$fAmount,
			self::CREATED
		);
	}
}