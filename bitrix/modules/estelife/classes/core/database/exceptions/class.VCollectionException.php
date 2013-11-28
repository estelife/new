<?php
namespace core\database\exceptions;
use core\exceptions\VException;

/**
 * Вероятно я забыл оставить описание. Пишите на почту, дам комментарий.
 * @since 03.10.13
 * @autor Dmitriy Konev <dnkonev@yandex.ru>
 */
class VCollectionException extends VException {
	public function __construct($sErrorText='', $nErrorCode=0){
		parent::__construct($sErrorText, $nErrorCode);
	}
}