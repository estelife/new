<?php
namespace companies;
use core\database\collections\VCollection;
use core\database\collections\VMeta;
use core\database\VDatabase;
use core\types\VArray;
use core\types\VString;

class VCompanies {

	protected $obCompanies;

	public function __construct(){
		$this->obCompanies = VDatabase::driver();
	}

	//Добавляем контакты
	public function addContacts($idCompany, array $obArray, $tableName){
		if (empty($idCompany))
			return false;

		$arContactTypes = array('web', 'phone', 'fax', 'email');
		foreach ($arContactTypes as $val){
			$arContacts=$obArray[$val];

			//удаление привязки
			$obQuery =  $this->obCompanies->createQuery();
			$obQuery->builder()->from($tableName)
				->filter()
				->_eq('company_id', $idCompany)
				->_eq('type', $val);
			$obQuery->delete();

			foreach($arContacts as $sContact){
				if(empty($sContact))
					continue;

				if ($val == 'phone' || $val == 'fax'){
					if(!VString::isPhone($sContact))
						continue;
				}elseif ($val == 'email'){
					if(!VString::isEmail($sContact))
						continue;
				}

				$obQuery = $this->obCompanies->createQuery();
				$obQuery->builder()->from($tableName)
					->value('type', $val)
					->value('value', $sContact)
					->value('company_id', $idCompany);

				$obQuery->insert()->insertId();
			}
		}
	}

	//Добавляем Гео
	public function addGeo($idCompany, array $obArray, $tableName){
		if (empty($idCompany))
			return false;

		if (empty($obArray['city_name']))
			$obArray['city_id'] = 0;

		$obQuery = $this->obCompanies->createQuery();
		$obQuery ->builder()->from($tableName)
			->value('country_id', intval($obArray['country_id']))
			->value('city_id', intval($obArray['city_id']))
			->value('metro_id', intval($obArray['metro_id']))
			->value('address', htmlentities($obArray['address'],ENT_QUOTES,'utf-8'))
			->value('latitude', doubleval($obArray['latitude']))
			->value('longitude', doubleval($obArray['longitude']))
			->value('company_id', $idCompany);
		return $obQuery->insert()->insertId();
	}

}