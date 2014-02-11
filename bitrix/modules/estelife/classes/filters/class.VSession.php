<?php
namespace filters;

/**
 *
 * @author Maxim Shlemarev <shlemarev@gmail.com>
 * @since 30.01.14
 */

class VSession implements VBase{

	private $nType;

	public function __construct($nType){
		$this->type = $nType;

	/*	if(empty($nType))
			throw new Error('invalid type');*/
	}

	public function setParam($nKey,$sParam){
		$_SESSION['filter'][$this->type][$nKey] = $sParam;
	}

	public function getParam($nKey){
		return $_SESSION['filter'][$this->type][$nKey];
	}

	public function unsetParam($nKey){
		unset($_SESSION['filter'][$this->type][$nKey]);
	}

	public  function getAllParams(){
		return $_SESSION['filter'][$this->type];
	}
}