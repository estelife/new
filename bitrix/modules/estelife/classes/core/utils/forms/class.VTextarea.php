<?php
namespace textarea;
use field\VField;

/**
 *
 * @author Panait Vitaly <panait.v@yandex.ru>
 * @since 06.02.14
 */

class VTextarea extends VField{
	public function __toString(){
		$sField='';
		$sAttr='';

		if (!empty($this->arAttributes)){
			foreach ($this->arAttributes as $key=>$val){
				$sAttr.=' '.$key.'="'.$val.'"';
			}
		}

		if (!empty($this->sLabel))
			$sField.='<label for="'.$this->sId.'">';

		$sField.='<textarea name="'.$this->sName.'" id="'.$this->sId.'"'.$sAttr.' />'.$this->mValue.'</textarea>';

		if (!empty($this->sLabel))
			$sField.=$this->sLabel.'</label>';

		return $sField;
	}
}