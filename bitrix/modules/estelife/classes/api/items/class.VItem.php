<?php
namespace lists\items;
use core\types\VArray;
use lists\VList;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 02.12.13
 */
class VItem extends VArray {
	protected $obList;

	public function __construct(array $arData,VList $obList){
		parent::__construct($arData);
		$this->obList=$obList;
	}
}