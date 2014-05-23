<?php
namespace core\exceptions;

class VHttpEx extends VException {
	public function __construct($sErrorText='', $nErrorCode=0){
		parent::__construct($sErrorText, $nErrorCode);
	}
}