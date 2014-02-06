<?php
namespace notice;
use core\database\mysql\VFilter;
use core\database\VDatabase;
use core\exceptions\VException;

/**
 *
 * @author Panait Vitaly <panait.v@yandex.ru>
 * @since 06.02.14
 */

final class VNotice{

	//Регистрация ошибки
	public static function registerError($sText){
		if (empty($sText))
			return false;

		$_SESSION['noticeError'][]=$sText;
	}

	//Регистрация уведомления
	public static function registerSuccess($sText){
		if (empty($sText))
			return false;

		$_SESSION['noticeSuccess'][]=$sText;
	}

	//Получение ошибок
	public static function getError(){
		return $_SESSION['noticeError'];
	}

	//Получение уведомлений
	public static function getSuccess(){
		return $_SESSION['noticeSuccess'];
	}

	//Удаление ошибок
	public static function cleanError(){
		unset($_SESSION['noticeError']);
	}

	//Удаление уведомлений
	public static function cleanSuccess(){
		unset($_SESSION['noticeSuccess']);
	}

}