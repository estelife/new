<?php
namespace subscribe\factories;
use core\database\VDatabase;
use subscribe\events\VEvent;
use subscribe\exceptions as errors;
use subscribe\owners\VOwner;

/**
 * @author Maxim Shlemarev <shlemarev@gmail.com>
 * @since 30.01.14
 */
class VTrainings implements VFactory {
	protected $obEvent;
	protected $obOwner;

	public function __construct(VOwner $obOwner,VEvent $obEvent){
		$this->obEvent = $obEvent;
		$this->obOwner = $obOwner;
	}

	public function getElements(){
		$nDateFrom=$this->obOwner->getDateFromAsInteger();
		$nDateTo=$this->obOwner->getDateToAsInteger();

		$arFilter=$this->obEvent->getFilter();
		$nCompanyId=$this->obEvent
			->getElementId();

		if(empty($nCompanyId))
			throw new errors\VFactoryEx('undefined company id');

		$obQuery = VDatabase::driver()
			->createQuery();
		$obJoin = $obQuery->builder()
			->from('estelife_events', 'event')
			->sort('event.date_create','desc')
			->field('event.id','id')
			->field('event.short_name','name')
			->field('company.name', 'company_name')
			->field('company.id', 'company_id')
			->field('calendar.date','date')
			->join();
		$obJoin->_left()
			->_from('event','id')
			->_to('estelife_company_events','event_id','company_link');
		$obJoin->_left()
			->_from('event','id')
			->_to('estelife_event_types','event_id','type');
		$obJoin->_left()
			->_from('company_link','company_id')
			->_to('estelife_companies','id','company');
		$obJoin->_left()
			->_from('event','id')
			->_to('estelife_calendar','event_id','calendar');

		$obFilter=$obQuery->builder()
			->filter()
			->_eq('type.type',3)
			->_gte('event.date_create', $nDateFrom)
			->_lte('event.date_create', $nDateTo);

		if(!empty($arFilter['city']))
			$obFilter->_eq('event.city_id',intval($arFilter['city']));

		if(!empty($nCompanyId))
			$obFilter->_eq('company.id',$nCompanyId);

		$arResult=$obQuery
			->select()
			->all();

		if(!empty($arResult)){
			$arGroup=array();

			foreach($arResult as $arElement){
				if(!isset($arGroup[$arElement['company_id']])){
					$arGroup[$arElement['company_id']]['name']=$arGroup[$arElement['company_name']];
					$arGroup[$arElement['company_id']]['id']=$arGroup[$arElement['company_id']];
					$arGroup[$arElement['company_id']]['elements']=array();
				}

				$arGroup[$arElement['company_id']]['elements'][]=$arElement;
			}

			$arResult=$arGroup;
		}

		return $arResult;
	}
}