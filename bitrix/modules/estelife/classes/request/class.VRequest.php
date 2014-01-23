<?php
namespace request;
use core\database\exceptions as dbErrors;
use core\database\VDatabase;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 17.01.14
 */
class VRequest {
	private $obUser;
	private $obCompany;

	/**
	 * Осуществляет инициализацию объекта для генерации заявки
	 * @param VUser $obUser
	 * @param VCompany $obCompany
	 */
	public function __construct(VUser $obUser,VCompany $obCompany){
		$this->obUser=$obUser;
		$this->obCompany=$obCompany;
	}

	/**
	 * Создает заявку
	 * @return bool
	 */
	public function create(){
		$bResult=false;

		try{
			$obQuery=VDatabase::driver()->createQuery();
			$obQuery->builder()
				->from('estelife_requests')
				->value('company_id',$this->obCompany->getId())
				->value('company_name',$this->obCompany->getName())
				->value('company_city',$this->obCompany->getCity())
				->value('company_type',$this->obCompany->getType())
				->value('user_name',$this->obUser->getName())
				->value('user_email',$this->obUser->getEmail())
				->value('user_phone',$this->obUser->getPhone())
				->value('user_id',$this->obUser->getId());
			$obResult=$obQuery->insert();
			$bResult=($obResult->insertId()>0);
		}catch(dbErrors\VDatabaseException $e){
		}catch(dbErrors\VQueryException $e){}

		return $bResult;
	}

	/**
	 * Ищет заявку по данным компании
	 * @return array
	 */
	public function findByCompany(){
		$obQuery=VDatabase::driver()->createQuery();
		$obFilter=$obQuery->builder()
			->from('estelife_requests')
			->filter()
			->_eq('company_type',$this->obCompany->getType())
			->_eq('company_city',$this->obCompany->getCity());
		$nCompanyId=$this->obCompany->getId();

		if($nCompanyId>0)
			$obFilter->_eq('company_id',$nCompanyId);
		else
			$obFilter->_eq('company_name',$this->obCompany->getName());

		$obResult=$obQuery->select();
		return ($obResult->count()) ?
			$obResult->assoc() :
			array();
	}

	/**
	 * Ищет заявку по информации о пользователе, который её создает
	 * @return array
	 */
	public function findByUser(){
		$obQuery=VDatabase::driver()->createQuery();
		$obFilter=$obQuery->builder()
			->from('estelife_requests')
			->filter();
		$nUserId=$this->obUser->getId();

		if($nUserId>0)
			$obFilter->_eq('user_id',$nUserId);
		else{
			$obFilter->_or()
				->_eq('user_email',$this->obUser->getEmail());
			$obFilter->_or()
				->_eq('user_phone',$this->obUser->getPhone());
		}

		$obResult=$obQuery->select();
		return ($obResult->count()) ?
			$obResult->assoc() :
			array();
	}
}