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

	/**
	 * Регистрация ошибки
	 * @param $sTitle
	 * @param $sDescription
	 * @return bool
	 */
	public static function registerError($sTitle, $sDescription){
		if (empty($sTitle))
			return false;

		if (empty($sDescription))
			return false;

		$_SESSION['noticeError'][]=array(
			'title'=>$sTitle,
			'description'=>$sDescription
		);
	}

	/**
	 * Регситрация уведомления
	 * @param $sTitle
	 * @param $sDescription
	 * @return bool
	 */
	public static function registerSuccess($sTitle, $sDescription){
		if (empty($sTitle))
			return false;

		if (empty($sDescription))
			return false;

		$_SESSION['noticeSuccess'][]=array(
			'title'=>$sTitle,
			'description'=>$sDescription
		);
	}

	/**
	 * Получение ошибок
	 * @return mixed
	 */
	public static function getError(){
		return $_SESSION['noticeError'];
	}

	/**
	 * Получение уведомлений
	 * @return mixed
	 */
	public static function getSuccess(){
		return $_SESSION['noticeSuccess'];
	}

	/**
	 * Удаление ошибок
	 */
	public static function cleanError(){
		unset($_SESSION['noticeError']);
	}

	/**
	 * Удаление уведомлений
	 */
	public static function cleanSuccess(){
		unset($_SESSION['noticeSuccess']);
	}

}