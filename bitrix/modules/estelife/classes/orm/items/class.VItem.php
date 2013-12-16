<?php
namespace orm\items;
use lists\exceptions\VItemException;
use orm\VEntities;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 02.12.13
 */
class VItem {
	protected $obEntities;

	protected function __construct($sField=false,$mValue=false){
		$this->obEntities=new VEntities($this);

		($sField && $mValue) ?
			$this->find($sField,$mValue) :
			$this->create();
	}

	protected function create(){
		if($arProps=$this->obEntities->_props()){
			foreach($arProps as $sProp)
				$this->obEntities->_default($sProp);
		}
	}

	protected function find($sProp,$mValue){
		if(!$this->obEntities->_isset($sProp))
			throw new VItemException('unsupported property for this object');

		$this->obEntities->_length($sProp,$mValue);
		$this->obEntities->_type($sProp,$mValue);


	}

	public function save(){

	}

	public function delete(){

	}

	public function __get($sProp){
		return ($this->obEntities->_isset($sProp)) ?
			$this->{$sProp} :
			null;
	}

	public function __set($sProp,$sValue){
		if($this->obEntities->_isset($sProp)){
			$this->obEntities->_length($sProp,$sValue);
			$this->obEntities->_type($sProp,$sValue);
			$this->{$sProp}=$sValue;
		}
	}
}