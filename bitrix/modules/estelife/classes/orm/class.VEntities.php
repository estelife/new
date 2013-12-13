<?php
namespace orm;
use core\types\VArray;
use orm\items\VItem;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 05.12.13
 */
class VEntities {
	private static $arCache=array();
	private static $sParams;
	protected static $arParams=array(
		'table',
		'engine',
		'key',
		'join',
		'field',
		'length',
		'default'
	);
	private $sClass;
	private $obItem;

	public function __construct(VItem $obItem){
		$this->sClass=get_class($obItem);
		$this->obItem=$obItem;
		$this->parseItem($obItem);
	}
	public function __destruct(){}

	protected function parseItem(VItem $obItem){
		if(!self::$sParams)
			self::$sParams=implode('|',self::$arParams);

		if(!isset(self::$arCache[$this->sClass])){
			self::$arCache[$this->sClass]=array();

			$obReflection=new \ReflectionObject($obItem);
			$sComment=$obReflection->getMethod('__construct')
				->getDocComment();

			if(($arMethods=$this->parseComment($sComment))===false)
				return;

			if(empty($arMethods['table']))
				return;

			$arMethods['engine']=!empty($arMethods['engine']) ? $arMethods['engine'] : 'MyISAM';
			$arProps=$obReflection->getProperties();

			if(empty($arProps))
				return;

			self::$arCache[$this->sClass]['table']=$arMethods['table'];
			self::$arCache[$this->sClass]['engine']=$arMethods['engine'];
			self::$arCache[$this->sClass]['fields']=array();

			foreach($arProps as $obProp){
				$sComment=$obProp->getDocComment();

				if(($arMethods=$this->parseComment($sComment))===false)
					continue;

				self::$arCache[$this->sClass]['fields'][$obProp->getName()]=$arMethods;
			}
		}
	}

	protected function parseComment($sComment){
		$sComment=trim($sComment);

		if(preg_match_all('#@('.self::$sParams.')(.*)?#i',$sComment,$arMatches)){
			$arMethods=array();

			foreach($arMatches[1] as $nKey=>$sMethod){
				$arMatches[2][$nKey]=trim($arMatches[2][$nKey]);
				$arMethods[$sMethod]=(!empty($arMatches[2][$nKey])) ?
					explode(',',preg_replace('#^\(?(.*)\)?$#','$1',$arMatches[2][$nKey])) :
					array();
			}

			return $arMethods;
		}

		return false;
	}

	public function _props(){
		return (!empty(self::$arCache[$this->sClass]['fields'])) ?
			array_keys(self::$arCache[$this->sClass]['fields']) :
			false;
	}

	public function _fields(){
		if(!empty(self::$arCache[$this->sClass]))
			return self::$arCache[$this->sClass]['fields'];

		return false;
	}

	public function _field($sProp){
		if($this->_isset($sProp))
			return self::$arCache[$this->sClass]['fields'][$sProp];

		return false;
	}

	public function _isset($sProp){
		return !empty(self::$arCache[$this->sClass]['fields'][$sProp]);
	}

	public function _default($sProp){
		if(($arField=$this->_field($sProp)) && isset($arField['default'])){
			$this->obItem->{$sProp}=(isset($arField['default'][0])) ?
				$arField['default'][0] : '';
		}else
			$this->obItem->{$sProp}=null;
	}

	public function _length($sProp,&$sValue){
		if(($arField=$this->_field($sProp)) && isset($arField['length'][0])){
			$sValue=(strlen($sValue)>$arField['length'][0]) ?
				substr($sValue,0,$arField['length'][0]) :
				$sValue;
		}
	}

	public function _type($sProp,&$sValue){
		if($arField=$this->_field($sProp)){
			$sType=(isset($arField['type'][0])) ?
				$arField['type'][0] :
				'string';

			switch($sType){
				case 'int':
				case 'tinyint':
				case 'bigint':
					$sType='int';
					break;
				case 'float':
					$sType='float';
					break;
				case 'double':
					$sType='double';
					break;
				case 'varchar':
				case 'char':
				case 'text':
				case 'string':
				default:
					$sType='string';
			}

			settype($sValue,$sType);
		}
	}

	public function _join($sProp){

	}

	public function _key($sProp){

	}
}