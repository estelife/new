<?php
namespace core\types;

/**
 * Класс обертка для работы с массивами. Повзволяет сократить кол-во проверок в коде шаблона.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 18.02.13
 * @file class.VArray.php
 * @version 0.1
 */
class VArray implements \Countable,\Iterator,\ArrayAccess {
	private $arKeys;
	private $arValues;
	private $nIterator;

	/**
	 * Инициализует экземпляр для работы с конкретным массивом
	 * @param array $arData
	 */
	public function __construct(array $arData=array()){
		if(!empty($arData)){
			$this->arKeys=array_keys($arData);
			$this->arValues=array_values($arData);
		}else{
			$this->arKeys=array();
			$this->arValues=array();
		}

		$this->nIterator=0;
	}

	/**
	 * Добавляет значение соответствующее ключу
	 * @param $sKey
	 * @param $mValue
	 */
	public function set($sKey,$mValue){
		if(($nKey=$this->is($sKey))>-1){
			$this->arValues[$nKey]=$mValue;
		}else{
			$this->arKeys[]=$sKey;
			$this->arValues[]=$mValue;
		}
	}

	/**
	 * Добавляет значение с автоматическим ключом
	 * @param $mValue
	 */
	public function put($mValue){
		$this->arValues[]=$mValue;
		$this->arKeys[]=count($this->arValues)-1;
	}

	/**
	 * Возвращает значение для ключа. Если не находит, возвращает по умолчанию.
	 * @param array $arData
	 * @param string $sKey
	 * @param mixed $mDefault
	 * @return mixed
	 */
	public static function get(array $arData,$sKey,$mDefault=false){
		return (isset($arData[$sKey])) ?
			$arData[$sKey] :
			$mDefault;
	}

	/**
	 * Возвращает одно значение по ключу, если не найдено возращает значение по умолчанию
	 * @param $sKey
	 * @param $mDefault
	 * @return mixed
	 */
	public function one($sKey,$mDefault=false){
		return (($sKey=$this->is($sKey))>-1) ?
			$this->arValues[$sKey] :
			$mDefault;
	}

	/**
	 * Возвращает массив целиком
	 * @return array
	 */
	public function all(){
		$arTemp=array();
		foreach($this->arKeys as $nKey=>$sKey)
			$arTemp[$sKey]=$this->arValues[$nKey];
		return $arTemp;
	}

	/**
	 * Проверяет наличие ключа в массиве
	 * @param $sKey
	 * @return bool
	 */
	public function is($sKey){
		return (($sKey=array_search($sKey,$this->arKeys))!==false) ? $sKey : -1;
	}

	/**
	 * Возвращает все ключи массива
	 * @return array
	 */
	public function keys(){
		return $this->arKeys;
	}

	/**
	 * Возращает все значения массива
	 * @return array
	 */
	public function values(){
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
		}
	}

	/**
	 * Осуществляет проверку пустоты пассива
	 * @param bool $sKey
	 * @return bool
	 */
	public function blank($sKey=false){
		return (is_string($sKey)) ?
			($this->is($sKey)==-1) :
			empty($this->arKeys);
	}

	/**
	 * Удаляет значение по ключу
	 * @param $sKey
	 * @return bool
	 */
	public function del($sKey){
		if(($sKey=$this->is($sKey))>-1){
			unset(
				$this->arKeys[$sKey],
				$this->arValues[$sKey]
			);
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
		return ($this->is($sKey)>-1 && $this->one($sKey,'')==$mValue);
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

	public function current(){
		return $this->arValues[$this->nIterator];
	}

	public function next(){
		$this->nIterator++;
	}

	public function key(){
		return $this->nIterator;
	}

	public function valid(){
		return isset($this->arKeys[$this->nIterator]);
	}

	public function rewind(){
		$this->nIterator=0;
	}

	public function offsetExists($sKey){
		return !$this->blank($sKey);
	}

	public function offsetGet($sKey){
		return $this->one($sKey);
	}

	public function offsetSet($sKey, $mValue){
		$this->set($sKey,$mValue);
	}

	public function offsetUnset($sKey){
		$this->del($sKey);
	}

	public function count(){
		return $this->size();
	}
}