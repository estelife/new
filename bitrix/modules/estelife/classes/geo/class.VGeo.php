<?php
namespace geo;
use core\database\mysql\VFilter;
use core\database\VDatabase;
use core\exceptions\VException;

final class VGeo{
	protected static $instance;
	protected $city;
	protected $ip;

	private function __clone(){}
	private function __construct(){
		if(!isset($options['ip']) OR !$this->isValidIp($options['ip']))
			$this->ip = $this->getIp();
		elseif($this->isValidIp($options['ip']))
			$this->ip = $options['ip'];
	}

	public static function getInstance() {
		if ( !isset(self::$instance) ){
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function getGeo(){
		if (!$this->city)
			$this->setGeo();

		return $this->city;
	}

	public function setGeo($mCity=false){
		$obCity=VDatabase::driver();
		$obQuery=$obCity->createQuery();
		$obQuery->builder()
			->field('ie.ID')
			->field('ie.NAME')
			->field('ie.CODE')
			->field('ieps.PROPERTY_44', 'COUNTRY_ID')
			->field('iec.NAME', 'COUNTRY_NAME')
			->from('iblock_element', 'ie');

		$obJoin=$obQuery->builder()->join();
		$obJoin->_left()
			->_from('ie','ID')
			->_to('iblock_element_prop_s16','IBLOCK_ELEMENT_ID','ieps');
		$obJoin->_left()
			->_from('ieps','PROPERTY_44')
			->_to('iblock_element','ID','iec');
		$bSetCookie=false;

		if (empty($mCity) && !isset($_COOKIE['estelife_city'])){
			$arCity=$this->geoBaseData(false);
			$this->city=$arCity['city'];

			if(empty($this->city)){
				$arCity['city'] = 'Москва';
			}

			$obQuery->builder()->filter()
				->_eq('ie.IBLOCK_ID', 16)
				->_like('ie.NAME',$arCity['city'],VFilter::LIKE_BEFORE|VFilter::LIKE_AFTER);

			$bSetCookie=true;
		}else if(!empty($mCity)){
			if(is_numeric($mCity)){
				$obQuery->builder()->filter()
					->_eq('ie.IBLOCK_ID', 16)
					->_eq('ie.ID',intval($mCity));
			}else{
				$obQuery->builder()->filter()
					->_eq('ie.IBLOCK_ID', 16)
					->_like(
						'ie.NAME',
						trim(htmlspecialchars(strip_tags($mCity),ENT_QUOTES,'utf-8')),
						VFilter::LIKE_BEFORE|VFilter::LIKE_AFTER
					);
			}

			$bSetCookie=true;
		}else if(isset($_COOKIE['estelife_city'])){
			$obQuery->builder()->filter()
				->_eq('ie.IBLOCK_ID', 16)
				->_eq('ie.ID',intval($_COOKIE['estelife_city']));
		}else{
			$obQuery->builder()->filter()
				->_eq('ie.IBLOCK_ID', 16)
				->_eq('ie.ID',359);
		}

		$arCities=$obQuery->select()->all();

		if (!empty($arCities)){
			$this->city = reset($arCities);

			if($bSetCookie){
				setcookie('estelife_city', $this->city['ID'], time() + 12*60*60*24*30, '/');
				setcookie('estelife_country', $this->city['COUNTRY_ID'], time() + 12*60*60*24*30, '/');
			}
		}

		return $this->city;
	}

	/**
	 * функция получает данные по ip.
	 * @param bool $sParam
	 * @return array - возвращает массив с данными
	 */
	function geoBaseData($sParam=false){
		// получаем данные по ip
		$link = 'ipgeobase.ru:7020/geo/?ip='.$this->ip;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $link);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 3);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
		$string = curl_exec($ch);

		$string=mb_convert_encoding($string,'utf-8','windows-1251');
		$data=$this->parseString($string);

		if(is_string($sParam))
			return (isset($data[$sParam])) ?
				$data[$sParam] : null;
		else
			return $data;
	}

	/**
	 * функция парсит полученные в XML данные в случае, если на сервере не установлено расширение Simplexml
	 * @return array - возвращает массив с данными
	 */

	function parseString($string)
	{
		$pa['inetnum'] = '#<inetnum>(.*)</inetnum>#is';
		$pa['country'] = '#<country>(.*)</country>#is';
		$pa['city'] = '#<city>(.*)</city>#is';
		$pa['region'] = '#<region>(.*)</region>#is';
		$pa['district'] = '#<district>(.*)</district>#is';
		$pa['lat'] = '#<lat>(.*)</lat>#is';
		$pa['lng'] = '#<lng>(.*)</lng>#is';
		$data = array();
		foreach($pa as $key => $pattern)
		{
			if(preg_match($pattern, $string, $out))
			{
				$data[$key] = trim($out[1]);
			}
		}
		return $data;
	}

	/**
	 * функция определяет ip адрес по глобальному массиву $_SERVER
	 * ip адреса проверяются начиная с приоритетного, для определения возможного использования прокси
	 * @return ip-адрес
	 */
	function getIp()
	{
		$ip=false;
		$ipa=array();

		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ipa[] = trim(strtok($_SERVER['HTTP_X_FORWARDED_FOR'], ','));

		if (isset($_SERVER['HTTP_CLIENT_IP']))
			$ipa[] = $_SERVER['HTTP_CLIENT_IP'];

		if (isset($_SERVER['REMOTE_ADDR']))
			$ipa[] = $_SERVER['REMOTE_ADDR'];

		if (isset($_SERVER['HTTP_X_REAL_IP']))
			$ipa[] = $_SERVER['HTTP_X_REAL_IP'];

		// проверяем ip-адреса на валидность начиная с приоритетного.
		foreach($ipa as $ips)
		{
			//  если ip валидный обрываем цикл, назначаем ip адрес и возвращаем его
			if($this->isValidIp($ips)){
				$ip = $ips;
				break;
			}
		}
		return $ip;

	}

	/**
	 * функция для проверки валидности ip адреса
	 * @param ip адрес в формате 1.2.3.4
	 * @return bolean : true - если ip валидный, иначе false
	 */
	function isValidIp($ip=null){
		if(preg_match("#^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$#", $ip))
			return true; // если ip-адрес попадает под регулярное выражение, возвращаем true

		return false; // иначе возвращаем false
	}
}