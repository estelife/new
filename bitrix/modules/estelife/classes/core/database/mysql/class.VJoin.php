<?php
namespace core\database\mysql;
use core\database as db;
use core\database\exceptions\VJoinException;
use core\exceptions\VException;

/**
 * Класс реализует формирование части sql, отвечающей за объединение данных таблиц
 * @since 12.05.2012
 * @version 0.1
 */
class VJoin implements db\VJoin {
	private $obQuery;
	private $arJoin;
	private $bJoinContext;
	/**
	 * @var VFilter
	 */
	private $obFilter;
	private $arTo;
	private $arFrom;
	private $obSpecialQuery;

	public function __construct(db\VQuery $obQuery,$bJoinContext=false){
		$this->obQuery=$obQuery;
		$this->arJoin=array();
		$this->bJoinContext=$bJoinContext;
		$this->obSpecialQuery=$this->obQuery->driver()->createSpecialQuery();
	}

	/**
	 * Генерит запрос для объединения данныз из 2-х таблиц / коллекций по принципу LEFT JOIN (SQL)
	 * @return db\VJoin
	 */
	public function _left(){
		if($this->bJoinContext)
			return $this;

		$obJoin=new VJoin($this->obQuery,true);
		$this->arJoin['left'][]=$obJoin;

		return $obJoin;
	}

	/**
	 * Генерит запрос для объединения данных из 2-х таблиц по принципу RIGHT JOIN (SQL)
	 * @return db\VJoin
	 */
	public function _right(){
		if($this->bJoinContext)
			return $this;

		$obJoin=new VJoin($this->obQuery,true);
		$this->arJoin['right'][]=$obJoin;

		return $obJoin;
	}

	/**
	 * Генерит запрос для объединения данных из 2-х таблиц по принципу INNER JOIN (SQL)
	 * @return db\VJoin
	 */
	public function _inner(){
		if($this->bJoinContext)
			return $this;

		$obJoin=new VJoin($this->obQuery,true);
		$this->arJoin['inner'][]=$obJoin;

		return $obJoin;
	}

	/**
	 * Генерит условие объединения данных из 2-х таблиц
	 * @throws \core\database\exceptions\VJoinException
	 * @return VFilter
	 */
	public function _cond(){
		if(!$this->bJoinContext)
			throw new VJoinException('calling only examples join');

		if(!$this->obFilter)
			$this->obFilter=new VFilter($this->obQuery);

		return $this->obFilter;
	}

	/**
	 * Указывает на какую таблицу делаем join
	 * @param string $sTable
	 * @param string $sField
	 * @param string $sAlias
	 * @throws \core\database\exceptions\VJoinException
	 * @return db\VJoin
	 */
	public function _to($sTable, $sField, $sAlias = ''){
		if(!$this->bJoinContext)
			throw new VJoinException('calling only examples join');

		if(!$this->obSpecialQuery->checkTable($sTable) ||
			!$this->obSpecialQuery->checkField($sTable,$sField))
			throw new VJoinException('incorrect joined table or field');

		$sField=(!empty($sAlias)) ?
			$sAlias.'.'.$sField :
			$sTable.'.'.$sField;

		$this->arTo=array(
			'table'=>$sTable,
			'field'=>$sField,
			'alias'=>$sAlias
		);
		$this->obQuery->registerTable($sTable,$sAlias);
		return $this;
	}

	/**
	 * Указывает с какой таблицы делаем join
	 * @param string $sTable
	 * @param string $sField
	 * @throws \core\database\exceptions\VJoinException
	 * @internal param string $sAlias
	 * @return db\VJoin
	 */
	public function _from($sTable, $sField){
		if(!$this->bJoinContext)
			throw new VJoinException('calling only examples join');

		$arTables=$this->obQuery->getRegisteredTables();

		if(isset($arTables[$sTable])){
			$sAlias=$sTable;
			$sTable=$arTables[$sAlias];
		}else{
			if(!$this->obSpecialQuery->checkTable($sTable))
				throw new VJoinException('incorrect table');

			$arTables=array_flip($arTables);
			$sAlias=(!is_numeric($arTables[$sTable])) ?
				$arTables[$sTable] :
				$sTable;
		}

		if(!$this->obSpecialQuery->checkField($sTable,$sField))
			throw new VJoinException('incorrect field');

		$this->arFrom=array(
			'table'=>$sTable,
			'field'=>$sAlias.'.'.$sField.''
		);
		$this->obQuery->registerTable($sTable);
		return $this;
	}

	/**
	 * Генерирует часть запроса, которая отвечает за объединение
	 * @throws \core\database\exceptions\VJoinException
	 * @return string
	 */
	public function make(){
		$sResult='';

		if(!$this->bJoinContext){
			$arTemp=array();

			foreach($this->arJoin as $sKey=>$arJoin){
				switch($sKey){
					case 'left':
						$sKey='LEFT JOIN ';
						break;
					case 'right':
						$sKey='RIGHT JOIN ';
						break;
					case 'inner':
						$sKey='INNER JOIN ';
						break;
					default:
						continue;
				}

				foreach($arJoin as $obJoin)
					$arTemp[]=$sKey.$obJoin->make();
			}

			$sResult=implode(' ',$arTemp);
		}else{
			if(empty($this->arTo) || empty($this->arFrom))
				throw new VJoinException('unknown table from or to');

			$sResult=$this->arTo['table'];

			if(!empty($this->arTo['alias']))
				$sResult.=' AS '.$this->arTo['alias'];

			$sResult.=' ON '.$this->arTo['field'].'='.$this->arFrom['field'];

			if($this->obFilter)
				$sResult.=' AND '.$this->obFilter->make();
		}

		$this->arJoin=null;
		$this->arFrom=null;
		$this->arTo=null;
		$this->obFilter=null;
		return $sResult;
	}
}
