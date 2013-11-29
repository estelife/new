<?php
namespace core\database\collections;

/**
 * Задает правила копирования элементов
 * @since 03.10.13
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 */
class VRules {
	protected $obRecord;
	protected $arRules;

	/**
	 * Устанавливает запись, которую будем копировать
	 * @param VRecord $obRecord
	 */
	public function __construct(VRecord $obRecord){
		$this->obRecord=$obRecord;
		$this->arRules=array();
	}

	/**
	 * Задает правило
	 * @param $sFieldFrom
	 * @param $sFieldTo
	 */
	final public function addRule($sFieldFrom,$sFieldTo){
		$this->arRules[$sFieldFrom]=$sFieldTo;
	}

	/**
	 * Возвращает запись, которую копируем
	 * @return VRecord
	 */
	final public function record(){
		return $this->obRecord;
	}

	/**
	 * Осуществляет применение правил
	 * @param VCollection $obCollection
	 * @return VRecord
	 */
	public function prepare(VCollection $obCollection){
		$obRecord=$obCollection->create();

		foreach($this->obRecord as $sKey=>$sValue)
			$obRecord->set($sKey,$sValue);

		return $obRecord;
	}
}