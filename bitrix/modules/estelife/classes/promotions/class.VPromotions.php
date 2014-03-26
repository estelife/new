<?php
namespace promotions;
use core\database\VDatabase;

class VPromotions {

	/**
	 * Получение похожих акций
	 * @param $nActionId
	 * @param $nCityId
	 * @param $nCount
	 * @param $sDopKey
	 * @param array $sDopValue
	 * @return array
	 */
	public static function getSimilarPromotions($nActionId, $nCityId, $nCount, $sDopKey, array $sDopValue){
		if (intval($nActionId)<=0)
			assert('Action ID is empty');

		if (intval($nCityId)<=0)
			assert('Dop filter is empty');

		if (intval($nCount)<=0)
			assert('Count is empty');

		if (empty($sDopKey))
			assert('DopKey is empty');

		if (empty($sDopValue))
			assert('DopValue is empty');

		$obDriver = VDatabase::driver();
		$obQuery = $obDriver->createQuery();
		$obBuilder = $obQuery->builder();
		$obJoin = $obBuilder
			->from('estelife_akzii','ea')
			->sort($obQuery->builder()->_rand())
			->field('ea.*')
			->field('ec.name','clinic_name')
			->field('ec.id','clinic_id')
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
			->group('ea.id')
			->filter()
			->_eq('ea.active', 1)
			->_ne('ea.id',$nActionId)
			->_gte('ea.end_date', time())
			->_eq('ec.city_id', $nCityId)
			->_eq('ec.city_id', $nCityId)
			->_in($sDopKey, $sDopValue);

		return $obQuery->select()->all();
	}


}