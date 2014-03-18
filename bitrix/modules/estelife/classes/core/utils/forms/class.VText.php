<?php
namespace text;
use field\VField;

/**
 *
 * @author Panait Vitaly <panait.v@yandex.ru>
 * @since 06.02.14
 */

class VText extends VField{
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

		$sField.='<input type="text" name="'.$this->sName.'" value="'.$this->mValue.'" id="'.$this->sId.'"'.$sAttr.' />';

		if (!empty($this->sLabel))
			$sField.=$this->sLabel.'</label>';

		return $sField;
	}
}