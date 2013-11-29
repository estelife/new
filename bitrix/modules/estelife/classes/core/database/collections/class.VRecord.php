<?php
namespace core\database\collections;

/**
 * Вероятно я забыл оставить описание. Пишите на почту, дам комментарий.
 * @since 03.10.13
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 */
class VRecord implements \Iterator,\ArrayAccess,\Countable {
	protected $obCollection;
	protected $arData;
	private $bChange;
	private $arFields;
	private $nCurrent;
	private $nCount;

	/**
	 * Задает коллекцию, которой принадлежит запись и данные самой записи
	 * @param VCollection $obCollection
	 * @param array $arData
	 */
	public function __construct(VCollection $obCollection,array $arData){
		$this->obCollection=$obCollection;
		$this->arData=$arData;
		$this->nCount=0;
		$this->nCurrent=0;
		$this->arFields=array();
		$this->bChange=false;
	}

	/**
	 * Присваивает значение определенному полю
	 * @param $sField
	 * @param $sValue
	 * @return $this
	 */
	public function __set($sField,$sValue){
		if(!$this->check($sField,$sValue)){
			$this->arData[$sField]=$sValue;
			$this->arFields[]=$sField;
			$this->bChange=true;
			++$this->nCount;
		}

		return $this;
	}

	/**
	 * Прверяет наличие поля в массиве с данными
	 * @param $sField
	 * @return bool
	 */
	public function __isset($sField){
		return isset($this->arData[$sField]);
	}

	/**
	 * Проверяет соответствие значения в поле с переданным
	 * @param $sField
	 * @param $sValue
	 * @return bool
	 */
	public function check($sField,$sValue){
		return (isset($this->arData[$sField]) &&
			$this->arData[$sField]==$sValue);
	}

	/**
	 * Возвращает значение поля, если такое есть, иначе - значение по умолчанию
	 * @param $sField
	 * @return bool
	 */
	public function __get($sField){
		return (isset($this->arData[$sField])) ?
			$this->arData[$sField] : null;
	}

	/**
	 * Удаляет поле из массива данных
	 * @param $sKey
	 * @return $this
	 */
	public function __unset($sKey){
		if(isset($this->arData[$sKey])){
			$nKey=array_search($sKey,$this->arFields);
			--$this->nCount;
			unset($this->arData[$sKey],$this->arFields[$nKey]);
		}
		return $this;
	}

	/**
	 * Возвращает коллекцию, которой принадлежит запись
	 * @return VCollection
	 */
	public function collection(){
		return $this->obCollection;
	}

	/**
	 * Возвращает элемент, соответствующий текущей итерации
	 * @return mixed
	 */
	public function current(){
		return $this->arData[$this->arFields[$this->nCurrent]];
	}

	/**
	 * Переводит указатель на следующий элемент
	 */
	public function next(){
		++$this->nCurrent;
	}

	/**
	 * Возвращает ключ элемента, соответствующего текущей итерации
	 * @return mixed
	 */
	public function key(){
		return $this->arFields[$this->nCurrent];
	}

	/**
	 * Реализует проверку наличия элемнета, соответствующего текущей итерации
	 * @return bool
	 */
	public function valid(){
		return isset($this->arFields[$this->nCurrent]);
	}

	/**
	 * Сбрасывает указатель на начало
	 */
	public function rewind(){
		$this->nCurrent=0;
	}

	/**
	 * Проверяет наличи поля среди данных
	 * @param mixed $sKey
	 * @return bool
	 */
	public function offsetExists($sKey){
		return isset($this->arData[$sKey]);
	}

	/**
	 * Возвращает значение поля
	 * @param mixed $sKey
	 * @return bool|mixed
	 */
	public function offsetGet($sKey){
		return $this->__get($sKey);
	}

	/**
	 * Добавляет значение поля
	 * @param mixed $sField
	 * @param mixed $mValue
	 */
	public function offsetSet($sField, $mValue){
		$this->__set($sField,$mValue);
	}

	/**
	 * Удаляет поле из данных записи
	 * @param mixed $sKey
	 */
	public function offsetUnset($sKey){
		$this->__unset($sKey);
	}

	/**
	 * Отдает количество полей
	 * @return int
	 */
	public function count(){
		return $this->nCount;
	}

	/**
	 * Возвращает данные записи в виде массива
	 * @return array
	 */
	public function toArray(){
		return $this->arData;
	}

	/**
	 * Проверяет, была  ли изменена запись
	 * @return bool
	 */
	public function isChanged(){
		return $this->bChange;
	}
}