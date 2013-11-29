<?php
namespace core\database\exceptions;
use core\exceptions\VException;

/**
 * Класс предназначен для перехвата исключений,
 * возникших при работе БД
 * @since 12.05.2012
 * @version 0.1
 */
class VDatabaseException extends VException {
	public function __construct($sErrorText='', $nErrorCode=0){
		parent::__construct($sErrorText, $nErrorCode);
	}
}
