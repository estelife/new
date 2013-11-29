<?php
namespace core\database;
use core\database\exceptions\VQueryException;

/**
 * Описание класса не задано. Обратитесь с вопросом на почту разработчика.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 03.10.13
 */
abstract class VQueryBuilder {

	/**
	 * Объект запроса, который инициировал создание контруктора
	 * @var VQuery
	 */
	protected $obQuery;

	/**
	 * Объект специальных запросов
	 * @var VSpecialQuery
	 */
	protected $obSpecialQuery;

	/**
	 * Массив с полями - значениями, которые в последующем будут подлежать записи
	 * @var array
	 */
	protected $arValues;

	/**
	 * Массив с указанием параметров среза выборки
	 * @var array
	 */
	protected $arSlice;

	/**
	 * Ассоциативный массив с правилами сортировки: ключи - поля, значения - направления сортировки
	 * @var array
	 */
	protected $arSort;

	/**
	 * Массив с полями, которые должны попасть в выборку и их алиасами
	 * @var array
	 */
	protected $arFields;

	/**
	 * Массив таблиц / коллекций, к которым осущестлвяется запрос и из алиасов
	 * @var array
	 */
	protected $arFrom;

	/**
	 * Массив с полями, по которым осущестлвяется группировка данных в запросе
	 * @var array
	 */
	protected $arGroup;

	/**
	 * Осуществляет инициализацию полей класса
	 * @param VQuery $obQuery
	 */
	public function __construct(VQuery $obQuery){
		$this->obQuery=$obQuery;
		$this->obSpecialQuery=$obQuery->driver()->createSpecialQuery();
		$this->arFrom=array();
		$this->arFields=array();
		$this->arSort=array();
		$this->arGroup=array();
		$this->arValues=array();
		$this->arSlice=array();
	}

	/**
	 * Условие, которое указывает, к какой таблице / коллекции, необходимо осущестлять обращение
	 * @param string $sTable
	 * @param string $sAlias
	 * @return VQueryBuilder
	 */
	public function from($sTable,$sAlias=''){
		if($this->obSpecialQuery->checkTable($sTable)){
			$this->obQuery->registerTable($sTable,$sAlias);
			$this->arFrom[]=array(
				'table'=>$sTable,
				'alias'=>$sAlias
			);
		}

		return $this;
	}

	/**
	 * Добавляет значение для записи в таблицу / коллекцию
	 * @param string $sField
	 * @param mixed $mValue
	 * @return VQueryBuilder
	 */
	public function value($sField,$mValue){
		$this->arValues[$sField]=$mValue;
		return $this;
	}

	/**
	 * Указывает, какое поле должно попасть в выборку
	 * допускается именовать поля, предваряя имя таблицей / коллекцией или её алиасом
	 * @param string|VQueryBuilder|VFunction $sField
	 * @param string $sAlias
	 * @return VQueryBuilder
	 */
	public function field($sField,$sAlias=''){
		$this->arFields[]=array(
			'field'=>$sField,
			'alias'=>$sAlias
		);
		return $this;
	}

	/**
	 * По какому принципу должна производиться сортировка. Направление сортировки указывается
	 * в качестве значения одной из констант класса VQuery
	 * @param string $sField
	 * @param string $sOrder
	 * @internal param int $nType
	 * @return VQueryBuilder
	 */
	public function sort($sField,$sOrder='asc'){
		$this->arSort[]=array(
			'field'=>$sField,
			'order'=>$sOrder
		);
		return $this;
	}

	/**
	 * Срез выборки, аналогично LIMIT в MySQL
	 * @param int $nFrom
	 * @param int $nCount
	 * @return VQueryBuilder
	 */
	public function slice($nFrom,$nCount=0){
		$nFrom=intval($nFrom);
		$nCount=intval($nCount);
		$this->arSlice=$nCount>0 ?
			array('from'=>$nFrom,'count'=>$nCount) :
			array('from'=>0, 'count'=>$nCount);
		return $this;
	}

	/**
	 * По каким полям осуществлять группировку данных в заросе
	 * @param string $sField
	 * @return VQueryBuilder
	 */
	public function group($sField){
		$this->arGroup[]=$sField;
		return $this;
	}

	/**
	 * Данный метод реализует HAVING в SQL, поддержка в других СУБД на данный момент не известна.
	 * Смотри реализацию для конкретной СУБД
	 * @throws VQueryException
	 * @return VFilter
	 */
	public function having(){
		throw new VQueryException('having is no support for this driver');
	}

	/**
	 * Объединение данных из нескольких таблиц / коллекций. Поддержка в других СУБД на данный момент не
	 * известна. Смотри реализацию для конкретной СУБД
	 * @throws VQueryException
	 * @return VJoin
	 */
	public function join(){
		throw new VQueryException('join is no support for this driver');
	}

	/**
	 * Объединение нескольких запрсоов
	 * @throws exceptions\VQueryException
	 * @return void
	 */
	public function union(){
		throw new VQueryException('union is no support for this driver');
	}

	/**
	 * Формирует часть запроса, отвечающуюу за генерацию фильтра запроса
	 * @return VFilter
	 */
	abstract public function filter();

	/**
	 * Генерирует запрос select. Вернее сказать, подготавливает данные для выполнения запроса.
	 * В случае с реляционками, понимающими sql, возвращает строку запроса, в случае, к примеру,
	 * с Mongo, возвращает массив, состоящий из сгененированных частей запроса. Для более точного
	 * понимания смотри конкретную реализацию.
	 * @return mixed
	 */
	abstract public function buildSelect();

	/**
	 * Генерирует запрос на сохранение данных. Смотри описание метода buildSelect
	 * @return mixed
	 */
	abstract public function buildInsert();

	/**
	 * Генерирует запрос на обновление данных. Смотри описание метода buildSelect
	 * @return mixed
	 */
	abstract public function buildUpdate();

	/**
	 * Генерирует запрос для удаление данных. Смотри описание метода buildDelete
	 * @return mixed
	 */
	abstract public function buildDelete();

	/**
	 * Делегирует запросы объекту для работы с функциями
	 * @param string $sFn
	 * @param array $arParams
	 * @return mixed
	 */
	abstract public function __call($sFn,array $arParams);
}