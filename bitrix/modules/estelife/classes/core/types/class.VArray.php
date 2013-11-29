<?php
namespace core\types;

/**
 * Класс обертка для работы с массивами. Повзволяет сократить кол-во проверок в коде шаблона.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 18.02.13
 * @file class.VArray.php
 * @version 0.1
 */
class VArray {
	private $arData;
	private $arKeys;
	private $arValues;

	/**
	 * Инициализует экземпляр для работы с конкретным массивом
	 * @param array $arData
	 */
	public function __construct(array $arData=array()){
		$this->arData=$arData;
	}

	/**
	 * Добавляет значение соответствующее ключу
	 * @param $sKey
	 * @param $mValue
	 */
	public function set($sKey,$mValue){
		$this->arData[$sKey]=$mValue;
	}

	/**
	 * Добавляет значение с автоматическим ключом
	 * @param $mValue
	 */
	public function put($mValue){
		$this->arData[]=$mValue;
	}

	/**
	 * Возвращает значение для ключа. Если не находит, возвращает по умолчанию.
	 * @param array $arData
	 * @param string $sKey
	 * @param mixed $mDefault
	 * @return mixed
	 */
	public static function get(array $arData,$sKey,$mDefault=false){
		return (isset($arData[$sKey])) ? $arData[$sKey] : $mDefault;
	}

	/**
	 * Возвращает одно значение по ключу, если не найдено возращает значение по умолчанию
	 * @param $sKey
	 * @param $mDefault
	 * @return mixed
	 */
	public function one($sKey,$mDefault=false){
		return ($this->is($sKey)) ? $this->arData[$sKey] : $mDefault;
	}

	/**
	 * Возвращает массив целиком
	 * @return array
	 */
	public function all(){
		return $this->arData;
	}

	/**
	 * Проверяет наличие ключа в массиве
	 * @param $sKey
	 * @return bool
	 */
	public function is($sKey){
		return isset($this->arData[$sKey]);
	}

	/**
	 * Возвращает все ключи массива
	 * @return array
	 */
	public function keys(){
		if(!$this->arKeys)
			$this->arKeys=array_keys($this->arData);
		return $this->arKeys;
	}

	/**
	 * Возращает все значения массива
	 * @return array
	 */
	public function values(){
		if(!$this->arValues)
			$this->arValues=array_values($this->arData);
		return $this->arValues;
	}

	/**
	 * Объединяет данные с другими данными :)
	 * @param array $arData
	 */
	public function merge(array $arData){
		if(!empty($arData)){
			foreach($arData as $sKey=>$mValue)
				$this->set($sKey,$mValue);

			$this->arValues=null;
			$this->arKeys=null;
		}
	}

	/**
	 * Осуществляет проверку пустоты пассива
	 * @param bool $sKey
	 * @return bool
	 */
	public function blank($sKey=false){
		return (is_string($sKey)) ?
			empty($this->arData[$sKey]) :
			empty($this->arData);
	}

	/**
	 * Удаляет значение по ключу
	 * @param $sKey
	 * @return bool
	 */
	public function del($sKey){
		if($this->is($sKey)){
			unset($this->arData[$sKey]);
			return true;
		}
		return false;
	}

	/**
	 * Проверяет ключ на соответствие значению
	 * @param $sKey
	 * @param $mValue
	 * @return bool
	 */
	public function check($sKey,$mValue){
		return ($this->is($sKey) && $this->one($sKey,'')==$mValue);
	}

	/**
	 * Функция осуществляет форматированный вывод значения переменной
	 * @param mixed $mData
	 */
	public static function prePrint($mData){
		print '<pre>'; print_r($mData); print '</pre>';
	}

	/**
	 * Возвращает размер массива
	 * @return int
	 */
	public function size(){
		return sizeof($this->arData);
	}
}