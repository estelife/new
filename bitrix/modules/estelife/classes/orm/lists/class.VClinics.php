<?php
namespace lists;
use core\database\VDatabase;
use lists\adapters\VAdapter;
use lists\exceptions\VListException;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 02.12.13
 */
class VClinics extends VList {
	protected $arItems;

	public function __construct(){
		parent::__construct();
		$this->arItems=array();
	}

	public function getItems(VAdapter $obAdapter=null){
		$obQuery=VDatabase::driver()->createQuery();
		$obJoin=$obQuery
			->builder()
			->from('estelife_clinics', 'ec')
			->group('ec.id')
			->field('mt.ID','metro_id')
			->field('mt.NAME','metro_name')
			->field('ct.ID','city_id')
			->field('ct.NAME','city_name')
			->field('ct.CODE','city_code')
			->field('ec.id','id')
			->field('ec.dop_text','dop_text')
			->field('ec.recomended', 'recomended')
			->field('ec.address','address')
			->field('ec.name','name')
			->field('ec.logo_id','logo_id')
			->field('eccp.value', 'phone')
			->field('eccw.value', 'web')
			->join();
		$obJoin->_left()
			->_from('ec','city_id')
			->_to('iblock_element','ID','ct')
			->_cond()->_eq('ct.IBLOCK_ID',16);
		$obJoin->_left()
			->_from('ec','metro_id')
			->_to('iblock_element','ID','mt')
			->_cond()->_eq('mt.IBLOCK_ID',17);
		$obJoin->_left()
			->_from('ec', 'id')
			->_to('estelife_clinic_contacts', 'clinic_id', 'eccp')
			->_cond()->_eq('eccp.type', 'phone');
		$obJoin->_left()
			->_from('ec', 'id')
			->_to('estelife_clinic_contacts', 'clinic_id', 'eccw')
			->_cond()->_eq('eccw.type', 'web');
		$obJoin->_left()
			->_from('ec', 'id')
			->_to('estelife_clinic_services', 'clinic_id', 'ecs');

		$obFilter=$obQuery
			->builder()
			->filter()
				->_eq('ec.active', 1)
				->_eq('ec.clinic_id', 0);

		if($this->obFilter){
			if($nCity=$this->obFilter->getField('city'))
				$obFilter->_eq(
					'ec.city_id',
					intval($nCity)
				);

			if($nMetro=$this->obFilter->getField('metro'))
				$obFilter->_eq(
					'ec.metro_id',
					intval($nMetro)
				);

			if($nSpec=$this->obFilter->getField('spec'))
				$obFilter->_eq(
					'ecs.specialization_id',
					intval($nSpec)
				);

			if($nService=$this->obFilter->getField('service'))
				$obFilter->_eq(
					'ecs.service_id',
					intval($nService)
				);

			if($nConcreate=$this->obFilter->getField('concreate'))
				$obFilter->_eq(
					'ecs.service_concreate_id',
					intval($nConcreate)
				);
		}

		$this->setFindCount($obQuery->count());

		if($arSlice=$this->getLimit()){
			$obQuery->builder()
				->slice('ec.'.$arSlice[0],$arSlice[1]);
		}

		if($arSort=$this->getSort()){
			$obQuery->builder()
				->sort($arSort[0],$arSort[1]);
		}

		$obResult=$obQuery
			->select();
		$arResult=array();

		while($arData=$obResult->assoc()){
			if($obAdapter)
				$obAdapter->prepare($arData);

			$arResult[]=$arData;
		}

		return $arResult;
	}

