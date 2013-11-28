<?php
namespace core\database\mysql;
use core\bx\VDatabaseAdapter;
use core\database\exceptions\VDatabaseException;
use core\database as db;
use core\database\VListQueries;
use core\database\mysql\VSpecialQuery;
use mysqli;

/**
 * Класс обеспечивает интерфейс доступа к данным
 * посредством СУБД Mysql
 * @since 12.05.2012
 * @version 0.1
 * @see VDb
 */
class VDriver implements db\VDriver {
	/**
	 * Настройки соединения
	 */
	private $arConfig;

	/**
	 * @var mysqli
	 */
	private $obConnection;
	private $obSpecialQuery;

	/**
	 * Осуществляет инциализацию
	 * @param array $arConfig
	 */
	public function __construct(array $arConfig){
		$this->arConfig=$arConfig;
	}

	/**
	 * Возвращает объект соединения
	 * @return mixed|mysqli
	 * @throws \core\database\exceptions\VDatabaseException
	 */
	public function connect(){
		global $DB;

		if(!$this->obConnection)
			$this->obConnection=new VDatabaseAdapter($DB,$this);

		return $this->obConnection;
	}

	/**
	 * Убивает соединение
	 */
	public  function disconnect(){
		if($this->obConnection)
			$this->obConnection->close();
	}

	/**
	 * Экранирует строку
	 * @param string $sValue
	 * @return string
	 */
	public function escapeString($sValue){
		return $this->connect()->escape_string($sValue);
	}

	/**
	 * @return VQuery
	 */
	public function createQuery(){
		return new VQuery($this);
	}

	/**
	 * Создает объект выполнения специального запроса
	 * @return VSpecialQuery
	 */
	public function createSpecialQuery(){
		if(!$this->obSpecialQuery)
			$this->obSpecialQuery=new VSpecialQuery($this);

		return $this->obSpecialQuery;
	}

	/**
	 * @return VListQueries
	 */
	public function createListQueries(){
		return new VListQueries($this);
	}
}
