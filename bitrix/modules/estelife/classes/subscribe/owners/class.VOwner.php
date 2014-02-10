<?php
namespace subscribe\owners;
use core\database\VDatabase;
use core\types\VString;
use subscribe\aggregators\VAggregator;
use subscribe\events\VEvent;
use subscribe\exceptions as errors;

/**
 * @author Maxim Shlemarev <shlemarev@gmail.com>
 * @since 30.01.14
 */
class VOwner {
	protected $nOwnerId;
	protected $sEmail;
	private $sDateSend;

	/**
	 * Инициализация полей класса
	 * @param $nOwnerId
	 * @param $sEmail
	 * @param $sDateSend
	 * @throws \subscribe\exceptions\VOwnerEx
	 */
	public function __construct($nOwnerId,$sEmail,$sDateSend){
		$nOwnerId=intval($nOwnerId);
		$sEmail=_addslashes($sEmail);

		if(empty($nOwnerId))
			throw new errors\VOwnerEx('invalid owner id');

		if(!VString::isEmail($sEmail))
			throw new errors\VOwnerEx('invalid email');

		$this->nOwnerId=$nOwnerId;
		$this->sEmail=$sEmail;
		$this->sDateSend=$sDateSend;
	}

	/**
	 * Отдает идентификатор владельца события
	 * @return int
	 */
	public function getOwnerId(){
		return $this->nOwnerId;
	}

	/**
	 * Отдает email пользователя
	 * @return mixed
	 */
	public function getEmail(){
		return $this->sEmail;
	}

	/**
	 * Отдает дату отправки
	 * @return mixed
	 */
	public function getDateSend(){
		return $this->sDateSend;
	}

	/**
	 * Получение событий пользователя
	 * @param \subscribe\aggregators\VAggregator $obAggregator
	 * @return \subscribe\events\VEvent[]
	 */
	public function getEvents(VAggregator $obAggregator=null){
		$obQuery = VDatabase::driver()
			->createQuery();
		$obQuery->builder()
			->from('estelife_subscribe_events')
			->filter()
			->_eq('active',1)
			->_eq('owner_id', $this->nOwnerId);
		$arEvents = $obQuery->select()->all();

		if(!empty($arEvents)){
			$arTemp=array();

			foreach($arEvents as $arEvent){
				$obEvent=new VEvent(
					$this,
					$arEvent['type'],
					$arEvent['element_id'],
					$arEvent['filter']
				);

				if($obAggregator)
					$obAggregator->aggregateItem($obEvent);

				$arTemp[]=$obEvent;
			}

			$arEvents=$arTemp;
			unset($arTemp);
		}

		return $arEvents;
	}

	/**
	 * Добавляет событие подписки для пользователя
	 * @param $nType
	 * @param $nElementId
	 * @param array $arFilter
	 * @throws \subscribe\exceptions\VOwnerEx
	 * @internal param $nTotal
	 * @return void
	 */
	public function setEvent($nType,$nElementId,array $arFilter=null){
		$nType=abs(intval($nType));
		$nElementId=abs(intval($nElementId));

		if($nType==0)
			throw new errors\VOwnerEx('try set event with type');

		$bTotal=($nElementId>0);
		$sFilter=(!empty($arFilter)) ? serialize($arFilter) : '';
		$obQuery=VDatabase::driver()
			->createQuery();

		if($bTotal){
			//Проверка на существование общей подписки
			$obQuery->builder()
				->from('estelife_subscribe_events')
				->filter()
				->_eq('owner_id', $this->nOwnerId)
				->_eq('element_id',0)
				->_eq('type',$nType);
			$nCountTotal = $obQuery
				->count();

			if($nCountTotal==0){
				$obQuery->builder()
					->from('estelife_subscribe_events')
					->value('type', $nType)
					->value('owner_id', $this->nOwnerId)
					->value('filter', $sFilter)
					->value('element_id', 0)
					->value('active', 0);
				$obQuery->insert()
					->insertId();
			}
		}else{
			//Проверка на существование одиночной подписки
			$obQuery->builder()
				->from('estelife_subscribe_events')
				->filter()
				->_eq('owner_id', $this->nOwnerId)
				->_eq('element_id',$nElementId)
				->_eq('type',$nType);
			$arEvent = $obQuery
				->select()
				->assoc();

			$obQuery->builder()
				->from('estelife_subscribe_events')
				->value('type', $nType)
				->value('subscribe_user_id', $this->nOwnerId)
				->value('element_id', $nElementId)
				->value('filter', $sFilter);

			if(!empty($arEvent)){
				$obQuery->builder()
					->value('active', 0);
				$obQuery->insert()
					->insertId();
			}else{
				$obQuery->builder()
					->filter()
					->_eq('id',$arEvent['id']);
				$obQuery->update();
			}
		}
	}

}