<?php
namespace core\database;

/**
 * Интерфейс описывает методы объекта результата запроса
 * @since 12.05.2012
 * @version 0.1
 */	
interface VResult {
	/**
	 * Задает объект запроса, сгенерировавшего результат
	 * @param VQuery $obQuery
	 */
	public function __construct(VQuery $obQuery);

	/**
	 * Возвращает одну строку из результата выборки в виде ассоциативного массива
	 * @return array
	 */
	public function assoc();

	/**
	 * Возвращает одну строку из результата выборки в виде нумерованного массива
	 * @return array
	 */
	public function row();

	/**
	 * Возвращает массив всех строк из результата выборки
	 * @return array
	 */
	public function all();

	/**
	 * Возвращает число строк, подпадающих под результат выборки
	 * @return int
	 */
	public function count();

	/**
	 * Возвращает число строк, затронутых запросом на изменение
	 * @return mixed
	 */
	public function affected();

	/**
	 * Возвращает индекс вставленной строки
	 * @return mixed
	 */
	public function insertId();

	/**
	 * Костыль для получения объекта CDBResult
	 * @return \CDBResult
	 */
	public function bxResult();

	/**
	 * Костыль для обращения к методам объекта CDBResult
	 * @param $sMethod
	 * @param $arParams
	 * @return mixed|null
	 */
	public function __call($sMethod,$arParams);
}