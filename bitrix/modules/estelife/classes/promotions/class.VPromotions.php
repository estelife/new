<?php
namespace promotions;
use core\database\VDatabase;

class VPromotions {

	/**
	 * Получение похожих акций
	 * @param array $arActionId
	 * @param $nCityId
	 * @param $nCount
	 * @param $sDopKey
	 * @param $sDopValue
	 * @return array
	 */
	public static function getSimilarPromotions(array $arActionId, $nCityId, $nCount, $sDopKey=false, $sDopValue=false){
		if (empty($arActionId))
			assert('Action ID is empty');

		if (intval($nCityId)<=0)
			assert('Dop filter is empty');

		if (intval($nCount)<=0)
			assert('Count is empty');

		$obDriver = VDatabase::driver();
		$obQuery = $obDriver->createQuery();
		$obBuilder = $obQuery->builder();
		$obJoin = $obBuilder
			->from('estelife_akzii','ea')
			->sort($obQuery->builder()->_rand())
			->field('ea.*')
			->field('ec.name','clinic_name')
			->field('ec.id','clinic_id')
			->field('ec.clinic_id','parent_clinic_id')
			->join();
		$obJoin->_left()
			->_from('ea','id')
			->_to('estelife_clinic_akzii','akzii_id','eca');
		$obJoin->_left()
			->_from('eca','clinic_id')
			->_to('estelife_clinics','id','ec');
		$obJoin->_left()
			->_from('ea', 'id')
			->_to('estelife_akzii_types', 'akzii_id', 'eat');
		$obBuilder
			->slice(0, $nCount)
			->group('ea.id');
		$obFilter = $obBuilder->filter()
			->_eq('ea.active', 1)
			->_gte('ea.end_date', time())
			->_eq('ec.city_id', $nCityId)
			->_eq('ec.city_id', $nCityId);
		if (!empty($sDopKey) && !empty($sDopValue))
			$obFilter->_in($sDopKey, $sDopValue);
		foreach ($arActionId as $val){
			$obFilter->_ne('ea.id',$val);
		}

		return $obQuery->select()->all();
	}


}