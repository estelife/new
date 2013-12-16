<?php
namespace lists;
use core\database\VDatabase;
use lists\adapters\VAdapter;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 02.12.13
 */
class VActions extends VList {
	public function __construct(){
		parent::__construct();
	}

	/**
	 * Этот метод нао переопределить для получения списка элемнетов
	 * @param adapters\VAdapter $obAdapter
	 * @return array
	 */
	public function getItems(VAdapter $obAdapter){
//Получение списка акций
		$obQuery=VDatabase::driver()->createQuery();
		$obJoin=$obQuery->builder()
			->from('estelife_akzii', 'ea')
			->field('ea.id','id')
			->field('ea.name','name')
			->field('ea.end_date','end_date')
			->field('ea.base_old_price','old_price')
			->field('ea.base_new_price','new_price')
			->field('ea.base_sale','sale')
			->field('ea.big_photo','logo_id')
			->field('ct.CODE', 'city_code')
			->field('ea.small_photo','s_logo_id')
			->group('eca.clinic_id')
			->join();
		$obJoin->_left()
			->_from('ea','id')
			->_to('estelife_clinic_akzii','akzii_id','eca');
		$obJoin->_left()
			->_from('eca','clinic_id')
			->_to('estelife_clinics','id','ec');
		$obJoin->_left()
			->_from('ec','city_id')
			->_to('iblock_element','ID','ct')
			->_cond()->_eq('ct.IBLOCK_ID',16);

		$obFilter=$obQuery->builder()
			->filter()
			->_eq('ea.active', 1);

		if($this->obFilter){
			if($nDate=$this->obFilter->getField('date'))
				$obFilter->_gte('ea.end_date',intval($nDate));

			if($nCity=$this->obFilter->getField('city'))
				$obFilter->_eq('eca.city_id',intval($nCity));

			if($nMetro=$this->obFilter->getField('metro'))
				$obFilter->_eq('ec.metro_id', intval($nMetro));

			if($nSpec=$this->obFilter->getField('spec'))
				$obFilter->_eq('ea.specialization_id', intval($nSpec));

			if($nService=$this->obFilter->getField('service'))
				$obFilter->_eq('ea.service_id', intval($nService));

			if($nConcreate=$this->obFilter->getField('concreate'))
				$obFilter->_eq('ea.service_concreate_id', intval($nConcreate));
		}

		$this->setFindCount($obQuery->count());

		if($arSort=$this->getSort()){
			$obQuery->builder()
				->sort('ea.'.$arSort[0], $arSort[1]);
		}

		if($arSlice=$this->getLimit()){
			$obQuery->builder()
				->slice($arSlice[0],$arSlice[1]);
		}

		$obResult=$obQuery->select();
		$arResult=array();

		while($arData=$obResult->assoc()){
			if($obAdapter)
				$obAdapter->prepare($arData);

			$arResult[]=$arData;
		}

		return $arResult;
	}

	/**
	 * Этот метод требуется переопределить для получения одного элемента
	 * @param $mIdent
	 * @return mixed
	 */
	public function getItem($mIdent){

	}
}