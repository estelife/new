<?php
namespace core\database;

/**
 * Описание класса не задано. Обратитесь с вопросом на почту разработчика.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 29.09.13
 */
interface VFilter {
	/**
	 * Задает ссылку на запрос
	 * @param VQuery $obQuery
	 */
	public function __construct(VQuery $obQuery);

	/**
	 * Генерирует ИЛИ выражение, при этом возвращаемый объект не равен текущему
	 * @return VFilter
	 */
	public function _or();

	/**
	 * Условие IS
	 * @param $sField
	 * @param $mValue
	 * @return mixed
	 */
	public function _is($sField,$mValue);

	/**
	 * Условие РАВНО
	 * @param $sField
	 * @param $mValue
	 * @return VFilter
	 */
	public function _eq($sField,$mValue);

	/**
	 * Условие НЕ РАВНО
	 * @param $sField
	 * @param $mValue
	 * @return VFilter
	 */
	public function _ne($sField,$mValue);

	/**
	 * Условие НЕ
	 * @param $sField
	 * @param $mValue
	 * @return VFilter
	 */
	public function _not($sField,$mValue);

	/**
	 * Условие МЕНЬШЕ
	 * @param $sField
	 * @param $mValue
	 * @return VFilter
	 */
	public function _lt($sField,$mValue);

	/**
	 * Условие МЕНЬШЕ или РАВНО
	 * @param $sField
	 * @param $mValue
	 * @return VFilter
	 */
	public function _lte($sField,$mValue);

	/**
	 * Условие БОЛЬШЕ
	 * @param $sField
	 * @param $mValue
	 * @return VFilter
	 */
	public function _gt($sField,$mValue);

	/**
	 * Условие БОЛЬШЕ или РАВНО
	 * @param $sField
	 * @param $mValue
	 * @return VFilter
	 */
	public function _gte($sField,$mValue);

	/**
	 * Условие поиска по части строки
	 * @param $sField
	 * @param $mValue
	 * @param $nType
	 * @return VFilter
	 */
	public function _like($sField,$mValue,$nType);

	/**
	 * Условие отсутсвия части указанной строки
	 * @param $sField
	 * @param $mValue
	 * @param $nType
	 * @return VFilter
	 */
	public function _notLike($sField,$mValue,$nType);

	/**
	 * Услловие соответствия хотя бы одному элементу списка
	 * @param $sField
	 * @param array $arValue
	 * @return VFilter
	 */
	public function _in($sField,array $arValue);

	/**
	 * Условие противоречия всем элементам списка
	 * @param $sField
	 * @param array $arValue
	 * @return VFilter
	 */
	public function _notIn($sField,array $arValue);

	/**
	 * Генерит выражения для проверки соответсвия значению NULL
	 * @param $sField
	 * @return VFilter
	 */
	public function _isNull($sField);

	/**
	 * Генерит выражение для проверки не совпадения со значением NULL
	 * @param $sField
	 * @return VFilter
	 */
	public function _notNull($sField);

	/**
	 * Условие РЕГУЛЯРНОЕ ВЫРАЖЕНИЕ
	 * @param $sField
	 * @param $sRegex
	 * @return VFilter
	 */
	public function _regex($sField,$sRegex);

	/**
	 * Условие не совпадения по регулярному выражению
	 * @param $sField
	 * @param $sRegex
	 * @return VFilter
	 */
	public function _notRegex($sField,$sRegex);

	/**
	 * Условие ПОЛНОТЕКСТОВЫЙ ПОИСК
	 * @param $sField
	 * @param $mValue
	 * @return VFilter
	 */
	public function _match($sField,$mValue);

	/**
	 * Генерирует запрос
	 * @return string
	 */
	public function make();
}