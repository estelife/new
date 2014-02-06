<?php
namespace subscribe;

/**
 *
 * @author Maxim Shlemarev <shlemarev@gmail.com>
 * @since 30.01.14
 */


class ClinicFactory {

	public function __construct(VUser $vUser){
		$this->obUser = $vUser;
	}

	//Выводит все акции из общей подписки
	public function getComplex($arUser){

		$nUserId = $arUser['id'];
		$nDateLastSend = $arUser['date_last_send'];


		$obData = \core\database\VDatabase::driver();

		$obQuery = $obData->createQuery();
		$obQuery->builder()
			->from('estelife_subscribe_events')
			->filter()
			->_eq('type', 1)
			->_eq('total', 1)
			->_eq('event_active',1)
			->_eq('subscribe_user_id',$nUserId);

		$arData = $obQuery->select()->all();


		$arResult = array();
		$arClinicsTargetActii = array();
		$arClinicsComplexActii = array();

		foreach($arData as $arEvent){

			$filter = $arEvent['filter'];
			$filter = unserialize($filter);
			$date_send = $arEvent['date_send'];
			$city_id = $filter;


			$current_time = time();


				$obQueryClinics = $obData->createQuery();
				$obQueryClinics->builder()->from('estelife_clinics', 'ec');

				$obQueryClinics->builder()
					->field('ec.id','id');

				$obQueryClinics->builder()->filter()
					//->_lte('ea.date_create', $date_sent)
					//->_eq('ec.city_id',$city_id)
					->_eq('ec.active', 1);
				$arClinicsData = $obQueryClinics->select()->all();

				foreach($arClinicsData as $arClinicId){
					$nClinicId = $arClinicId['id'];

					$obQueryAkcii = $obData->createQuery();
					$obQueryAkcii->builder()->from('estelife_akzii', 'ea');
					$obJoin=$obQueryAkcii->builder()->join();
					$obJoin->_left()
						->_from('ea','id')
						->_to('estelife_clinic_akzii','akzii_id','ca');
					$obJoin->_left()
						->_from('ca','clinic_id')
						->_to('estelife_clinics','id','ec');

					$obQueryAkcii->builder()
						->field('ea.name','name')
						->field('ea.id','id')
						->field('ea.start_date','start_date')
						->field('ea.end_date','end_date')
						->field('ca.clinic_id','clinic_id');

					$obQueryAkcii->builder()->filter()
						//->_lte('ea.date_create', $date_sent)
						->_eq('ea.active',1)
						->_gte('ea.date_create', $date_send)
						->_eq('ec.id',$nClinicId)
						->_gte('ea.end_date', $current_time);
					$arAkciiData = $obQueryAkcii->select()->all();

					if(!empty($arAkciiData)){
						$arClinicsComplexActii[$nClinicId] = $arAkciiData;
					}
				}


		}

		if(!empty($arClinicsComplexActii)){
			$arResult['complex'] = $arClinicsComplexActii;
		}

		ClinicFactory::updateDates('complex',$nUserId);

		return $arResult;

	}

	//Выводит все акции из индивидуальной подписки
	public function getTarget($arUser){

		$nUserId = $arUser['id'];
		$nDateLastSend = $arUser['date_last_send'];


		$obData = \core\database\VDatabase::driver();

		$obQuery = $obData->createQuery();
		$obQuery->builder()
			->from('estelife_subscribe_events')
			->filter()
			->_eq('type', 1)
			->_eq('event_active',1)
			->_eq('total',0)
			->_eq('subscribe_user_id',$nUserId);

		$arData = $obQuery->select()->all();



		$arResult = array();
		$arClinicsTargetActii = array();


		foreach($arData as $arEvent){

			$filter = $arEvent['filter'];
			$filter = unserialize($filter);
			$clinic_id = $filter['id'];
			$date_send = $arEvent['date_send'];


			$current_time = time();

			if(!empty($clinic_id)){

				$obQueryAkcii = $obData->createQuery();
				$obQueryAkcii->builder()->from('estelife_akzii', 'ea');
				$obJoin=$obQueryAkcii->builder()->join();
				$obJoin->_left()
					->_from('ea','id')
					->_to('estelife_clinic_akzii','akzii_id','ca');
				$obJoin->_left()
					->_from('ca','clinic_id')
					->_to('estelife_clinics','id','ec');

				$obQueryAkcii->builder()
					->field('ea.name','name')
					->field('ea.id','id')
					->field('ea.start_date','start_date')
					->field('ea.end_date','end_date')
					->field('ca.clinic_id','clinic_id');

				$obQueryAkcii->builder()->filter()
					//->_lte('ea.date_create', $date_sent)
					->_eq('ea.active',1)
					->_gte('ea.date_create', $date_send)
					->_eq('ec.id',$clinic_id)
					->_gte('ea.end_date', $current_time);
				$arAkciiData = $obQueryAkcii->select()->all();


				if(!empty($arAkciiData)){
					$arClinicsTargetActii[$clinic_id] = $arAkciiData;
				}


			}

		}

		if(!empty($arClinicsTargetActii)){
			$arResult['target'] = $arClinicsTargetActii;
		}

		ClinicFactory::updateDates('target',$nUserId);

		return $arResult;

	}

