<?php
namespace subscribe;
use core\exceptions\VException;
/**
 * Меня зовут Максим и я реально поленился оставить описание для этого файла
 * @author Maxim Shlemarev <shlemarev@gmail.com>
 * @since 30.01.14
 */


class VUserNotFound extends VException {
	public function __construct($sErrorText='', $nErrorCode=0){
		parent::__construct($sErrorText, $nErrorCode);
	}
}