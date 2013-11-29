<?php
namespace core\bx;
use core\database\collections\VCollection;
use core\database\VDriver;

/**
 * Вероятно я забыл оставить описание. Пишите на почту, дам комментарий.
 * @since 09.10.13
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 */
class VDatabaseAdapter {
	private $obDatabase;
	private $obDriver;

	/**
	 * @var \CDBResult
	 */
	private $obResult;

	public function __construct(\CDatabase $obDb,VDriver $obDriver){
		$this->obDatabase=$obDb;
		$this->obDriver=$obDriver;
	}

	public function query($sQuery){
		$this->obResult=$this->obDatabase->Query($sQuery);
		$this->obResult->LastID=$this->obDatabase->LastID();
		return ($this->obResult) ?
			$this->obResult :
			false;
	}

	public function escape_string($sValue){
		return $this->obDatabase->ForSql($sValue);
	}

	public function lastResult(){
		return $this->obResult;
	}
}