<?php
namespace core\database\mysql;
use core\database as db;
use core\database\exceptions\VQueryBuildException;
use core\types\VArray;

/**
 * Описание класса не задано. Обратитесь с вопросом на почту разработчика.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 05.10.13
 */
class VQueryBuilder extends db\VQueryBuilder {
	/**
	 * @var VJoin
	 */
	protected $obJoin;
	/**
	 * @var VFilter
	 */
	protected $obFilter;
	/**
	 * @var VFilter
	 */
	protected $obHaving;
	protected $arUnions;

	public function __construct(db\VQuery $obQuery){
		parent::__construct($obQuery);
	}

	public function having(){
		if(!$this->obHaving)
			$this->obHaving=new VFilter($this->obQuery);
		return $this->obHaving;
	}

	public function join(){
		if(!$this->obJoin)
			$this->obJoin=new VJoin($this->obQuery);
		return $this->obJoin;
	}

	/**
	 * Формирует часть запроса, отвечающуюу за генерацию фильтра запроса
	 * @return VFilter
	 */
	public function filter(){
		if(!$this->obFilter)
			$this->obFilter=new VFilter($this->obQuery);
		return $this->obFilter;
	}

	/**
	 * Объединяет несколько запросов
	 * @return void
	 */
	public function union(){
		$obCurrent=clone $this;

		if(!empty($this->arUnions)){
			$obFirst=reset($this->arUnions);
			$obCurrent->arFields=$obFirst->arFields;
			$obCurrent->arFrom=$obFirst->arFrom;
		}

		if($this->obJoin)
			$obCurrent->obJoin=clone $this->obJoin;

		$this->obFilter=null;
		$this->obHaving=null;
		$obCurrent->arUnions=array();
		$this->arUnions[]=$obCurrent;
	}

	/**
	 * Методы ниже генерируют массив с соответствующими частями запроса,
	 * при этом наличие тех или иных частей зависит от предварительных действий клиента
	 * @throws \core\database\exceptions\VQueryBuildException
	 * @return string
	 */
	public function buildSelect(){
		if(empty($this->arFrom))
			throw new VQueryBuildException('undefined table');

		$sSelect='';

		if(!empty($this->arUnions)){
			/**
			 * @var VQueryBuilder $obBuilder
			 * @var string[] $arSelects
			 */
			$arSelects=array();

			foreach($this->arUnions as $obBuilder)
				$arSelects[]=$obBuilder->buildSelect();

			$sSelect='('.implode(') UNION (',$arSelects).')';
		}else{
			$sFields='*';

			if(!empty($this->arFields)){
				$arTemp=array();

				foreach($this->arFields as $arField){
					if(is_object($arField['field']) && $arField['field'] instanceof VFunction){
						$arField['field']=$arField['field']->make();
					}else if(is_object($arField['field']) && $arField['field'] instanceof VQueryBuilder){
						$arField['field']=$arField['field']->buildSelect();
					}else if(!preg_match('#\.?\*#',$arField['field']) && !$this->obSpecialQuery->checkField(
							$this->obQuery->getRegisteredTables(),
							$arField['field']))
						continue;

					$arTemp[]=$arField['field'].(!empty($arField['alias']) ?
							' AS '.$arField['alias'] : '');
				}

				$sFields=implode(',', $arTemp);
			}

			$arTemp=array();

			foreach($this->arFrom as $arFrom)
				$arTemp[]='`'.$arFrom['table'].'`'.
					(!empty($arFrom['alias']) ? ' '.$arFrom['alias'] : '');

			$sSelect='SELECT '.$sFields.' FROM '.implode(', ',$arTemp);

			if($this->obJoin)
				$sSelect.=' '.$this->obJoin->make();

			if($this->obFilter &&
				$sFilter=$this->obFilter->make()){
				$sSelect.=' WHERE '.$sFilter;
			}

			if(!empty($this->arGroup)){
				$arTemp=array();

				foreach($this->arGroup as $sField){
					if(!$this->obSpecialQuery->checkField(
						$this->obQuery->getRegisteredTables(),
						$sField
					))
						continue;

					$arTemp[]=$sField;
				}

				$sSelect.=' GROUP BY '.implode(', ',$arTemp);
			}

			if($this->obHaving)
				$sSelect.=' HAVING '.$this->obHaving->make();

			if(!empty($this->arSort) ){
				$arTemp=array();

				foreach($this->arSort as $arSort){
					if(is_object($arSort['field']) &&
						$arSort['field'] instanceof VFunction)
						$arSort['field']=$arSort['field']->make();
					else if(!$this->obSpecialQuery->checkField(
						$this->obQuery->getRegisteredTables(),
						$arSort['field']
					))
						continue;

					$arTemp[]=$arSort['field'].' '.$arSort['order'];
				}

				if(!empty($arTemp))
					$sSelect.=' ORDER BY '.implode(', ',$arTemp);
			}

			if(!empty($this->arSlice))
				$sSelect.=' LIMIT '.$this->arSlice['from'].', '.$this->arSlice['count'];
		}

		return $sSelect;
	}

