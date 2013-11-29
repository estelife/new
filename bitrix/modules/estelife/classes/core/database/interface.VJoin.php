<?php
namespace core\database;

/**
 * Описание класса не задано. Обратитесь с вопросом на почту разработчика.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 29.09.13
 */
interface VJoin {
	public function __construct(VQuery $obQuery,$bJoinContext=false);

	/**
	 * Генерит запрос для объединения данныз из 2-х таблиц / коллекций по принципу LEFT JOIN (SQL)
	 * @return VJoin
	 */
	public function _left();

	/**
	 * Генерит запрос для объединения данных из 2-х таблиц по принципу RIGHT JOIN (SQL)
	 * @return VJoin
	 */
	public function _right();

	/**
	 * Генерит запрос для объединения данных из 2-х таблиц по принципу INNER JOIN (SQL)
	 * @return VJoin
	 */
	public function _inner();

	/**
	 * Генерит условие объединения данных из 2-х таблиц
	 * @return VFilter
	 */
	public function _cond();

	/**
	 * Указывает на какую таблицу делаем join
	 * @param string $sTable
	 * @param string $sField
	 * @param string $sAlias
	 * @return VJoin
	 */
	public function _to($sTable,$sField,$sAlias='');

	/**
	 * Указывает с какой таблицы делаем join
	 * @param string $sTable
	 * @param string $sField
	 * @internal param string $sAlias
	 * @return VJoin
	 */
	public function _from($sTable,$sField);

	/**
	 * Генерирует часть запроса, которая отвечает за объединение
	 * @return mixed
	 */
	public function make();
}