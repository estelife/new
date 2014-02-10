<?php
namespace subscribe;
use core\database\VDatabase;
use core\types\VString;
use subscribe\errors as errors;

/**
 *
 * @author Maxim Shlemarev <shlemarev@gmail.com>
 * @since 30.01.14
 */
class VOwner {
	private $nOwnerId;

	public function __construct($nOwnerId,$sEmail){
		$nOwnerId=intval($nOwnerId);

		if($nOwnerId<=0)
			throw new errors\VOwnerEx('invalid owner id');

		if(!VString::isEmail($sEmail))
			throw new errors\VOwnerEx('invalid owner email');

		$this->nOwnerId=$nOwnerId;
		$this->sEmail=addslashes($sEmail);
	}
	//Получение событий рассылки
	public function getEvents(){
		$obSubscribe = \core\database\VDatabase::driver();

		$obQuery=$obSubscribe->createQuery();
		$obQuery->builder()->from('estelife_subscribe_events')
			->filter()
			->_eq('subscribe_user_id', $this->nSubscribeUserId);
		$arEvents = $obQuery->select()->all();

		return $arEvents;
	}

	public static function getAllEvents($nUserId){
		$obSubscribe = \core\database\VDatabase::driver();

		$obQuery=$obSubscribe->createQuery();
		$obQuery->builder()->from('estelife_subscribe_events')
			->filter()
			->_eq('subscribe_user_id',$nUserId);
		$arEvents = $obQuery->select()->all();

		return $arEvents;
	}

	public static function getTargetClinicEvents($nUserId){
		$obSubscribe = \core\database\VDatabase::driver();

		$obQuery=$obSubscribe->createQuery();
		$obQuery->builder()->from('estelife_subscribe_events')
			->filter()
			->_eq('type',1)
			->_eq('total',0)
			->_eq('subscribe_user_id',$nUserId);
		$arEvents = $obQuery->select()->all();

		return $arEvents;
	}

	public static function getTargetTrainingsEvents($nUserId){
		$obSubscribe = \core\database\VDatabase::driver();

		$obQuery=$obSubscribe->createQuery();
		$obQuery->builder()->from('estelife_subscribe_events')
			->filter()
			->_eq('type', 2)
			->_eq('total',0)
			->_eq('subscribe_user_id',$nUserId);
		$arEvents = $obQuery->select()->all();

		return $arEvents;
	}

	public static function getComplexClinicEvents($nUserId){
		$obSubscribe = \core\database\VDatabase::driver();

		$obQuery=$obSubscribe->createQuery();
		$obQuery->builder()->from('estelife_subscribe_events')
			->filter()
			->_eq('type',1)
			->_eq('total',1)
			->_eq('subscribe_user_id',$nUserId);
		$arEvents = $obQuery->select()->all();

		return $arEvents;
	}

	public static function getComplexTrainingsEvents($nUserId){
		$obSubscribe = \core\database\VDatabase::driver();

		$obQuery=$obSubscribe->createQuery();
		$obQuery->builder()->from('estelife_subscribe_events')
			->filter()
			->_eq('type', 2)
			->_eq('total',1)
			->_eq('subscribe_user_id',$nUserId);
		$arEvents = $obQuery->select()->all();

		return $arEvents;
	}

	public static function getAllTrainingsEvents($nUserId){
		$obSubscribe = \core\database\VDatabase::driver();

		$obQuery=$obSubscribe->createQuery();
		$obQuery->builder()->from('estelife_subscribe_events')
			->filter()
			->_eq('type', 2)
			->_eq('subscribe_user_id',$nUserId);
		$arEvents = $obQuery->select()->all();

		return $arEvents;
	}

	public static function getAllClinicsEvents($nUserId){
		$obSubscribe = \core\database\VDatabase::driver();

		$obQuery=$obSubscribe->createQuery();
		$obQuery->builder()->from('estelife_subscribe_events')
			->filter()
			->_eq('type', 1)
			->_eq('subscribe_user_id',$nUserId);
		$arEvents = $obQuery->select()->all();

		return $arEvents;
	}


	public static function setSubscribe($type, $email, $always, $filter){
		$obSubscribe = VDatabase::driver();

		//Проверка на существование пользователя
		$obQuery=$obSubscribe->createQuery();
		$obQuery->builder()->from('estelife_subscribe_user')
			->filter()
			->_eq('email', $email);
		$arUser = $obQuery->select()->assoc();

		if ($arUser>0){
			$nUser = $arUser['user_id'];

		}else{
			$obQueryMaxUser=$obSubscribe->createQuery();
			$obQueryMaxUser->builder()->from('estelife_subscribe_user');
			$obQueryMaxUser->builder()->sort('user_id','desc');

			$arQueryMaxUser = $obQueryMaxUser->select()->assoc();
			$nUser = $arQueryMaxUser['user_id']+1;

			$time = time();

			$obQueryInsertUser=$obSubscribe->createQuery();
			$obQueryInsertUser->builder()->from('estelife_subscribe_user')
				->value('email', $email)
				->value('event_active', 1)
				->value('date_last_send', $time);
			$nSubsInsertUser = $obQueryInsertUser->insert()->insertId();



			$sHashLink = md5($email.$time);

			$arFields = array(
				'EMAIL_TO'=>$email,
				'LINK'=>$sHashLink,
			);

			CEvent::Send("SEND_SUBSCRIBE_EMAIL", "s1", $arFields,"Y",59);
		}

		$obQueryInsert=$obSubscribe->createQuery();

		if($always == 1){

			//Проверка на существование общей подписки
			$obQuerySub=$obSubscribe->createQuery();
			$obQuerySub->builder()->from('estelife_subscribe_events')
				->filter()
				->_eq('subscribe_user_id', $nUser)
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