	public function getItem($mIdent){
		$nClinicID=intval($mIdent);

		if(!isset($this->arItems[$nClinicID])){
			if(empty($nClinicID))
				throw new VListException('not found clinic id');

			$obQuery=VDatabase::driver()
				->createQuery();
			
			//Получаем данные по клинике
			$obJoin=$obQuery->builder()
				->from('estelife_clinics', 'ec')
				->join();
			$obJoin->_left()
				->_from('ec','city_id')
				->_to('iblock_element','ID','ct')
				->_cond()->_eq('ct.IBLOCK_ID',16);
			$obJoin->_left()
				->_from('ec','metro_id')
				->_to('iblock_element','ID','mt')
				->_cond()->_eq('mt.IBLOCK_ID',17);
			$obJoin->_left()
				->_from('ec','id')
				->_to('estelife_clinic_contacts','clinic_id','eccp')
				->_cond()->_eq('eccp.type', 'phone');
			$obJoin->_left()
				->_from('ec','id')
				->_to('estelife_clinic_contacts','clinic_id','eccw')
				->_cond()->_eq('eccw.type', 'web');
			$obQuery->builder()
				->field('ec.*')
				->field('ct.NAME', 'city')
				->field('ct.CODE', 'city_code')
				->field('mt.NAME', 'metro')
				->field('eccp.value', 'phone')
				->field('eccw.value', 'web');

			$obQuery->builder()
				->filter()
				->_eq('ec.id', $nClinicID);

			$obResult=$obQuery
				->select();

			if($obResult->count()<=0)
				throw new VListException('clinic not found');

			$arClinic=$obResult->assoc();

			$arClinic['contacts']=array(
				'city'=>$arClinic['city'],
				'address'=>$arClinic['address'],
				'metro'=>$arClinic['metro'],
				'phone'=>$arClinic['phone'],
				'web'=>$arClinic['web']
			);

			unset(
				$arClinic['city'],
				$arClinic['address'],
				$arClinic['metro'],
				$arClinic['phone'],
				$arClinic['web']
			);

			//Получаем платежи
			$obQuery=VDatabase::driver()
				->createQuery();
			$obQuery->builder()->from('estelife_clinic_pays');
			$obQuery->builder()->filter()
				->_eq('clinic_id', $nClinicID);
			$arClinic['pays']=$obQuery
				->select()
				->all();

			//получаем услуги
			$obQuery=VDatabase::driver()
				->createQuery();
			$obJoin=$obQuery->builder()
				->from('estelife_clinic_services', 'ecs')
				->field('es.name','s_name')
				->field('es.id','s_id')
				->field('eser.name','ser_name')
				->field('eser.id','ser_id')
				->field('econ.name','con_name')
				->field('econ.id','con_id')
				->field('ecs.price_from')
				->join();
			$obJoin->_left()
				->_from('ecs','specialization_id')
				->_to('estelife_specializations','id','es');
			$obJoin->_left()
				->_from('ecs','service_id')
				->_to('estelife_services','id','eser');
			$obJoin->_left()
				->_from('ecs','service_concreate_id')
				->_to('estelife_service_concreate','id','econ');
			$obQuery->builder()->filter()
				->_eq('ecs.clinic_id', $nClinicID);
			$arClinic['services']=$obQuery
				->select()
				->all();

			//Получаем галерею
			$obQuery=VDatabase::driver()
				->createQuery();
			$obQuery->builder()
				->from('estelife_clinic_photos')
				->filter()
				->_eq('clinic_id',$nClinicID);

			$arClinic['gallery']=$obQuery
				->select()
				->all();

			//Получаем акции
			$obQuery=VDatabase::driver()
				->createQuery();
			$obJoin=$obQuery->builder()
				->from('estelife_clinic_akzii', 'ecs')
				->field('ea.id','id')
				->field('ea.name','name')
				->field('ea.end_date','end_date')
				->field('ea.base_old_price','old_price')
				->field('ea.base_new_price','new_price')
				->field('ea.base_sale','sale')
				->field('ea.big_photo','logo_id')
				->join();
			$obJoin->_left()
				->_from('ecs','akzii_id')
				->_to('estelife_akzii','id','ea');
			$obQuery->builder()
				->filter()
				->_eq('ecs.clinic_id', $nClinicID);
			$arClinic['promotions']=$obQuery
				->select()
				->all();

			//Получаем филиалы
			$obQuery=VDatabase::driver()
				->createQuery();
			$obJoin=$obQuery->builder()
				->from('estelife_clinics', 'ec')
				->field('ec.address', 'address')
				->field('ct.NAME', 'city')
				->field('mt.NAME', 'metro')
				->field('eccp.value', 'phone')
				->field('eccw.value', 'web')
				->join();
			$obJoin->_left()
				->_from('ec','city_id')
				->_to('iblock_element','ID','ct')
				->_cond()->_eq('ct.IBLOCK_ID',16);
			$obJoin->_left()
				->_from('ec','metro_id')
				->_to('iblock_element','ID','mt')
				->_cond()->_eq('mt.IBLOCK_ID',17);
			$obJoin->_left()
				->_from('ec','id')
				->_to('estelife_clinic_contacts','clinic_id','eccp')
				->_cond()->_eq('eccp.type', 'phone');
			$obJoin->_left()
				->_from('ec','id')
				->_to('estelife_clinic_contacts','clinic_id','eccw')
				->_cond()->_eq('eccw.type', 'web');
			$obQuery->builder()
				->filter()
				->_eq('ec.clinic_id', $nClinicID);
			$arClinic['filial']=$obQuery
				->select()
				->all();

			$this->arItems[$nClinicID]=new VClinic($arClinic);
		}

		return $this->arItems[$nClinicID];
	}
}