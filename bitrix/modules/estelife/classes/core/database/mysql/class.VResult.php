<?php
namespace core\database\mysql;
use core\database as db;

/**
 * Класс выполняет получение данных в результате запроса к MySQL
 * @since 12.05.2012
 * @version 0.1
 * @see VQuery
 */
class VResult implements db\VResult {
	private $obQuery;
	private $nFetchedRows;
	private $nCount;
	private $nInsertId;
	private $nAffected;

	/**
	 * Задает объект запроса, сгенерировавшего результат
	 * @param \core\database\mysql\VQuery|\core\database\VQuery $obQuery
	 * @throws \core\database\exceptions\VResultException
	 * @return \core\database\mysql\VResult
	 */
	public function __construct(db\VQuery $obQuery){
		$this->obQuery=$obQuery;

		if(!$this->obQuery->queryResult())
			throw new db\exceptions\VResultException('incorrect query result');

		$this->nFetchedRows=0;
	}

	/**
	 * Возвращает одну строку из результата выборки в виде ассоциативного массива
	 * @return array
	 */
	public function assoc(){
		return ($obResult=$this->obQuery->queryResult()) ?
			$obResult->Fetch() : false;
	}

	/**
	 * Возвращает одну строку из результата выборки в виде нумерованного массива
	 * @return array
	 */
	public function row(){
		return ($obResult=$this->obQuery->queryResult()) ?
			array_values($obResult->Fetch()) : false;
	}

	/**
	 * Возвращает массив всех строк из результата выборки
	 * @return array
	 */
	public function all(){
		$arResult=array();

		while($arData=$this->assoc())
			$arResult[]=$arData;

		return $arResult;
	}

	/**
	 * Возвращает число строк, подпадающих под результат выборки
	 * @return int
	 */
	public function count(){
		return ($obResult=$this->obQuery->queryResult()) ?
			$obResult->SelectedRowsCount() : 0;
	}

	/**
	 * Возвращает число строк, затронутых запросом на изменение
	 * @return mixed
	 */
	public function affected(){
		return ($obResult=$this->obQuery->queryResult()) ?
			$obResult->AffectedRowsCount() : 0;
	}

	/**
	 * Возвращает индекс вставленной строки
	 * @return mixed
	 */
	public function insertId(){
		return ($obResult=$this->obQuery->queryResult()) ?
			$obResult->LastID : 0;
	}

	/**
	 * Костыль для получения объекта CDBResult
	 * @return \CDBResult
	 */
	public function bxResult(){
		return $this->obQuery->queryResult();
	}

	/**
	 * Костыль для обращения к методам объекта CDBResult
	 * @param $sMethod
	 * @param $arParams
	 * @return mixed|null
	 */
	public function __call($sMethod,$arParams){
		if(($obResult=$this->obQuery->queryResult()) &&
			method_exists($obResult,$sMethod)){
			return call_user_func_array(array($obResult,$sMethod),$arParams);
		}
		return null;
	}
}
