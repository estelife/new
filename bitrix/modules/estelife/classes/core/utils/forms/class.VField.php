<?php
namespace form;

/**
 *
 * @author Panait Vitaly <panait.v@yandex.ru>
 * @since 06.02.14
 */

abstract class VField{
	protected $sName;
	protected $mValue;
	protected $sLabel;
	protected $arAttributes;
	protected $sId;

	abstract public function __toString();

	public function __construct($sName, $sId){
		$this->sName=trim(strip_tags($sName));
		if (empty($sId))
			$sId=md5($this->sName.time());

		$this->sId=$sId;
	}

	public function setName($sName){
		if (!empty($sName))
			$this->sName=$sName;
	}

	public function setValue($mValue){
		if (empty($mValue))
			$this->mValue=false;

		$this->mValue=$mValue;
	}

	public function setLabel($sLabel){
		if (!empty($sLabel))
			$this->sLabel=$sLabel;
	}

	public function setAttributes($arAttributes){
		if (!empty($arAttributes))
			$this->arAttributes=$arAttributes;
	}

	public function getValue(){
		return $this->mValue;
	}

	public function getName(){
		return $this->sName;
	}
}