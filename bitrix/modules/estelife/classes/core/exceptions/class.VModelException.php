<?php
namespace core\exceptions;

/**
 * Вероятно я забыл оставить описание. Пишите на почту, дам комментарий.
 * @since 03.10.13
 * @autor Dmitriy Konev <dnkonev@yandex.ru>
 */
class VModelException extends VException {
	public function __construct($sErrorText='', $nErrorCode=0){
		parent::__construct($sErrorText, $nErrorCode);
	}
}