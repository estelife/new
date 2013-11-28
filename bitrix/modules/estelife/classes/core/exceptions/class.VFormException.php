<?php
namespace core\exceptions;

/**
 * Класс предназначен для перехвата исключений,
 * возникших при работе Формами
 * @since 12.05.2012
 * @vesion 0.1
 */
class VFormException extends VException {
	private $arFieldErrors;
	private $arFormErrors;
	
	function __construct(){
		parent::__construct('Ошибка заполнения данных');
		$this->arFieldErrors=array();
		$this->arFormErrors=array();
	}
	
	public function setFieldError($sErrorText,$sErrorField){
		$this->arFieldErrors[$sErrorField]=$sErrorText;
	}
	
	public function getFieldErrors(){
		return $this->arFieldErrors;
	}
	
	public function setFormError($sErrorText){
		$this->arFormErrors[]=$sErrorText;
	}
	
	public function getFormErrors(){
		return $this->arFormErrors;
	}
	
	public function hasErrors(){
		return (!empty($this->arFormErrors) || !empty($this->arFieldErrors));
	}
	
	public function raise(){
		if($this->hasErrors())
			throw $this;
	}
}
