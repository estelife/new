<?php
namespace core\database\mysql;
use core\database as db;
use core\database\VDatabase;

/**
 * Класс реализует формирование части sql, отвечающей за сортировку
 * @since 12.05.2012
 * @version 0.1
 */
class VFilter implements db\VFilter {
	const LIKE_BEFORE=2;
	const LIKE_AFTER=4;

	private $obQuery;
	private $obSpecialQuery;
	private $arFilter;
	private $arOr;

	public function __construct(db\VQuery $obQuery){
		$this->obQuery=$obQuery;
		$this->obSpecialQuery=$obQuery->driver()->createSpecialQuery();
		$this->arFilter=array();
		$this->arOr=array();
	}

	/**
	 * Генерирует ИЛИ выражение, при этом возвращаемый объект не равен текущему
	 * @return db\VFilter
	 */
	public function _or(){
		$obFilter=new VFilter($this->obQuery);
		$this->arOr[]=$obFilter;
		return $obFilter;
	}

	/**
	 * Условие IS
	 * @param $sField
	 * @param $mValue
	 * @return mixed
	 */
	public function _is($sField,$mValue){
		$this->arFilter[]=array(
			'field'=>$sField,
			'value'=>$mValue ,
			'cond'=>'[v_field] IS [v_value]'
		);
		return $this;
	}

	/**
	 * Условие РАВНО
	 * @param $sField
	 * @param $mValue
	 * @return VFilter
	 */
	public function _eq($sField, $mValue){
		$this->arFilter[]=array(
			'field'=>$sField,
			'value'=>$mValue,
			'cond'=>'[v_field]=[v_value]'
		);
		return $this;
	}

	/**
	 * Условие НЕ РАВНО
	 * @param $sField
	 * @param $mValue
	 * @return mixed
	 */
	public function _ne($sField,$mValue){
		$mValue=$this->obQuery->driver()->escapeString($mValue);
		$this->arFilter[]=array(
			'field'=>$sField,
			'value'=>$mValue,
			'cond'=>'[v_field]!=[v_value]'
		);
		return $this;
	}

	/**
	 * Условие НЕ
	 * @param $sField
	 * @param $mValue
	 * @return VFilter
	 */
	public function _not($sField, $mValue){
		$this->arFilter[]=array(
			'field'=>$sField,
			'value'=>$mValue,
			'cond'=>'[v_field] NOT [v_value]'
		);
		return $this;
	}

	/**
	 * Условие МЕНЬШЕ
	 * @param $sField
	 * @param $mValue
	 * @return VFilter
	 */
	public function _lt($sField, $mValue){
		$mValue=$this->obQuery->driver()->escapeString($mValue);
		$this->arFilter[]=array(
			'field'=>$sField,
			'value'=>$mValue,
			'cond'=>'[v_field]<[v_value]'
		);
		return $this;
	}

	/**
	 * Условие МЕНЬШЕ или РАВНО
	 * @param $sField
	 * @param $mValue
	 * @return VFilter
	 */
	public function _lte($sField, $mValue){
		$mValue=$this->obQuery->driver()->escapeString($mValue);
		$this->arFilter[]=array(
			'field'=>$sField,
			'value'=>$mValue,
			'cond'=>'[v_field]<=[v_value]'
		);
		return $this;
	}

	/**
	 * Условие БОЛЬШЕ
	 * @param $sField
	 * @param $mValue
	 * @return VFilter
	 */
	public function _gt($sField, $mValue){
		$mValue=$this->obQuery->driver()->escapeString($mValue);
		$this->arFilter[]=array(
			'field'=>$sField,
			'value'=>$mValue,
			'cond'=>'[v_field]>[v_value]'
		);
		return $this;
	}

	/**
	 * Условие БОЛЬШЕ или РАВНО
	 * @param $sField
	 * @param $mValue
	 * @return VFilter
	 */
	public function _gte($sField, $mValue){
		$mValue=$this->obQuery->driver()->escapeString($mValue);
		$this->arFilter[]=array(
			'field'=>$sField,
			'value'=>$mValue,
			'cond'=>'[v_field]>=[v_value]'
		);
		return $this;
	}

	/**
	 * Условие поиска по части строки
	 * @param $sField
	 * @param $mValue
	 * @param $nType
	 * @return VFilter
	 */
	public function _like($sField, $mValue, $nType){
		if($nType>0){
			$mValue=$this->obQuery->driver()->escapeString($mValue);

			if(($nType&self::LIKE_BEFORE)==self::LIKE_BEFORE)
				$mValue='%'.$mValue;

			if(($nType&self::LIKE_AFTER)==self::LIKE_AFTER)
				$mValue.='%';

			$this->arFilter[]=array(
				'field'=>$sField,
				'value'=>$mValue,
				'cond'=>'[v_field] LIKE [v_value]'
			);
		}

		return $this;
	}

	/**
	 * Условие отсутсвия части указанной строки
	 * @param $sField
	 * @param $mValue
	 * @param $nType
	 * @return VFilter
	 */
	public function _notLike($sField, $mValue, $nType){
		if($nType>0){
			$mValue=$this->obQuery->driver()->escapeString($mValue);

			if($nType&self::LIKE_BEFORE==self::LIKE_BEFORE)
				$mValue='%'.$mValue;

			if($nType&self::LIKE_AFTER==self::LIKE_AFTER)
				$mValue.='%';

			$this->arFilter[]=array(
				'field'=>$sField,
				'value'=>$mValue,
				'cond'=>'[v_field] NOT LIKE [v_value]'
			);
		}

		return $this;
	}

