<?php
namespace core\database;
use core\types\VArray;

/**
 * Вероятно я забыл оставить описание. Пишите на почту, дам комментарий.
 * @since 18.10.13
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 */
interface VFunction {
	/**
	 * Задает название функции, поля и дополнительные параметры
	 * @param VQuery $obQuery
	 * @param $sFn
	 * @param VArray $obParams
	 * @internal param $sField
	 */
	public function __construct(VQuery $obQuery,$sFn,VArray $obParams=null);

	/**
	 * Добавляет условие для поиска результатов работы функции
	 * @param $sThen
	 * @param $sElse
	 * @return VFilter
	 */
	public function when($sThen,$sElse);

	/**
	 * Генерирует запрос
	 * @return mixed
	 */
	public function make();

	/**
	 * Проверяет корректность функции
	 * @return bool
	 */
	public function checkFn();
}