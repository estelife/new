<?php
namespace request;
use request\exceptions\VRequest as Error;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 17.01.14
 */
class VCompany {
	private $nCity;
	private $sName;
	private $nType;
	private $nId;

	const CLINIC=1;
	const PRODUCER=2;
	const TRAINING_CENTER=3;
	const SPONSOR=4;

	/**
		 * Инициализация данных класса, тип компании должен соответствовать значению одной из констант
		 * @param $nCity
		 * @param $nType
		 * @param $sName
		 * @param int $nId
		 * @throws exceptions\VRequest
		 */
	public function __construct($nCity,$nType,$sName,$nId=0){
				$this->nCity=intval($nCity);
				$this->sName=$sName;
				$this->nType=intval($nType);
				$this->nId=intval($nId);
		
				if(empty($this->nCity))
						throw new Error('undefined company city');

		if(empty($this->sName))
						throw new Error('undefined company name');
	}

	public function getCity(){
				return $this->nCity;
	}

	public function getName(){
				return $this->sName;
	}

	public function getType(){
				return $this->nType;
	}

	public function getId(){
				return $this->nId;
	}
}