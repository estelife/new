<?php
namespace core\database\mysql;
use core\database as db;


/**
 * Класс для генерации sql запроса из массива данных
 * @since 12.05.2012
 * @version 0.1
 */
class VQuery implements db\VQuery {
	protected $obDriver;
	private $obBuilder;
	private $obQueryResult;
	private $arTables;

	public function __construct(db\VDriver $obDriver){
		$this->obDriver=$obDriver;
	}

	public function select(){
		$sSelect=$this->builder()->buildSelect();
		$this->obBuilder=null;

		if(!($this->obQueryResult=$this->obDriver->connect()->query($sSelect)))
			throw new db\exceptions\VQueryException(
				$this->obDriver->connect()->error,
				$this->obDriver->connect()->errno
			);

		return new VResult($this);
	}

	public function count(){
		$sSelect=$this->builder()->buildCount();
		$this->obBuilder=null;

		if(!($this->obQueryResult=$this->obDriver->connect()->query($sSelect)))
			throw new db\exceptions\VQueryException(
				$this->obDriver->connect()->error,
				$this->obDriver->connect()->errno
			);

		$obResult=new VResult($this);
		$arResult=$obResult->assoc();

		return (!empty($arResult['count'])) ?
			intval($arResult['count']) : 0;
	}

	public function insert(){
		$sInsert=$this->builder()->buildInsert();
		$this->obBuilder=null;

		if(!($this->obQueryResult=$this->obDriver->connect()->query($sInsert)))
			throw new db\exceptions\VQueryException(
				$this->obDriver->connect()->error,
				$this->obDriver->connect()->errno
			);

		return new VResult($this);
	}

	public function update(){
		$sUpdate=$this->builder()->buildUpdate();
		$this->obBuilder=null;

		if(!($this->obQueryResult=$this->obDriver->connect()->query($sUpdate)))
			throw new db\exceptions\VQueryException(
				$this->obDriver->connect()->error,
				$this->obDriver->connect()->errno
			);

		return new VResult($this);
	}

	public function delete(){
		$sDelete=$this->builder()->buildDelete();
		$this->obBuilder=null;

		if(!($this->obQueryResult=$this->obDriver->connect()->query($sDelete)))
			throw new db\exceptions\VQueryException(
				$this->obDriver->connect()->error,
				$this->obDriver->connect()->errno
			);

		return new VResult($this);
	}

	/**
	 * Возвращает контсруктор запроса
	 * @return VQueryBuilder
	 */
	public function builder(){
		if(!$this->obBuilder)
			$this->obBuilder=new VQueryBuilder($this);
		return $this->obBuilder;
	}

	/**
	 * Возвращает драйвер базы
	 * @return db\VDriver
	 */
	public function driver(){
		return $this->obDriver;
	}

	/**
	 * Вовзращает реузультат выполненного запроса
	 * @return object
	 */
	public function queryResult(){
		return $this->obQueryResult;
	}

	/**
	 * Регистрирует таблицу, которая учавствует в запросе
	 * @param $sTable
	 * @param string $sAlias
	 * @return mixed
	 */
	public function registerTable($sTable,$sAlias=''){
		if(!empty($sAlias))
			$this->arTables[$sAlias]=$sTable;
		else
			$this->arTables[$sTable]=$sTable;
	}

	/**
	 * Возвращает список зарегистрированных таблиц
	 * @return mixed
	 */
	public function getRegisteredTables(){
		return $this->arTables;
	}
}