	/**
	 * Генерирует запрос на сохранение данных. Возвращает строку сгененированного запроса.
	 * @return string
	 * @throws \core\database\exceptions\VQueryBuildException
	 */
	public function buildInsert(){
		if(empty($this->arFrom))
			throw new VQueryBuildException('undefined table');

		if(count($this->arFrom)>1)
			throw new VQueryBuildException('when added to give only one table');

		if(empty($this->arValues))
			throw new VQueryBuildException('values not set for insert query');

		$arFrom=reset($this->arFrom);
		$arFields=array_keys($this->arValues);

		foreach($arFields as $nKey=>&$sField){
			$sValue=$this->arValues[$sField];
			unset($this->arValues[$sField]);

			if($this->obSpecialQuery->checkField($arFrom['table'],$sField,$this->arValues[$sField])){
				$this->arValues[$sField]=$sValue;
			}else{
				unset($arFields[$nKey]);
			}
		}

		$arValues=array_values($this->arValues);
		$arTemp=array();

		foreach($arValues as $nKey=>$mValue){
			if(is_array($mValue)){
				foreach($mValue as $nInnerKey=>$sValue){
					if(is_null($sValue)) {
						$arTemp[$nInnerKey][] = 'null';
					}else{
						$sValue = $this->obQuery->driver()->escapeString($sValue);
						$arTemp[$nInnerKey][] = is_numeric($sValue) ? $sValue : '\''.$sValue.'\'';
					}
				}
			}else if(is_null($mValue)) {
				$arTemp[0][] = 'null';
			}else{
				$mValue = $this->obQuery->driver()->escapeString($mValue);
				$arTemp[0][] = is_numeric($mValue) ? $mValue : '\''.$mValue.'\'';
			}
		}

		$sInset='INSERT INTO `'.$arFrom['table'].'` ('.implode(', ',$arFields).')';

		foreach($arTemp as $arValues)
			$sInset.=' VALUES ('.implode(', ',$arValues).')';

		return $sInset;
	}

	/**
	 * Возвращает запрос на обновление данных. Возвращает строку запроса.
	 * @return string
	 * @throws \core\database\exceptions\VQueryBuildException
	 */
	public function buildUpdate(){
		if(empty($this->arFrom))
			throw new VQueryBuildException('undefined table');

		if(count($this->arFrom)>1)
			throw new VQueryBuildException('when added to give only one table');

		if(empty($this->arValues))
			throw new VQueryBuildException('values not set for update query');

		$arFrom=reset($this->arFrom);
		$arFields=array();

		foreach($this->arValues as $sKey=>$mValue){
			if(!$this->obSpecialQuery->checkField($arFrom['table'],$sKey))
				continue;

			if (is_null($mValue)) {
				$mValue = 'null';
			} else {
				$mValue=$this->obQuery->driver()->escapeString($mValue);
				$mValue = is_numeric($mValue) ? $mValue : '\''.$mValue.'\'';
			}

			$arFields[]=$sKey.'='.$mValue;
		}

		if(empty($arFields))
			throw new VQueryBuildException('all fields is invalid');

		$sUpdate='UPDATE '.$arFrom['table'].' SET '.implode(', ',$arFields);

		if($this->obFilter)
			$sUpdate.=' WHERE '.$this->obFilter->make();

		return $sUpdate;
	}

	/**
	 * Генерирует запрос на удаление данных. Возвращает строку запроса.
	 * @return string
	 * @throws \core\database\exceptions\VQueryBuildException
	 */
	public function buildDelete(){
		if(empty($this->arFrom))
			throw new VQueryBuildException('undefined table');

		if(count($this->arFrom)>1)
			throw new VQueryBuildException('when added to give only one table');

		$arFrom=reset($this->arFrom);
		$sDelete='DELETE FROM '.$arFrom['table'];

		if($this->obFilter)
			$sDelete.=' WHERE '.$this->obFilter->make();

		return $sDelete;
	}

	/**
	 * Генерирует запрос на получение кол-ва строк, которые должны попасть в выборку,
	 * согласно указанному запросу
	 * @throws \core\database\exceptions\VQueryBuildException
	 * @return string
	 */
	public function buildCount(){
		if(empty($this->arFrom))
			throw new VQueryBuildException('undefined table');

		$arTemp=array();

		foreach($this->arFrom as $arFrom)
			$arTemp[]='`'.$arFrom['table'].'`'.
				(!empty($arFrom['alias']) ? ' '.$arFrom['alias'] : '');

		$sSelect='SELECT COUNT(*) as count FROM '.implode(', ',$arTemp);

		if($this->obJoin)
			$sSelect.=' '.$this->obJoin->make();

		if($this->obFilter &&
			$sFilter=$this->obFilter->make()){
			$sSelect.=' WHERE '.$sFilter;
		}

		if(!empty($this->arGroup)){
			$arTemp=array();

			foreach($this->arGroup as $sField){
				if(!$this->obSpecialQuery->checkField(
					$this->obQuery->getRegisteredTables(),
					$sField
				))
					continue;

				$arTemp[]=$sField;
			}

			$sSelect.=' GROUP BY '.implode(', ',$arTemp);
		}

		return $sSelect;
	}

	/**
	 * Делегирует запросы объекту для работы с функциями
	 * @param string $sFunction
	 * @param array $arParams
	 * @return mixed
	 */
	public function __call($sFunction, array $arParams){
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