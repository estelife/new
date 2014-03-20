<?php
namespace core\models;

use core\database\VDatabase;

/**
 * Class VList
 * Класс позволяет создает интерфейс для работы со списками данных. Позволяет создать
 * единую схему работы с подобными данными
 *
 * @package core\components\models
 */
abstract class VList
{
	/**
	 * Генерирует конструктор запроса на основании драйвера, с которым работает класс
	 */
	public function __construct()
	{
		$obDriver = VDatabase::driver();
		$this->obQuery = $obDriver->createQuery();
		$this->obBuilder = $this->obQuery->builder();
	}

	/**
	 * Задает класс настроек списка
	 *
	 * @param VSettings $obSettings
	 */
	public function setSettings(VSettings $obSettings)
	{
		$this->obSettings = $obSettings;
	}

	/**
	 * Позволяет задать фильтр
	 *
	 * @param \filters\VFilter $obFilter
	 */
	public function setFilter(\filters\VFilter $obFilter)
	{
		$this->obFilter = $obFilter;
	}

	/**
	 * Собственно, основа основ. Позволяет получить список, при этом
	 * еще и применить адаптер к каждому элементу
	 *
	 * @param VAdapter $obAdapter
	 * @return array
	 */
	public function getItems(VAdapter $obAdapter = null)
	{
		if ($this->obSettings) {
			if ($sSortField = $this->obSettings->getSortField()) {
				$sSortOrder = $this->obSettings->getSortOrder();
				$sSortOrder = $sSortOrder == 'asc' ? SORT_ASC : SORT_DESC;
				$this->obBuilder->sort($sSortField, $sSortOrder);
			}

			$this->obBuilder->slice(
				$this->obSettings->getOffset(),
				$this->obSettings->getLimit()
			);
		}

		$arData = $this->obQuery->select();

		if ($arData && $obAdapter) {
			foreach($arData as &$arValue)
				$obAdapter->prepareData($arValue);
		}

		return $arData;
	}

	/**
	 * Ссылка на объект настроек списка
	 *
	 * @var VSettings
	 */
	protected $obSettings;

	/**
	 * Ссылка на объект фильтра
	 *
	 * @var \filters\VFilter
	 */
	protected $obFilter;

	/**
	 * Ссылка на конструктор запросов, с которым работате класс
	 *
	 * @var \core\database\VQuery
	 */
	private $obQuery;

	/**
	 * Ссылка на конструтор запроса
	 *
	 * @var \core\database\VQueryBuilder
	 */
	protected $obBuilder;
}