<?php
namespace request;
use core\types\VString;
use request\exceptions\VRequest as Error;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 17.01.14
 */
class VUser {
	private $sName;
	private $sEmail;
	private $sPhone;

	/**
		 * Инициализация данных класса
		 * @param $sName
		 * @param $sEmail
		 * @param $sPhone
		 * @param int $nId
		 * @throws exceptions\VRequest
		 */
	public function __construct($sName,$sEmail,$sPhone,$nId=0){
				$this->sName=$sName;
				$this->sEmail=$sEmail;
				$this->sPhone=$sPhone;
				$this->nId=intval($nId);
		
				if(empty($sName))
						throw new Error('invalid name');

		if(!VString::isEmail($sEmail))
						throw new Error('invalid email');

		if(!VString::isPhone($sPhone))
						throw new Error('invalid phone');
	}

	public function getName(){
				return $this->sName;
	}

	public function getEmail(){
				return $this->sEmail;
	}

	public function getPhone(){
				return $this->sPhone;
	}

	public function getId(){
				return $this->nId;
	}
}