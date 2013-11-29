<?php
namespace core\exceptions;
use Exception;

/** 
 * Обертка для стандартного класса Exception.
 * Добавляет функционал работы с языком
 * @since 12.05.2012
 * @version 0.1
 */
class VException extends Exception {

	public function __construct($sErrortext='', $nErrorcode=0){
		parent::__construct($sErrortext, $nErrorcode);
	}
	
}
