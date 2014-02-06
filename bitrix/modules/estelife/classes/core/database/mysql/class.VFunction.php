<?php
namespace core\database\mysql;
use core\database as db;
use core\types\VArray;

/**
 * Вероятно я забыл оставить описание. Пишите на почту, дам комментарий.
 * @since 18.10.13
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 */
class VFunction implements db\VFunction {
	protected $obQuery;
	protected $sFn;
	protected $obParams;
	protected $sThen;
	protected $sElse;

	/**
	 * @var VFilter
	 */
	protected $obFilter;

	/**
	 * Задает название функции, поля и дополнительные параметры
	 * @param db\VQuery $obQuery
	 * @param $sFn
	 * @param VArray $obParams
	 * @internal param $sField
	 */
	public function __construct(db\VQuery $obQuery, $sFn, VArray $obParams = null){
		$this->obQuery=$obQuery;
		$this->sFn=$sFn;
		$this->obParams=$obParams;
	}

	/**
	 * Добавляет условие для поиска результатов работы функции
	 * @param $sThen
	 * @param $sElse
	 * @return VFilter
	 */
	public function when($sThen, $sElse){
		$this->sThen=$sThen;
		$this->sElse=$sElse;
		$this->obFilter=new VFilter($this->obQuery);
		return $this->obFilter;
	}

	/**
	 * Генерирует запрос
	 * @throws \core\database\exceptions\VQueryException
	 * @return mixed
	 */
	public function make(){
		$this->checkFn();
		$sField=$this->obParams->one('field');

		if(!empty($this->sThen)){
			if(is_object($this->sThen) && $this->sThen instanceof VFunction)
				$this->sThen=$this->sThen->make();
			else if(is_string($this->sThen)){
				if(!$this->obQuery->driver()->createSpecialQuery()->checkField(
					$this->obQuery->getRegisteredTables(),
					$this->sThen
				))
					$this->sThen='\''.$this->sThen.'\'';
			}else
				throw new db\exceptions\VQueryException('incorrect type for then part: use string or object instance of VFunction');
		}

		if(!empty($this->sElse)){
			if(is_object($this->sElse) && $this->sElse instanceof VFunction)
				$this->sElse=$this->sElse->make();
			else if(is_string($this->sElse)){
				if(!$this->obQuery->driver()->createSpecialQuery()->checkField(
					$this->obQuery->getRegisteredTables(),
					$this->sElse
				))
					$this->sElse='\''.$this->sElse.'\'';
			}else
				throw new db\exceptions\VQueryException('incorrect type for else part: use string or object instance of VFunction');
		}

		switch($this->sFn){
			case '_if':
				if(!$this->obFilter ||
					($sFilter=$this->obFilter->make())==false)
					throw new db\exceptions\VQueryException('not set filter for if function');

				return 'IF(('.$sFilter.')'.
					($this->sThen ? ','.$this->sThen : '').''.
					($this->sThen && $this->sElse ? ','.$this->sElse : '').')';
				break;
			case '_min':
			case '_max':
			case '_count':
			case '_sum':
				if($this->obFilter &&
					$sFilter=$this->obFilter->make()){
					$sField='CASE '.$sField.' WHEN '.$sFilter.' THEN '.$this->sThen.' ELSE '.$this->sElse;
				}
				$sFn=mb_strtoupper(substr($this->sFn,1),'utf-8');
				return $sFn.'('.$sField.')';
				break;
			case '_concat':
				if($this->obParams->blank('data'))
					throw new db\exceptions\VQueryException('not found field for concat function');

				$arTemp=array();
				$arFields=$this->obParams->one('data',array());

				foreach($arFields as $sValue){
					if($this->obQuery->driver()->createSpecialQuery()->checkField(
						$this->obQuery->getRegisteredTables(),
						$sValue
					)){
						$arTemp[]=$sValue;
					}else{
						$arTemp[]='\''.$sValue.'\'';
					}
				}

				if(empty($arTemp))
					throw new db\exceptions\VQueryException('all fields for concat function is invalid');

				array_unshift($arTemp,$sField);
				return 'CONCAT('.implode(',',$arTemp).')';
				break;
			case '_substr':
				if($this->obParams->blank('data'))
					throw new db\exceptions\VQueryException('not found field for substr function');

				$arData=$this->obParams->one('data');
				return 'SUBSTR('.$sField.','.intval($arData[0]).((isset($arData[1])) ? ','.intval($arData[1]) : '').')';
				break;
			case '_regexp':
				$arParams=$this->obParams->one('data',array());

				if(empty($arParams))
					throw new db\exceptions\VQueryException('not found instruction for regexp function');

				$sExpression=reset($arParams);

				if(!is_string($sExpression) || empty($sExpression))
					throw new db\exceptions\VQueryException('invalid instruction for regexp function');

				return $sField.' REGEXP \''.$sExpression.'\'';
				break;
			case '_md5':
				try {
					$this->sFn='_concat';
					$sField=$this->make();
				}catch(db\exceptions\VQueryException $e){}

				return 'MD5('.$sField.')';
				break;
			case '_rand':
				return 'RAND()';
				break;
		}

		throw new db\exceptions\VQueryException('unsupported function');
	}

	/**
	 * Проверяет корректность функции
	 * @throws \core\database\exceptions\VQueryException
	 * @return void
	 */
	public function checkFn(){
		if(!in_array($this->sFn,array('_if','_min','_max','_count','_sum','_concat','_md5','_regexp','_substr','_rand')))
			throw new db\exceptions\VQueryException('unsupported function');

		if($this->sFn=='_if' || $this->sFn=='_concat' || $this->sFn=='_rand')
			return;

		if($this->obParams->blank('field'))
			throw new db\exceptions\VQueryException('field not found');

		$sField=$this->obParams->one('field');

		if(!$this->obQuery->driver()->createSpecialQuery()->checkField(
			$this->obQuery->getRegisteredTables(),
			$sField
		))
			throw new db\exceptions\VQueryException('incorrect field');

		$this->obParams->set('field',$sField);
	}

	/**
	 * Определяет вложенные функции
	 * @param $sFunction
	 * @param array $arParams
	 * @return \core\database\mysql\VFunction
	 */
	public function __call($sFunction,array $arParams){
		$arTemp=array(
			'field'=>(isset($arParams[0])) ?
				$arParams[0] : ''
		);
		unset($arParams[0]);
		$arTemp['data']=array_values($arParams);

		return new VFunction(
			$this->obQuery,
			$sFunction,
			new VArray($arTemp)
		);
	}
}