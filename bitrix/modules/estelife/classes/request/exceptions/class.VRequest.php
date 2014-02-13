<?php
namespace request\exceptions;
use core\exceptions\VException;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 17.01.14
 */
class VRequest extends VException {
	public function __construct($sErrorText='', $nErrorCode=0){
				parent::__construct($sErrorText, $nErrorCode);
			}
}