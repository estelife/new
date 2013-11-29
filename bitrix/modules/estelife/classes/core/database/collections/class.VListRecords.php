<?php
namespace core\database\collections;

/**
 * Список записей коллекции
 * @since 03.10.13
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 */
class VListRecords implements \Iterator,\Countable {
	protected $arRecords;
	private $nCurrent;
	private $nCount;

	/**
	 * Осущетсвляет инициализацию полей класса
	 */
	public function __construct(){
		$this->arRecords=array();
		$this->nCurrent=0;
		$this->nCount=0;
	}

	/**
	 * Добавляет запись
	 * @param VRecord $obRecord
	 * @return VListRecords
	 */
	public function set(VRecord $obRecord){
		$this->arRecords[]=$obRecord;
		++$this->nCount;
		return $this;
	}

	/**
	 * Возвращайте запись, соответствующую текущей терации
	 * @return VRecord
	 */
	public function current(){
		return $this->arRecords[$this->nCurrent];
	}

	/**
	 * Переносит указатель на следующий элемент
	 */
	public function next(){
		++$this->nCurrent;
	}

	/**
	 * Возвращает ключ элемента, соответствующего текущей итерации
	 * @return int|mixed
	 */
	public function key(){
		return $this->nCurrent;
	}

	/**
	 * Осущетслвяет проверку наличия элмента, соответствующего текущей итерации
	 * @return bool
	 */
	public function valid(){
		return isset($this->arRecords[$this->nCurrent]);
	}

	/**
	 * Сбрасывает указатель на начало
	 */
	public function rewind(){
		$this->nCurrent=0;
	}

	/**
	 * Возвращает кол-во записей в коллекции
	 * @return int
	 */
	public function count(){
		return $this->nCount;
	}

	/**
	 *	Осуществляет проверку, есмть ли записи в списке
	 * @return bool
	 */
	public function blank(){
		return empty($this->arRecords);
	}
}