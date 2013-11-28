<?php
namespace core\database;

/**
 * Вероятно я забыл оставить описание. Пишите на почту, дам комментарий.
 * @since 03.10.13
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 */
interface VSpecialQuery {
	/**
	 * Задает драйвер базы, который инициировал создание запроса
	 * @param VDriver $obDriver
	 */
	public function __construct(VDriver $obDriver);

	/**
	 * Создает триггер
	 * @param $sName
	 * @param VListQueries $obQueries
	 * @return boolean
	 */
	public function createTrigger($sName,VListQueries $obQueries);

	/**
	 * Создает процедуру
	 * @param $sName
	 * @param VListQueries $obQueries
	 * @return boolean
	 */
	public function createProcedure($sName,VListQueries $obQueries);

	/**
	 * Создает таблицу
	 * @param $sName
	 * @return boolean
	 */
	public function createTable($sName);

	/**
	 * Удаляет таблицу
	 * @param $sName
	 * @return boolean
	 */
	public function dropTable($sName);

	/**
	 * Осуществляет валидацию поля
	 * @param string|array $mTable
	 * @param $sField
	 * @param bool $mValue
	 * @return boolean
	 */
	public function checkField($mTable,&$sField,$mValue=false);

	/**
	 * Осуществляет получение списка полей таблцы / коллекции
	 * @param string $sTable
	 * @return array
	 */
	public function getFields($sTable);

	/**
	 * Проверяет является ли таблица, таблицей
	 * @param $sTable
	 * @return boolean
	 */
	public function checkTable(&$sTable);
}