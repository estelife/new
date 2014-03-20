<?php
namespace core\models;

/**
 * Class VSettings
 * Настройки для модели списка
 *
 * @package core\models
 */
class VSettings
{
	/**
	 * Инициализует значения по умолчанию
	 */
	public function __construct()
	{
		$this->nLimit = isset($_GET['limit']) ? abs(intval($_GET['limit'])) : 10;
		$this->nPage = isset($_GET['page']) ? abs(intval($_GET['page'])) : 1;
	}

	/**
	 * Возвращает смещение относительно нуля
	 *
	 * @return int
	 */
	final public function getOffset()
	{
		$nLimit = $this->getLimit();
		$nPage = $this->getPage();

		if (!$nPage)
			$nPage = 1;

		if (!$nLimit)
			$nLimit = 10;

		$nPage -= 1;
		$nOffset = $nPage*$nLimit;
		return $nOffset;
	}

	/**
	 * Возвращает длинну списка
	 *
	 * @return int
	 */
	public function getLimit()
	{
		return $this->nLimit;
	}

	/**
	 * Вовзращает поле для сортировки
	 *
	 * @return string
	 */
	public function getSortField()
	{
		return $this->sField;
	}

	/**
	 * Возвращает порядок сортировки
	 *
	 * @return string
	 */
	public function getSortOrder()
	{
		return $this->sOrder;
	}

	/**
	 * Возвращает номер текущей страницы
	 *
	 * @return int
	 */
	public function getPage()
	{
		return $this->nPage;
	}

	/**
	 * Номер текущей страницы
	 *
	 * @var int
	 */
	protected $nPage;

	/**
	 * Смещение относительно нуля
	 *
	 * @var int
	 */
	protected $nOffset;

	/**
	 * Кол-во записей в результирющем наборе
	 *
	 * @var int
	 */
	protected $nLimit;

	/**
	 * Поле для сортировки
	 *
	 * @var string
	 */
	protected $sField;

	/**
	 * Порядок сортировкаи
	 *
	 * @var
	 */
	protected $sOrder;
}