<?php
namespace core\database;

/**
 * Описание класса не задано. Обратитесь с вопросом на почту разработчика.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 22.09.13
 */
interface VDriver {
	const MYSQL=1;
	const MONGO=2;

	public function __construct(array $obConfig);

	/**
	 * Устанавливает соединение с базой данных
	 * @return mixed
	 */
	public function connect();

	/**
	 * Закрывает соединение с базой данных
	 * @return void
	 */
	public function disconnect();

	/**
	 * Подготавливает строку для запроса
	 * @param string $sValue
	 * @return string
	 */
	public function escapeString($sValue);

	/**
	 * Создает объект выполнения запроса
	 * @return VQuery
	 */
	public function createQuery();

	/**
	 * Создает объект выполнения специального запроса
	 * @return VSpecialQuery
	 */
	public function createSpecialQuery();

	/**
	 * @return VListQueries
	 */
	public function createListQueries();

	/**
	 * Стартует транзакции. По логике может ничего не делать.
	 * @return void
	 */
	public function begin();

	/**
	 * Фиксирует изменения в базе данных
	 * создавая тем самым точку возврата
	 * @return void
	 */
	public function commit();

	/**
	 * Осуществляет возврат к зафиксированной позиции
	 * @return mixed
	 */
	public function rollback();
}