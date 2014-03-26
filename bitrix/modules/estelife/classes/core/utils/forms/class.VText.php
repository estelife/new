<?php
namespace core\utils\forms;

/**
 * Класс для построения текстового поля
 * @file class.VText.php
 * @version 0.1
 */
class VText extends VField {

	public function __toString(){
		$sField="";
		$sAttributes="";

		if (!empty($this->arAttributes)){
			foreach ($this->arAttributes as $key=>$val){
				$sAttributes .= ' '.$key.'="'.$val.'"';
			}
		}

		if (!empty($this->sLabel))
			$sField .= '<label for="'.$this->sId.'">';

		$sField .= '<input type="text" name="'.$this->sName.'" value="'.$this->mValue.'" id="'.$this->sId.'"'.$sAttributes.' />';

		if (!empty($this->sLabel))
			$sField .= $this->sLabel.'</label>';

		return $sField;
	}
}