<?php
namespace subscribe\factories;
use subscribe\events\VEvent;

/**
 * @author Maxim Shlemarev <shlemarev@gmail.com>
 * @since 30.01.14
 */
class VTrainigs implements VFactory {
	protected $obEvent;

	public function __construct(VEvent $obEvent){
		$this->obEvent = $obEvent;
	}

	public function getTargetElements(){
		$nUserId = $arUser['user_id'];
		$nDateLastSend = $arUser['date_last_send'];

		$obData = \core\database\VDatabase::driver();


		$obQuery = $obData->createQuery();
		$obQuery->builder()
			->from('estelife_subscribe_events')
			->filter()
			->_eq('type', 2)
			->_eq('total',0)
			->_eq('event_active',1)
			->_eq('subscribe_user_id',$nUserId);
		$arData = $obQuery->select()->all();

		$arResult= array();
		$arComplexData = array();


		foreach($arData as $arEvent){
			$filter = $arEvent['filter'];
			$filter = unserialize($filter);

			$city_id = $filter['city_id'];
			$campany_id = $filter['id'];

			$date_send = $arEvent['date_send'];
			$current_time = time();

			$obQueryClinics = $obData->createQuery();
			$obQueryClinics->builder()->from('estelife_companies');

			$obQueryClinics->builder()->filter()
				->_eq('id',$campany_id);

			$obQueryClinics->builder()
				->field('name','name')
				->field('id','id');

			$arCompaniesData = $obQueryClinics->select()->all();

			foreach($arCompaniesData as $arCompany){

				$sCampanyName = $arCompany['name'];
				$nCampanyId = $arCompany['id'];


				$obQueryEvents = $obData->createQuery();
				$obQueryEvents->builder()->from('estelife_events', 'ee');
				$obJoin=$obQueryEvents->builder()->join();
				$obJoin->_left()
					->_from('ee','id')
					->_to('estelife_company_events','event_id','ce');
				$obJoin->_left()
					->_from('ee','id')
					->_to('estelife_event_types','event_id','et');
				$obJoin->_left()
					->_from('ce','company_id')
					->_to('estelife_companies','id','ec');
				$obJoin->_left()
					->_from('ee','id')
					->_to('estelife_calendar','event_id','ecal');

				$obQueryEvents->builder()
					->field('ee.id','id')
					->field('ee.short_name','short_name')
					->field('ce.company_id','company_id')
					->field('ee.date_create','date_create')
					->field('ee.city_id',0)
					->field('ecal.date','date');

				$obQueryEvents->builder()->filter()
					->_gte('ecal.date', $current_time)
					->_eq('et.type',3)
					->_eq('ee.city_id',0)
					->_gte('ee.date_create', $date_send)
					->_eq('ec.id',$nCampanyId);

				$arEventsData = $obQueryEvents->select()->all();



				if(!empty($arEventsData)){
					foreach($arEventsData as $event){
						$arComplexData[$event['id']] = $event;
					}
				}

			}

		}
		$arResult['target'] = $arComplexData;

		TrainigsFactory::updateDates('target',$nUserId);

		return $arResult;
	}

	public function getComplexElements(){
		$nUserId = $arUser['user_id'];
		$nDateLastSend = $arUser['date_last_send'];

		$obData = \core\database\VDatabase::driver();


		$obQuery = $obData->createQuery();
		$obQuery->builder()
			->from('estelife_subscribe_events')
			->filter()
			->_eq('type', 2)
			->_eq('total',1)
			->_eq('event_active',1)
			->_eq('subscribe_user_id',$nUserId);
		$arData = $obQuery->select()->all();

		$arResult= array();
		$arComplexData = array();


		foreach($arData as $arEvent){
			$filter = $arEvent['filter'];
			$filter = unserialize($filter);
			$date_send = $arEvent['date_send'];
			$current_time = time();

			$obQueryClinics = $obData->createQuery();
			$obQueryClinics->builder()->from('estelife_companies');

			$obQueryClinics->builder()
				->field('name','name')
				->field('id','id');

			$arCompaniesData = $obQueryClinics->select()->all();

			foreach($arCompaniesData as $arCompany){

				$sCampanyName = $arCompany['name'];
				$nCampanyId = $arCompany['id'];


				$obQueryEvents = $obData->createQuery();
				$obQueryEvents->builder()->from('estelife_events', 'ee');
				$obJoin=$obQueryEvents->builder()->join();
				$obJoin->_left()
					->_from('ee','id')
					->_to('estelife_company_events','event_id','ce');
				$obJoin->_left()
					->_from('ee','id')
					->_to('estelife_event_types','event_id','et');
				$obJoin->_left()
					->_from('ce','company_id')
					->_to('estelife_companies','id','ec');
				$obJoin->_left()
					->_from('ee','id')
					->_to('estelife_calendar','event_id','ecal');

				$obQueryEvents->builder()
					->field('ee.id','id')
					->field('ee.short_name','short_name')
					->field('ce.company_id','company_id')
					->field('ee.date_create','date_create')
					->field('ee.city_id',0)
					->field('ecal.date','date');

				$obQueryEvents->builder()->filter()
					->_gte('ecal.date', $current_time)
					->_eq('et.type',3)
					->_eq('ee.city_id',0)
					->_gte('ee.date_create', $date_send)
					->_eq('ec.id',$nCampanyId);

				$arEventsData = $obQueryEvents->select()->all();



				if(!empty($arEventsData)){
					foreach($arEventsData as $event){
						$arComplexData[$event['id']] = $event;
					}
				}

			}

		}
		$arResult['complex'] = $arComplexData;

		TrainigsFactory::updateDates('complex',$nUserId);

		return $arResult;
	}
}