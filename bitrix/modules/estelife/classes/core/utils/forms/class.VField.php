<?php
namespace core\utils\forms;

/**
 * Класс для работы с полями.
 * @file class.VField.php
 * @version 0.1
 */
abstract class VField {

	protected $mValue;
	protected $sName;
	protected $sId;
	protected $sLabel;
	protected $arAttributes;

	abstract function __toString();

	public function __construct($sName, $sId){
		$this->sName = trim(addslashes(htmlspecialchars($sName, ENT_QUOTES, 'utf-8')));
		$this->sId = trim(addslashes(htmlspecialchars($sId, ENT_QUOTES, 'utf-8')));
	}

	/**
	 * Метод установки имени поля
	 * @param $sName
	 */
	public function setName($sName){
		if (empty($sName))
			assert('Field name is empty');

		$this->sName = trim(addslashes(htmlspecialchars($sName, ENT_QUOTES, 'utf-8')));
	}

	/**
	 * Метод установки значения для поля
	 * @param $mValue
	 */
	public function setValue($mValue){
		$this->mValue = trim(addslashes(htmlspecialchars($mValue, ENT_QUOTES, 'utf-8')));
	}


	/**
	 * Метод получения значения для поля
	 */
	public function getValue(){
		return $this->mValue;
	}

	/**
	 * Метод установки лэйбла для поля
	 * @param $sLabel
	 */
	public function setLabel($sLabel){
		if (empty($sLabel))
			assert('Field label is empty');

		$this->sLabel = trim(addslashes(htmlspecialchars($sLabel, ENT_QUOTES, 'utf-8')));

	}

	/**
	 * Метод установки атрибутов поля
	 * @param array $arAttributes
	 */
	public function setAttributes(array $arAttributes){
		if (!is_array($arAttributes) && empty($arAttributes))
			assert('Field attributes is empty or not array');

		$this->arAttributes = $arAttributes;
	}
}