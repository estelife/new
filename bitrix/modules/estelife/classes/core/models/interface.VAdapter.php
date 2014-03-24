<?php
namespace core\models;

/**
 * Class VAdapter
 * Адаптер модели
 *
 * @package core\models
 */
interface VAdapter
{
	public function prepareData(array &$arData);
}