<?php
namespace pay;
use core\database\VDatabase;
use geo\VGeo;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 18.02.14
 */
final class VSecure {
	private $sSalt;

	public function __construct(){
		$this->sSalt = 'receipt-user-hash';
	}

	public function checkProtectedKey(){
		$sCookie = isset($_COOKIE['rec-ush']) ? $_COOKIE['rec-ush'] : '';
		$sAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$sIp = VGeo::getInstance()->getIp();

		$sKey = crypt($sAgent.$sIp,$this->sSalt);
		setcookie('rec-ush','',time()-86400);
		return ($sKey != $sCookie);
	}

	public function getUserBySecret(){
		$sUserHash = isset($_COOKIE['rec-ust']) ? $_COOKIE['rec_ust'] : '';

		if(empty($sUserHash))
			return array();

		$obQuery = VDatabase::driver()
			->createQuery();
		$obQuery->builder()
			->from('b_user')
			->filter()
			->_eq(
				$obQuery->builder()->_md5('ID','DATE_REGISTER','EMAIL','NAME',$this->sSalt),
				$sUserHash
			);

		setcookie('rec-ust','',time()-86400);
		return $obQuery
			->select()
			->assoc();
	}

	public function createUserSecrete($nUserId,$sDateRegister,$sEmail,$sName){
		$sUserSecrete = md5($nUserId.$sDateRegister.$sEmail.$sName.$this->sSalt);
		setcookie('rec-ust', $sUserSecrete, 3600, '/', 'estelife.ru');
	}

	public function createProtectedKey(){
		$sAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$sIp = VGeo::getInstance()->getIp();
		$sProtectedKey = crypt($sAgent.$sIp, $this->sSalt);

		setcookie('rec-ush', $sProtectedKey, 3600, '/', 'estelife.ru');
	}
}