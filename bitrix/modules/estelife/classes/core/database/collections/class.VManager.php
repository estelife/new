<?php
namespace core\database\collections;
use core\database\exceptions\VCollectionException;

/**
 * Вероятно я забыл оставить описание. Пишите на почту, дам комментарий.
 * @since 04.10.13
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 */
class VManager {
	/**
	 * Массив зарегистрированных коллекций
	 * @var array
	 */
	private static $arCollections;

	/**
	 * Возвращает зарегистрированную коллекуию. Если такой нет, возбуждает исключение
	 * @param $sCollection
	 * @return VCollection
	 * @throws VCollectionException
	 */
	public static function get($sCollection){
		if(!self::has($sCollection))
			throw new VCollectionException('collection not registered in manager');

		return self::$arCollections[$sCollection];
	}

	/**
	 * Регитсрирует колелкцию в менеджере коллекций
	 * @param VCollection $obCollection
	 */
	public static function register(VCollection $obCollection){
		if(!is_array(self::$arCollections))
			self::$arCollections=array();

		$sCollection=$obCollection->name();
		self::$arCollections[$sCollection]=$obCollection;
	}

	/**
	 * Проверяет наличие зарегистрированной коллекции
	 * @param $sCollection
	 * @return bool
	 */
	public static function has($sCollection){
		return (is_array(self::$arCollections) &&
			isset(self::$arCollections[$sCollection]));
	}
}