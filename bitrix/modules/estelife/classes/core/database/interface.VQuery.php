<?php
namespace core\database;

/**
 * Интерфейс описывает методы, используемые при образовании частей запроса
 * @since 12.05.2012
 * @version 0.1
 */
interface VQuery {
	/**
	 * @param VDriver $obDriver
	 */
	public function __construct(VDriver $obDriver);

	/**
	 * Метод осущетсвляет выборку из данных из базы
	 * возращает строку запроса.
	 * @return VResult
	 */
	public function select();

	/**
	 * Метод реализует вставку данных
	 * @return mixed
	 */
	function insert();

	/**
	 * Реализует обновление данных
	 * @return mixed
	 */
	function update();

	/**
	 * Релаизует удаление данных
	 * @return mixed
	 */
	public function delete();

	/**
	 * Возвращает контсруктор запроса
	 * @return VQueryBuilder
	 */
	public function builder();

	/**
	 * Возвращает драйвер базы
	 * @return VDriver
	 */
	public function driver();

	/**
	 * Вовзращает реузультат выполненного запроса
	 * @return object
	 */
	public function queryResult();

	/**
	 * Регистрирует таблицу, которая учавствует в запросе
	 * @param $sTable
	 * @param string $sAlias
	 * @return mixed
	 */
	public function registerTable($sTable,$sAlias='');

	/**
	 * Возвращает список зарегистрированных таблиц
	 * @return mixed
	 */
	public function getRegisteredTables();
}
