<?php
namespace lists;
use core\types\VArray;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 02.12.13
 */
final class VFilter {
	/**
	 * @var \core\types\VArray
	 */
	private $obFilter;
	private $sList;
	private $bStored;

	public function __construct(VList $obList,$bStored=false){
		$this->sList=get_class($obList);
		$this->bStored=$bStored;

		if($bStored && isset($_SESSION[$this->sList]) &&
			is_object($_SESSION[$this->sList]) && $_SESSION[$this->sList] instanceof VArray)
			$this->obFilter=$_SESSION[$this->sList];
		else
			$this->obFilter=new VArray();
	}

	/**
	 * Задает поле для фильтрации
	 * @param $sField
	 * @param $mValue
	 * @return $this
	 */
	public function setField($sField,$mValue){
		$this->obFilter->set($sField,$mValue);
		return $this;
	}

	/**
	 * Возвращает значение поля
	 * @param $sField
	 * @return mixed
	 */
	public function getField($sField){
		return $this->obFilter->one($sField);
	}

	/**
	 * Удаляет поле из фильтра
	 * @param $sField
	 * @return $this
	 */
	public function delField($sField){
		$this->obFilter->del($sField);
		return $this;
	}

	/**
	 * Возвращает поля фильтра
	 * @return array
	 */
	public function getFields(){
		return $this->obFilter->all();
	}

	/**
	 * Проверяте, ялвяется ли фильтр пустым
	 * @return bool
	 */
	public function isEmpty(){
		return $this->obFilter->blank();
	}

	/**
	 * Очищает фильтр
	 */
	public function clear(){
		if($this->bStored)
			unset($_SESSION[$this->sList]);

		$this->obFilter=new VArray();
	}

	/**
	 * Пишет фильтр в сессию
	 */
	public function __destruct(){
		if($this->bStored)
			$_SESSION[$this->sList]=$this->obFilter;
	}
}