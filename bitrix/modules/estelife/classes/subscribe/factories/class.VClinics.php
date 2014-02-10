<?php
namespace subscribe\factories;
use core\database\VDatabase;
use subscribe\events\VEvent;

/**
 * @author Maxim Shlemarev <shlemarev@gmail.com>
 * @since 30.01.14
 */
class VClinics implements VFactory {
	protected $obEvent;

	public function __construct(VEvent $obEvent){
		$this->obEvent = $obEvent;
	}

	//Выводит все акции из общей подписки
	public function getComplexElements(){
		$nDateSend=$this->obEvent->getOwner()->getDateSend();
		$nDateSend=strtotime($nDateSend);
		$obQuery = VDatabase::driver()
			->createQuery();

		$obJoin=$obQuery->builder()
			->from('estelife_akzii', 'promo')
			->field('promo.name','name')
			->field('promo.id','id')
			->field('clinic_link.clinic_id','clinic_id')
			->join();

		$obJoin->_left()
			->_from('promo','id')
			->_to('estelife_clinic_akzii','akzii_id','clinic_link');

		$obJoin->_left()
			->_from('clinic_link','clinic_id')
			->_to('estelife_clinics','id','clinic');

		$obFilter=$obQuery->builder()
			->filter()
			->_eq('promo.active',1)
			->_gte('promo.date_create', $nDateSend);

		$arFilter=$this->obEvent->getFilter();

		if(!empty($arFilter['city'])){
			$obFilter->_eq('clinic.city_id',intval($arFilter['city']));
		}

		$arResult = $obQuery
			->select()
			->all();

		return $arResult;
	}

	//Выводит все акции из индивидуальной подписки
	public function getTargetElements(){
		$nDateSend=$this->obEvent->getOwner()->getDateSend();
		$nDateSend=strtotime($nDateSend);
		$obQuery = VDatabase::driver()
			->createQuery();

		$obJoin=$obQuery->builder()
			->from('estelife_akzii', 'promo')
			->field('promo.name', 'name')
			->field('promo.id', 'id')
			->field('clinic_link.clinic_id', 'clinic_id')
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
			->_gte('promo.date_create',$nDateSend)
			->_eq('clinic_link.clinic_id',$this->obEvent->getElementId());

		$arFilter=$this->obEvent->getFilter();

		if(!empty($arFilter['city'])){
			$obFilter->_eq('clinic.city_id',intval($arFilter['city']));
		}

		$arResult = $obQuery
			->select()
			->all();

		return $arResult;
	}
}