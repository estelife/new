<?php
namespace form;
use select\VSelect;
use submit\VSubmit;
use text\VText;
use textarea\VTextarea;

/**
 *
 * @author Panait Vitaly <panait.v@yandex.ru>
 * @since 06.02.14
 */

class VForm{
	const POST=1;
	const GET=2;
	protected $nMethod;
	protected $sAction;
	protected $sName;
	/**
	 * @var VField[]
	 */
	protected $arFields;

	final public function __construct($sName, $sAction=false, $nMethod=VForm::POST){
		$this->nMethod=intval($nMethod);
		$this->sAction=trim(strip_tags($sAction));
		$this->sName=trim(strip_tags($sName));
	}

	public function createToken($arFields, $sAction, $sName){
		$sToken=$sAction.$sName;
		if (!empty($arFields)){
			foreach ($arFields as $val){
				$sToken.=$val;
			}
		}
		$sSalt=substr($sToken,0,floor(strlen($sToken)/2));
		$sToken=crypt($sToken, $sSalt);

		return $sToken;
	}

	public function setValues($arValues){
		if (empty($arValues))
			return null;

		foreach ($arValues as $key=>$val){
			if (isset($this->arFields[$key])){
				$obField=$this->arFields[$key];
				$obField->setValue($val);
			}
		}
	}

	public function getValues(){
		if (empty($this->arFields))
			return false;

		$arValues=array();
		foreach ($this->arFields as $key=>$val){
			$arValues[$key]=$val->getValue();
		}

		return $arValues;
	}

	public function createTextField($sName, $sId){
		$obTextField=new VText($sName, $sId);
		$this->arFields[$sName]=$obTextField;

		return $obTextField;
	}

	public function createTextArea($sName, $sId){
		$obTextArea=new VTextArea($sName, $sId);
		$this->arFields[$sName]=$obTextArea;

		return $obTextArea;
	}

	public function createSubmit($sName, $sId){
		$obSubmit=new VSubmit($sName, $sId);
		$this->arFields[$sName]=$obSubmit;

		return $obSubmit;
	}

	public function createSelectField($sName, $sId){
		$obSelect=new VSelect($sName, $sId);
		$this->arFields[$sName]=$obSelect;

		return $obSelect;
	}


}