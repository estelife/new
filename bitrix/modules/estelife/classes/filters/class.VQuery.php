<?php
namespace filters;

/**
 *
 * @author Maxim Shlemarev <shlemarev@gmail.com>
 * @since 30.01.14
 */
class VQuery implements VFilter, VChangeable {
	private $type;

	public function __construct($sType){
		$this->type = $sType;
		$this->params = $_GET;
	}

	public function setParam($nKey,$sParam){
		$this->params[$nKey] = $sParam;
	}

	public function getParam($nKey){
		return $this->params[$nKey];
	}

	public function unsetParam($nKey){
		unset($this->params[$nKey]);
	}

	public  function getParams(){
		return $this->params;
	}

	public function clearParams(){
		$this->params = array();
	}
}