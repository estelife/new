<?php
namespace core\validate;
use core\exceptions\VFormException;

/**
 * Описание класса не задано. Обратитесь с вопросом к разработчику.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 30.05.13
 */
class VValidate {

	/**
	 * Проверка массива параметров на валидность
	 * @param array $arParams
	 * @param array $arRules
	 * @param array $arFunc
	 * @return mixed
	 */
	public static function validateArray(array &$arParams, array $arRules=null, array $arFunc=null){
		$obError = new VFormException();
		foreach ($arParams as $key=>&$val){
			if (!empty($arRules)){
				if (isset($arRules[$key])){
					$arItemRules = (self::isArray($arRules[$key])) ? $arRules[$key] : explode(',',$arRules[$key]);
					if (!empty($arItemRules)){
						foreach ($arItemRules as $v){
							$v = trim($v);
							if (preg_match('/^(.*)\\[([><=]{1,2})(.*)\\]$/', $v, $matches)){
								$matches[1] = trim($matches[1]);
								switch ($matches[2]){
									case '>':
										if (call_user_func($matches[1], $val) <= $matches[3])
											$obError->setFieldError($v,$key);
									break;
									case '<':
										if (call_user_func($matches[1], $val) >= $matches[3])
											$obError->setFieldError($v,$key);
										break;
									case '<=':
										if ((call_user_func($matches[1], $val) > $matches[3]))
											$obError->setFieldError($v,$key);
										break;
									case '>=':
										if (call_user_func($matches[1], $val) < $matches[3])
											$obError->setFieldError($v,$key);
										break;
									default:
										if (call_user_func($matches[1], $val) != $matches[3])
											$obError->setFieldError($v,$key);
										break;
								}
							}else{
								if (!method_exists(__CLASS__, $v)){
									$bRes = call_user_func($v, $val);
									if ($bRes == false)
										$obError->setFieldError($v,$key);
								}else{
									$obRes = self::$v($val);
									if ($obRes == false)
										$obError->setFieldError($v,$key);
								}
							}
						}
					}
				}
			}

			if (!empty($arFunc)){
				if (isset($arFunc[$key])){
					$arItemFunc=(self::isArray($arFunc[$key])) ? $arFunc[$key] : explode(',',$arFunc[$key]);
					if (!empty($arItemFunc)){
						foreach ($arItemFunc as $v){
							$v = trim($v);
							$val = call_user_func($v, $val);
						}
					}
				}
			}
		}
		$obError->raise();
	}

	/**
	 * Проверка параметра на валидность
	 * @param $sParam
	 * @param array $arRules
	 * @return mixed
	 */

	public static function validateParam($sParam, array $arRules){
		try{
			$obError=new VFormException();
			foreach ($arRules as $val){
				if (!self::$val($sParam))
					$obError->setFieldError($val,$val);
			}
			$obError->raise();
		}catch(VFormException $e){
			return $e->getFieldErrors();
		}
	}

	/**
	 * Проверяет, является ли переменная объектом
	 * @param $obObject
	 * @return bool
	 */

	public static function isObject($obObject){
		return is_object($obObject);
	}

	/**
	 * Проверяет, является ли переменная булевой
	 * @param $bBool
	 * @return bool
	 */
	public static function isBool($bBool){
		return is_bool($bBool);
	}

	/**
	 * Проверяет, является ли переменная массивом
	 * @param $arArray
	 * @return bool
	 */
	public static function isArray($arArray){
		return is_array($arArray);
	}

	/**
	 * Проверяет, является ли переменная строкой
	 * @param $sString
	 * @return bool
	 */
	public static function isString($sString){
		return is_string($sString);
	}

	/**
	 * Проверяет, является ли переменная числом
	 * @param $nNumeric
	 * @return bool
	 */
	public static function isNumeric($nNumeric){
		return is_numeric($nNumeric);
	}

	/**
	 * Осуществляет проверку страки на соответствие идентификатору
	 * @param $sTranslit
	 * @return int
	 */
	public static function isIdent($sTranslit){
		return preg_match('/^[a-z0-9_-]+$/',$sTranslit);
	}

	/**
	 * Проверяет корректность email
	 * @param string $sEmail
	 * @return boolean
	 */
	public static function isEmail($sEmail){
		return preg_match('/^[a-z\d](?:[\w\.-]*[a-z\d])*@(?:[a-z\d](?:[a-z\d-]*[a-z\d])*\.)+[a-z]{2,4}$/i',$sEmail);
	}

	/**
	 * Проверяет корректность пароля
	 * @param string $sPassword
	 * @return boolean
	 */
	public static function isPassword($sPassword){
		return preg_match('/^[a-z0-9]{6,255}$/i',$sPassword);
	}

	/**
	 * Проверяет валидность номера телефона
	 * @param string $sPhone
	 * @return int
	 */
	public static function isPhone($sPhone){
		$sPhone=preg_replace('#([^\d]*)#','',$sPhone);
		return !(empty($sPhone) || strlen($sPhone)<10);
	}

	/**
	 * Проверяет урл
	 * @param $sUrl
	 * @return int
	 */
	public static function isUrl($sUrl){
		return preg_match('#^((https?|ftp)\:\/\/){1}([a-z_\-\.\/]+)(\?[0-9a-z=\/_\-&\%]+)$#i',$sUrl);
	}


	/**
	 * Проверяет на пустоту
	 * @param $mValue
	 * @return bool
	 */
	public static function notEmpty($mValue){
		return !empty($mValue);
	}

	/**
	 * Проверяет на isset
	 * @param $mValue
	 * @return bool
	 */
	public static function isIsset($mValue){
		return isset($mValue);
	}
}