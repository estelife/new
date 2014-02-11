<?php
namespace subscribe\exceptions;
use core\exceptions\VException;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 10.02.14
 */
class VFactoryEx extends VException {
	public function __construct($sErrorText,$nErrorCode=0){
		parent::__construct($sErrorText,$nErrorCode);
	}
}