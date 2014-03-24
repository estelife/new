<?php
namespace core\utils\forms;

/**
 * Класс для построения текстарии
 * @file class.VTextarea.php
 * @version 0.1
 */
class VTextarea extends VField {

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

		$sField .= '<textarea type="text" name="'.$this->sName.'" id="'.$this->sId.'"'.$sAttributes.'>'.$this->mValue.'</textarea>';

		if (!empty($this->sLabel))
			$sField .= $this->sLabel.'</label>';

		return $sField;
	}
}