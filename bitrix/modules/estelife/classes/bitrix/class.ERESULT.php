<?php
namespace bitrix;

/**
 * Пустышка для обозначения результатов работы компонентов в массиве.
 * Стандартные компоненты не могу возвращать рещультат, поэтому такой костыль
 * @package bitrix
 */
class ERESULT {
	public static $DATA=array();
	public static $KEY;
}