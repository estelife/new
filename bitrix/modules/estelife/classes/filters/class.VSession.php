<?php
namespace filters;

/**
 *
 * @author Maxim Shlemarev <shlemarev@gmail.com>
 * @since 30.01.14
 */

class VSession implements VFilter,VChangeable {
	private $type;

	public function __construct($nType){
		$this->type = $nType;
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

	public  function getParams(){
		return $_SESSION['filter'][$this->type];
	}

	public function clearParams(){
		unset($_SESSION['filter'][$this->type]);
	}
}