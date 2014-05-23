<?php
namespace core\utils\forms;
use core\validate\VValidate;

/**
 * Класс для работы с формами.
 * @file class.VForm.php
 * @version 0.1
 */
class VForm {

	const POST = 1;
	const GET = 2;
	protected $arFields;
	protected $sAction;
	protected $sName;
	protected $sId;
	protected $nMethod;

	public function __construct($sName, $sAction, $nMethod=VForm::POST){
		if (empty($sName) || empty($sAction))
			return null;

		$this->sName = trim(addslashes(htmlspecialchars($sName, ENT_QUOTES, 'utf-8')));
		$this->sAction = trim(addslashes(htmlspecialchars($sAction, ENT_QUOTES, 'utf-8')));
		$this->nMethod = intval($nMethod);
		$this->sId = md5($sName);
	}

	/**
	 * Метод для получения полей
	 */
	public function getFields(){
		return $this->arFields;
	}

	/**
	 * Метод для получения названия формы
	 */
	public function getName(){
		return $this->sName;
	}

	/**
	 * Метод для получения id формы
	 */
	public function getId(){
		return $this->sId;
	}

	/**
	 * Метод для получения метода формы
	 */
	public function getMethod(){
		if ($this->nMethod==1)
			return 'post';
		else
			return 'get';
	}

	/**
	 * Метод для получения action формы
	 */
	public function getAction(){
		return $this->sAction;
	}

	/**
	 * Метод установки значений для созданных полей
	 * @param array $arValues
	 * @param array $arRules
	 * @param array $arFunc
	 */
	public function setValues(array $arValues, array $arRules=null, array $arFunc=null){
		if (empty($arValues) && !is_array($arValues))
			assert('Values is empty or not array');

		if (!empty($arRules) && !empty($arFunc))
			VValidate::validateArray($arValues, $arRules, $arFunc);

		foreach ($arValues as $key=>$val){
			if (isset($this->arFields[$key]))
				$this->arFields[$key]->setValue($val);
		}
	}

	/**
	 * Метод получения значений для созданных полей
	 * @return array
	 */
	public function getValues(){
		$arValues=array();
		foreach ($this->arFields as $key=>$val){
				$arValues[$key] = $val->getValue();
		}

		return $arValues;
	}

	/**
	 * Метод создания текстового поля
	 * @param $sName
	 * @param $sId
	 * @return VText
	 */
	public function createTextField($sName, $sId=false){
		if (empty($sName))
			assert('Field name is empty');

		if (!$sId)
			$sId = $this->createFieldId($sName);

		$obField = new VText($sName, $sId);
		$this->arFields[$sName] = $obField;

		return $obField;
	}

	/**
	 * Метод создания скрытого поля
	 * @param $sName
	 * @param $sId
	 * @return VHidden
	 */
	public function createHiddenField($sName, $sId=false){
		if (empty($sName))
			assert('Field name is empty');

		if (!$sId)
			$sId = $this->createFieldId($sName);

		$obField = new VHidden($sName, $sId);
		$this->arFields[$sName] = $obField;

		return $obField;
	}

	/**
	 * Метод создания поля textarea
	 * @param $sName
	 * @param bool $sId
	 * @return VTextarea
	 */

	public function createTextareaField($sName, $sId=false){
		if (empty($sName))
			assert('Field name is empty');

		if (!$sId)
			$sId = $this->createFieldId($sName);

		$obField = new VTextarea($sName, $sId);
		$this->arFields[$sName] = $obField;

		return $obField;
	}

	/**
	 * Метод создания поля submit
	 * @param $sName
	 * @param $sId
	 * @return VSubmit
	 */
	public function createSubmitField($sName, $sId=false){
		if (empty($sName))
			assert('Field name is empty');

		if (!$sId)
			$sId = $this->createFieldId($sName);

		$obField = new VSubmit($sName, $sId);
		$this->arFields[$sName] = $obField;

		return $obField;
	}

	/**
	 * Метод создания token
	 * @param $sName
	 * @param $sId
	 * @return VToken
	 */
	public function createTokenField($sName, $sId=false){
		if (empty($sName))
			assert('Field name is empty');

		if (!$sId)
			$sId = $this->createFieldId($sName);

		$obField = new VToken($sName, $sId);
		$this->arFields[$sName] = $obField;

		return $obField;
	}

	/**
	 * Создание идентификатора для поля
	 * @param $sName
	 * @return string
	 */
	private function createFieldId($sName){
		return md5($sName);
	}

	/**
	 * Создание токена для формы
	 * @param $sName
	 * @param $nMethod
	 * @param $sAction
	 * @return string
	 */
	static public function createToken($sName, $nMethod, $sAction){
		return md5($sName.'saltname'.$nMethod.'saltmethod'.$sAction.'saltaction');
	}

	/**
	 * Проверка токена для формы
	 * @param $sToken
	 * @return bool
	 */

	public function checkToken($sToken){
		$sTempToken = $this->createToken($this->sName, $this->nMethod, $this->sAction);

		if ($sTempToken == $sToken)
			return true;
		else
			return false;
	}

	public function getScriptForToken($bFlag=false){
		$sScript='
			<script type="text/javascript">
				$.get("/api/estelife_ajax.php",{
					"action":"create_form_token",
					"params":{
						"name": "'.$this->sName.'" ,
						"action": "'.$this->sAction.'",
						"method": "'.$this->nMethod.'",
					}
				},function(r){
					if (r)
						$("#'.$this->sId.'").append("<input type=\'hidden\' name=\'form_token\' value=\'"+r+"\' />");
				},
				"json");
			</script>
		';
		if ($bFlag==true){
			return $sScript;
		}else{
			GLOBAL $APPLICATION;
			$APPLICATION->AddHeadString($sScript, true);
		}
	}
}