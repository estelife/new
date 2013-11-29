<?php
namespace core\database\mysql;
use core\database as db;
use core\database\VListQueries;

/**
 * Вероятно я забыл оставить описание. Пишите на почту, дам комментарий.
 * @since 08.10.13
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 */
class VSpecialQuery implements db\VSpecialQuery {
	/**
	 * @var \core\database\mysql\VDriver
	 */
	private $obDriver;
	private $arFields;
	private $arTables;

	/**
	 * Задает драйвер базы, который инициировал создание запроса
	 * @param db\VDriver $obDriver
	 */
	public function __construct(db\VDriver $obDriver){
		$this->obDriver=$obDriver;
		$this->arFields=array();
	}

	/**
	 * Создает триггер
	 * @param $sName
	 * @param VListQueries $obQueries
	 * @throws \core\database\exceptions\VQueryException
	 * @return boolean
	 */
	public function createTrigger($sName, VListQueries $obQueries){
		throw new db\exceptions\VQueryException('database action unsupported');
	}

	/**
	 * Создает процедуру
	 * @param $sName
	 * @param VListQueries $obQueries
	 * @throws \core\database\exceptions\VQueryException
	 * @return boolean
	 */
	public function createProcedure($sName, VListQueries $obQueries){
		throw new db\exceptions\VQueryException('database action unsupported');
	}

	/**
	 * Создает таблицу
	 * @param $sName
	 * @throws \core\database\exceptions\VQueryException
	 * @return boolean
	 */
	public function createTable($sName){
		throw new db\exceptions\VQueryException('database action unsupported');
	}

	/**
	 * Удаляет таблицу
	 * @param $sName
	 * @throws \core\database\exceptions\VQueryException
	 * @return boolean
	 */
	public function dropTable($sName){
		throw new db\exceptions\VQueryException('database action unsupported');
	}

	/**
	 * Осуществляет валидацию поля
	 * @param string|array $mTable
	 * @param $sField
	 * @param bool $mValue
	 * @return boolean
	 */
	public function checkField($mTable,&$sField,$mValue=false){
		$sField=str_replace('`','',$sField);
		$sAlias=false;

		if(preg_match('#^([\w_\-]+\.)?([\w_\-]+)$#',$sField,$arMatches)){
			if(!empty($arMatches[1])){
				$arMatches[1]=mb_substr($arMatches[1],0,-1,'utf-8');

				if($this->checkTable($arMatches[1])){
					$mTable=$arMatches[1];
					$sAlias=PREFIX.$arMatches[1];
				}else
					$sAlias=$arMatches[1];
			}

			$sField=$arMatches[2];
		}

		if($bResult=$this->_checkField($mTable,$sField,$mValue))
			$sField=(!empty($sAlias)) ?
				$sAlias.'.`'.$sField.'`' :
				'`'.$sField.'`';

		return $bResult;
	}

	/**
	 * Непосредственный процесс валиадции поля.
	 * Вынесено в отдельный метод, чтобы можно было перед этим привести поле к должному виду (касается SQL СУБД)
	 * @param string|array $mTable
	 * @param $sField
	 * @param bool $mValue
	 * @return boolean
	 */
	private function _checkField($mTable,$sField,$mValue=false){
		if(!is_array($mTable)){
			$arFields=$this->getFields($mTable);
			return isset($arFields[$sField]);
		}else if(count($mTable)==1){
			$mTable=reset($mTable);

			if(is_array($mTable) && isset($mTable['table']))
				$mTable=$mTable['table'];

			return $this->checkField($mTable,$sField,$mValue);
		}else{
			$bResult=false;

			foreach($mTable as $sTable){
				if($bResult=$this->checkField($sTable,$sField,$mValue))
					break;
			}

			return $bResult;
		}
	}

	/**
	 * Осуществляет получение списка полей таблцы / коллекции
	 * @param string $sTable
	 * @return array
	 */
	public function getFields($sTable){
		if(!isset($this->arFields[$sTable])){
			if(!$this->checkTable($sTable))
				return array();

			$obResult=$this->obDriver->connect()->query('SHOW COLUMNS  FROM `'.$sTable.'`');

			while($arField=$obResult->Fetch()){
				$this->arFields[$sTable][$arField['Field']]=true;
			}
		}

		return $this->arFields[$sTable];
	}

	/**
	 * Проверяет является ли таблица, таблицей
	 * @param $sTable
	 * @return boolean
	 */
	public function checkTable(&$sTable){

		if(!$this->arTables){
			$this->arTables=array();
			$obResult=$this->obDriver->connect()->query('SHOW TABLES');

			if($obResult){
				while($arTable=$obResult->Fetch())
					$this->arTables[reset($arTable)]=true;
			}
		}

		$sTemp=(mb_substr($sTable,0,strlen(PREFIX),'utf-8') != PREFIX) ?
			PREFIX.$sTable :
			$sTable;

		if(isset($this->arTables[$sTemp])){
			$sTable=$sTemp;
			return true;
		}

		return false;
	}

	/**
	 * Получает список таблиц
	 * @return array
	 */
	public function getTables(){

		if(!$this->arTables){
			$this->arTables=array();
			$obResult=$this->obDriver->connect()->query('SHOW TABLES');

			if($obResult){
				while($arTable=$obResult->Fetch())
					$this->arTables[]=reset($arTable);
			}
		}

		return $this->arTables;
	}
}