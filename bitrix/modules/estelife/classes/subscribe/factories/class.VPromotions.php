<?php
namespace subscribe\factories;
use core\database\VDatabase;
use subscribe\events\VEvent;
use subscribe\owners\VOwner;

/**
 * @author Maxim Shlemarev <shlemarev@gmail.com>
 * @since 30.01.14
 */
class VPromotions implements VFactory {
	protected $obEvent;
	protected $obOwner;

	public function __construct(VOwner $obOwner,VEvent $obEvent){
		$this->obEvent = $obEvent;
		$this->obOwner = $obOwner;
	}

	//Выводит все акции из индивидуальной подписки
	public function getElements(){
		$nDateFrom=$this->obOwner->getDateFromAsInteger();
		$nDateTo=$this->obOwner->getDateToAsInteger();

		$obQuery = VDatabase::driver()
			->createQuery();

		$obJoin=$obQuery->builder()
			->from('estelife_akzii', 'promo')
			->field('promo.name', 'name')
			->field('promo.id', 'id')
			->field('clinic.id', 'clinic_id')
			->field('clinic.name', 'clinic_name')
			->join();

		$obJoin->_left()
			->_from('promo','id')
			->_to('estelife_clinic_akzii', 'akzii_id', 'clinic_link');

		$obJoin->_left()
			->_from('clinic_link','clinic_id')
			->_to('estelife_clinics','id','clinic');

		$obFilter=$obQuery->builder()
			->filter()
			->_eq('promo.active',1)
			->_gte('promo.date_create',$nDateFrom)
			->_lte('promo.date_create',$nDateTo);

		$arFilter=$this->obEvent->getFilter();

		if(!empty($arFilter['city']))
			$obFilter->_eq('clinic.city_id',intval($arFilter['city']));

		if(!empty($nClinicId))
			$obFilter->_eq('clinic_link.clinic_id',$nClinicId);

		$arResult = $obQuery
			->select()
			->all();
		
		if(!empty($arResult)){
			$arGroup=array();

			foreach($arResult as $arElement){
				if(!isset($arGroup[$arElement['clinic_id']])){
					$arGroup[$arElement['clinic_id']]['name']=$arGroup[$arElement['clinic_name']];
					$arGroup[$arElement['clinic_id']]['id']=$arGroup[$arElement['clinic_id']];
					$arGroup[$arElement['clinic_id']]['elements']=array();
				}

				$arGroup[$arElement['clinic_id']]['elements'][]=$arElement;
			}

			$arResult=$arGroup;
		}

		return $arResult;
	}
}