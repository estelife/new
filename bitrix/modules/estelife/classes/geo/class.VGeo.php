<?php
namespace geo;
use core\database\mysql\VFilter;
use core\database\VDatabase;
use core\exceptions\VException;

final class VGeo{
	protected static $instance;
	protected $city;
	private $option;

	private function __clone(){}
	private function __construct(){
		$this->option = array();
		$this->option['charset'] = 'utf-8';

		$this->setGeo();
	}

	public static function getInstance() {
		if ( !isset(self::$instance) ){
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function getGeo(){
		return $this->city;
	}

	public function setGeo($arParam=false){

		$obCity = VDatabase::driver();
		$obQuery = $obCity->createQuery();
		$obQuery->builder()->from('iblock_element');

		if (empty($arParam)){
			$geo = new Geo($this->option);
			$arCity = $geo->get_value();

			if (empty($this->city)){
				$arCity['city'] = 'Москва';
			}
			$obQuery->builder()->filter()
				->_eq('IBLOCK_ID', 16)
				->_like('NAME',$arCity['city'],VFilter::LIKE_BEFORE|VFilter::LIKE_AFTER);
		}else if (is_numeric($arParam)){
			$obQuery->builder()->filter()
				->_eq('IBLOCK_ID', 16)
				->_eq('ID',$arParam);
		}else{
			$obQuery->builder()->filter()
				->_eq('IBLOCK_ID', 16)
				->_like('NAME',$arParam,VFilter::LIKE_BEFORE|VFilter::LIKE_AFTER);
		}
		$arCities = $obQuery->select()->all();

		if (!empty($arCities)){
			$this->city = reset($arCities);
			setcookie('estelife_city', $this->city['ID'], time() + 12*60*60*24*30, '/');
		}else{
			throw new VException('city not found');
		}

		return $this->city;
	}
}