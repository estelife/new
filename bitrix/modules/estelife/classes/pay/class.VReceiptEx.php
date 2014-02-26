<?php
namespace pay;
use core\exceptions\VException;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 18.02.14
 */
class VReceiptEx extends VException {
	public function __construct($sErrorText,$nErrorCode=0){
		parent::__construct($sErrorText,$nErrorCode);
	}
}