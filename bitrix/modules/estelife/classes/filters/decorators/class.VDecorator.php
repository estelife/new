<?php
namespace filters\decorators;
use core\database\mysql\VQuery;
use filters\VFilter;
use filters\VSession;

/**
 *
 * @author Maxim Shlemarev <shlemarev@gmail.com>
 * @since 30.01.14
 */

class VDecorator implements VFilter {
	protected $arFields;
	protected $sType;
	protected $arParams;

	protected function __construct($sType){
		$obFilter = !empty($_GET) ?
			new VQuery($sType) :
			new VSession($sType);

		$this->sType=$sType;
		$this->arParams=$obFilter->getParams();
		$this->arFields = array();
	}

	protected function setDefaultField($sField,$sValue){
		$this->arFields[$sField] = $sValue;
	}

	public function getParam($sKey){
		if(!isset($this->arFields[$sKey]))
			return null;

		$arParams=$this->getParams();
		return isset($arParams[$sKey]) ?
			$arParams[$sKey] :
			$this->arFields[$sKey];
	}

	public function getParams(){
		$arResult=array();

		foreach($this->arFields as $sField=>$sDefault){
			$arResult[$sField] = isset($this->arParams[$sField]) ?
				$this->arParams[$sField] :
				$sDefault;
		}

		return $arResult;
	}

	public function __destruct(){
		$obSession = new \filters\VSession($this->sType);
		$obSession->clearParams();

		if(!empty($this->arParams)){
			foreach($this->arParams as $sParam=>$sValue)
				$obSession->setParam($sParam, $sValue);
		}
	}
}