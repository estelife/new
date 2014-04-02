<?php
namespace select;
use field\VField;

/**
 *
 * @author Panait Vitaly <panait.v@yandex.ru>
 * @since 06.02.14
 */

class VSelect extends VField{

	public function __toString(){
		$sField='';
		$sAttr='';
		$sSelected='';

		if (!empty($this->arAttributes)){
			foreach ($this->arAttributes as $key=>$val){
				$sAttr.=' '.$key.'="'.$val.'"';
			}
		}

		if (!empty($this->sLabel))
			$sField.='<label for="'.$this->sId.'">';

		$sField.='<select name="'.$this->sName.'" id="'.$this->sId.'"'.$sAttr.' />';

		if (!empty($this->mValue)){
			foreach ($this->mValue as $key=>$val){
				if ($val['selected']==true)
					$sSelected='selected="selected"';
				$sField.='<option value="'.$key.'" '.$sSelected.'>'.$val['value'].'</option>';
			}
		}

		$sField.='</select>';

		if (!empty($this->sLabel))
			$sField.=$this->sLabel.'</label>';

		return $sField;
	}

	public function setValue($mKey=false, $sValue, $bSelected=false){
		if (!$mKey)
			$this->mValue[]=array(
				'value'=>$sValue,
				'selected'=>$bSelected
			);
		else
			$this->mValue[$mKey]=array(
				'value'=>$sValue,
				'selected'=>$bSelected
			);
	}

	public function setArrayValue($arValues){
		$this->mValue=$arValues;
	}
}