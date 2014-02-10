<?php
namespace subscribe\errors;
use core\exceptions\VException;

/**
 * Описание класса не задано. Обратитесь с вопросом на почту разработчика.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 07.02.14
 */
class VOwnerEx extends VException {
	public function __construct($sErrorText,$nErrorCode=0){
		parent::__construct($sErrorText,$nErrorCode);
	}
}