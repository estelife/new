<?php
namespace subscribe\owners;
use core\database\VDatabase;
use core\types\VString;
use subscribe\exceptions as errors;

/**
 * @author Maxim Shlemarev <shlemarev@gmail.com>
 * @since 30.01.14
 */
class VOwner {
	protected $nOwnerId;
	protected $sEmail;

	/**
	 * Инициализация полей класса
	 * @param $nOwnerId
	 * @param $sEmail
	 * @throws \subscribe\exceptions\VOwnerEx
	 */
	public function __construct($nOwnerId,$sEmail){
		$nOwnerId=intval($nOwnerId);
		$sEmail=_addslashes($sEmail);

		if(empty($nOwnerId))
			throw new errors\VOwnerEx('invalid owner id');

		if(!VString::isEmail($sEmail))
			throw new errors\VOwnerEx('invalid email');

		$this->nOwnerId=$nOwnerId;
		$this->sEmail=$sEmail;
	}

	/**
	 * Получение событий пользователя
	 * @return array
	 */
	public function getEvents(){
		$obSubscribe = \core\database\VDatabase::driver();

		$obQuery=$obSubscribe->createQuery();
		$obQuery->builder()->from('estelife_subscribe_events')
			->filter()
			->_eq('subscribe_user_id', $this->nSubscribeUserId);
		$arEvents = $obQuery->select()->all();

		return $arEvents;
	}

	public function setEvent($nType, $nTotal, array $arFilter=null){
		$obSubscribe = VDatabase::driver();
		$obQueryInsert=$obSubscribe->createQuery();

		if($nTotal==1){
			//Проверка на существование общей подписки
			$obQuerySub=$obSubscribe->createQuery();
			$obQuerySub->builder()->from('estelife_subscribe_events')
				->filter()
				->_eq('owner_id', $this->nOwnerId)
				->_eq('total',1)
				->_eq('type',$type);
			$arSub = $obQuerySub->select()->assoc();

			if($arSub >0){

			}else{

				$obQueryInsert->builder()->from('estelife_subscribe_events')
					->value('type', $type)
					->value('subscribe_user_id', $nUser)
					->value('filter', $filter)
					->value('total', 1)
					->value('event_active', 1)
					->value('date_send', time());
				$nSubsInsert = $obQueryInsert->insert()->insertId();
			}
		}else{
			//Проверка на существование одиночной подписки
			$obQuerySub=$obSubscribe->createQuery();
			$obQuerySub->builder()->from('estelife_subscribe_events')
				->filter()
				->_eq('subscribe_user_id', $nUser)
				->_eq('total',0)
				->_eq('type',$filter)
				->_eq('filter',$filter);
			$arSub = $obQuerySub->select()->assoc();

			if($arSub >0){

			}else{

				$obQueryInsert->builder()->from('estelife_subscribe_events')
					->value('type', $type)
					->value('subscribe_user_id', $nUser)
					->value('event_active', 1)
					->value('filter', $filter)
					->value('total', 0)
					->value('date_send', time());
				$nSubsInsert = $obQueryInsert->insert()->insertId();

			}
		}
		return $nSubsInsert;
	}

}