	//Выводит все акции из всевозможных подписок для текущего пользователя
	public  function  getAll($arUser){
		$nUserId = $arUser['id'];
		$nDateLastSend = $arUser['date_last_send'];


		$obData = \core\database\VDatabase::driver();

		$obQuery = $obData->createQuery();
		$obQuery->builder()
			->from('estelife_subscribe_events')
			->filter()
			->_eq('type', 1)
			->_eq('event_active',1)
			->_eq('subscribe_user_id',$nUserId);

		$arData = $obQuery->select()->all();

		$arResult = array();
		$arClinicsTargetActii = array();
		$arClinicsComplexActii = array();

		foreach($arData as $arEvent){



			$filter = $arEvent['filter'];
			$filter = unserialize($filter);

			if(is_array($filter)){
				$clinic_id = $filter['id'];;
				$city_id = $filter['city_id'];
			}else{
				$city_id = $filter;
				$clinic_id = 0;
			}

			$date_send = $arEvent['date_send'];

			$current_time = time();

			if(!empty($clinic_id)){


				$obQueryAkcii = $obData->createQuery();
				$obQueryAkcii->builder()->from('estelife_akzii', 'ea');
				$obJoin=$obQueryAkcii->builder()->join();
				$obJoin->_left()
					->_from('ea','id')
					->_to('estelife_clinic_akzii','akzii_id','ca');
				$obJoin->_left()
					->_from('ca','clinic_id')
					->_to('estelife_clinics','id','ec');

				$obQueryAkcii->builder()
					->field('ea.name','name')
					->field('ea.id','id')
					->field('ea.start_date','start_date')
					->field('ea.end_date','end_date')
					->field('ca.clinic_id','clinic_id');

				$obQueryAkcii->builder()->filter()
					//->_lte('ea.date_create', $date_sent)
					->_eq('ea.active',1)
					->_gte('ea.date_create', $date_send)
					->_eq('ec.id',$clinic_id)
					->_gte('ea.end_date', $current_time);
				$arAkciiData = $obQueryAkcii->select()->all();


				if(!empty($arAkciiData)){
					$arClinicsTargetActii[$clinic_id] = $arAkciiData;
				}


			}else{


				$obQueryClinics = $obData->createQuery();
				$obQueryClinics->builder()->from('estelife_clinics', 'ec');

				$obQueryClinics->builder()
					->field('ec.id','id');

				$obQueryClinics->builder()->filter()
					//->_lte('ea.date_create', $date_sent)
					->_eq('ec.city_id',$city_id)
					->_eq('ec.active', 1);
				$arClinicsData = $obQueryClinics->select()->all();

				foreach($arClinicsData as $arClinicId){
					$nClinicId = $arClinicId['id'];

					$obQueryAkcii = $obData->createQuery();
					$obQueryAkcii->builder()->from('estelife_akzii', 'ea');
					$obJoin=$obQueryAkcii->builder()->join();
					$obJoin->_left()
						->_from('ea','id')
						->_to('estelife_clinic_akzii','akzii_id','ca');
					$obJoin->_left()
						->_from('ca','clinic_id')
						->_to('estelife_clinics','id','ec');

					$obQueryAkcii->builder()
						->field('ea.name','name')
						->field('ea.id','id')
						->field('ea.start_date','start_date')
						->field('ea.end_date','end_date')
						->field('ca.clinic_id','clinic_id');

					$obQueryAkcii->builder()->filter()
						//->_lte('ea.date_create', $date_sent)
						->_eq('ea.active',1)
						->_gte('ea.date_create', $date_send)
						->_eq('ec.id',$nClinicId)
						->_gte('ea.end_date', $current_time);
					$arAkciiData = $obQueryAkcii->select()->all();

					if(!empty($arAkciiData)){
						$arClinicsComplexActii[$nClinicId] = $arAkciiData;
					}
				}
			}

		}

		if(!empty($arClinicsComplexActii)){
			$arResult['complex'] = $arClinicsComplexActii;
		}
		if(!empty($arClinicsTargetActii)){
			$arResult['target'] = $arClinicsTargetActii;
		}

		ClinicFactory::updateDates('all',$nUserId);

		return $arResult;
	}


	public  function  updateDates($index,$nUserId){

		$obData = \core\database\VDatabase::driver();
		$obQuery = $obData->createQuery();

		if($index == 'all'){
			$arEvents = VUser::getAllClinicsEvents($nUserId);

			foreach($arEvents as $arEvent){
				$obQuery->builder()->from('estelife_subscribe_events')
					->value('date_send', time());

				$obQuery->builder()->filter()
					->_eq('id',$arEvent['id']);
				$obQuery->update();
			}
		}

		if($index == 'target'){
			$arEvents = VUser::getTargetClinicEvents($nUserId);

			foreach($arEvents as $arEvent){
				$obQuery->builder()->from('estelife_subscribe_events')
					->value('date_send', time());

				$obQuery->builder()->filter()
					->_eq('id',$arEvent['id']);
				$obQuery->update();
			}
		}

		if($index == 'complex'){
			$arEvents = VUser::getComplexClinicEvents($nUserId);

			foreach($arEvents as $arEvent){
				$obQuery->builder()->from('estelife_subscribe_events')
					->value('date_send', time());

				$obQuery->builder()->filter()
					->_eq('id',$arEvent['id']);
				$obQuery->update();
			}
		}
	}


}