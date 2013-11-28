<?php
namespace core\database;
use core\database\exceptions\VDatabaseException;

/**
 * Фабрика для генерации объекта базы данных
 * @since 12.05.2012
 * @version 0.1
 */
class VDatabase {
	private static $obDriver;

	public static function driver(){
		if(!self::$obDriver){
			self::$obDriver=new mysql\VDriver(array());
		}
		return self::$obDriver;
	}
}