	/**
	 * Услловие соответствия хотя бы одному элементу списка
	 * @param $sField
	 * @param array $arValue
	 * @return VFilter
	 */
	public function _in($sField, array $arValue){
		if(!empty($arValue)){
			$arTemp=array();

			foreach($arValue as $sValue){
				if(is_array($sValue) || is_object($sValue))
					continue;

				$arTemp[]=$this->obQuery->driver()->escapeString($sValue);
			}

			$this->arFilter[]=array(
				'field'=>$sField,
				'value'=>implode('\',\'',$arTemp),
				'cond'=>'[v_field] IN ([v_value])'
			);
		}

		return $this;
	}

	/**
	 * Условие противоречия всем элементам списка
	 * @param $sField
	 * @param array $arValue
	 * @return VFilter
	 */
	public function _notIn($sField, array $arValue){
		if(!empty($arValue)){
			$arTemp=array();

			foreach($arValue as $sValue){
				if(is_array($sValue) || is_object($sValue))
					continue;

				$arTemp[]=$this->obQuery->driver()->escapeString($sValue);
			}

			$this->arFilter[]=array(
				'field'=>$sField,
				'value'=>implode('\',\'',$arTemp),
				'cond'=>'[v_field] NOT IN ([v_value])'
			);
		}

		return $this;
	}

	/**
	 * Генерит выражения для проверки соответсвия значению NULL
	 * @param $sField
	 * @return db\VFilter
	 */
	public function _isNull($sField){
		$this->arFilter[]=array(
			'field'=>$sField,
			'cond'=>'ISNULL([v_field])'
		);
		return $this;
	}

	/**
	 * Генерит выражение для проверки не совпадения со значением NULL
	 * @param $sField
	 * @return db\VFilter
	 */
	public function _notNull($sField){
		$this->arFilter[]=array(
			'field'=>$sField,
			'cond'=>'NOT ISNULL([v_field])'
		);
		return $this;
	}

	/**
	 * Условие РЕГУЛЯРНОЕ ВЫРАЖЕНИЕ
	 * @param $sField
	 * @param $sRegex
	 * @return VFilter
	 */
	public function _regex($sField, $sRegex){
		$this->arFilter[]=array(
			'field'=>$sField,
			'value'=>$sRegex,
			'cond'=>'[v_field] REGEXP [v_value]'
		);
		return $this;
	}

	/**
	 * Условие не совпадения по регулярному выражению
	 * @param $sField
	 * @param $sRegex
	 * @return VFilter
	 */
	public function _notRegex($sField, $sRegex){
		$this->arFilter[]=array(
			'field'=>$sField,
			'value'=>$sRegex,
			'cond'=>'[v_field] NOT REGEXP [v_value]'
		);
		return $this;
	}

	/**
	 * Условие ПОЛНОТЕКСТОВЫЙ ПОИСК
	 * @param $sField
	 * @param $mValue
	 * @return VFilter
	 */
	public function _match($sField, $mValue){
		$mValue=$this->obQuery->driver()->escapeString($mValue);
		$this->arFilter[]=array(
			'field'=>$sField,
			'value'=>$mValue,
			'cond'=>'MATCH([v_field]) AGAINST ([v_value])'
		);
		return $this;
	}

	/**
	 * Генерирует запрос
	 * @return string
	 */
	public function make(){
		$arTemp=array();
		$arFilter=array();

		if(!empty($this->arOr)){
			foreach($this->arOr as $obFilter){
				if($sMake=$obFilter->make())
					$arTemp[]=$sMake;
			}

			if(!empty($arTemp))
				$arFilter[]='(('.implode(') OR (',$arTemp).'))';
		}

		if(!empty($this->arFilter)){
			foreach($this->arFilter as $arTemp){
				if(is_object($arTemp['field']) && $arTemp['field'] instanceof VFunction)
					$arTemp['field']=$arTemp['field']->make();
				else if(!$this->checkField($arTemp['field']))
					continue;

				if(array_key_exists('value',$arTemp)){
					if(is_null($arTemp['value']))
						$arTemp['value']='NULL';
					else if(is_object($arTemp['value']) && $arTemp['value'] instanceof VFunction)
						$arTemp['value']=$arTemp['value']->make();
					else
						$arTemp['value']=(is_numeric($arTemp['value'])) ?
							$arTemp['value'] :
							'\''.$arTemp['value'].'\'';
				}else
					$arTemp['value']='';

				$arFilter[]=str_replace(
					array('[v_field]','[v_value]'),
					array($arTemp['field'],$arTemp['value']),
					$arTemp['cond']
				);
			}
		}

		unset($arTemp);
		return (!empty($arFilter)) ?
			implode(' AND ', $arFilter) :
			false;
	}

	/**
	 * Проверяет корректность поле, заключает его в апострофы
	 * @param $sField
	 * @return bool
	 */
	private function checkField(&$sField){
		if(preg_match('#^([\w_\-]+\.)?([\w_\-]+)$#',$sField,$arMatches)){
			$arTables=$this->obQuery->getRegisteredTables();

			if(!empty($arMatches[1])){
				$arMatches[1]=mb_substr($arMatches[1],0,-1,'utf-8');

				if(!isset($arTables[$arMatches[1]]) ||
					!$this->obSpecialQuery->checkTable($arTables[$arMatches[1]]) ||
					!$this->obSpecialQuery->checkField($arTables[$arMatches[1]],$arMatches[2]))
					return false;

				$arMatches[1].='.';
			}else{
				if(!$this->obSpecialQuery->checkField($arTables,$arMatches[2]))
					return false;

				$arMatches[1]='';
			}

			$sField=$arMatches[1].''.$arMatches[2].'';
			return true;
		}

		return false;
	}
}
