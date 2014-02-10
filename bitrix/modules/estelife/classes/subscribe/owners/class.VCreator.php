<?php
namespace subscribe\owners;
use core\database\VDatabase;
use core\types\VString;
use subscribe\aggregators\VOwner;
use subscribe\exceptions as errors;
use \subscribe\aggregators\VAggregator;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 07.02.14
 */
class VCreator {
	/**
	 * Получаем пользователя по email
	 * @param $sEmail
	 * @return VOwner
	 * @throws \subscribe\exceptions\VOwnerCreatorEx
	 */
	public static function getByEmail($sEmail){
		$obQuery=VDatabase::driver()
			->createQuery();
		$obQuery->builder()
			->from('estelife_subscribe_owners')
			->filter()
			->_eq('active',1)
			->_eq('email', $sEmail);

		$arUser=$obQuery
			->select()
			->assoc();

		if(empty($arUser)){
			return self::createFromEmail($sEmail);
		}else{
			return new VOwner(
				$arUser['user_id'],
				$arUser['email']
			);
		}
	}

	/**
	 * Получаем пользователя по ID
	 * @param $nUserId
	 * @return VOwner
	 * @throws \subscribe\exceptions\VOwnerCreatorEx
	 */
	public static function getById($nUserId){
		$obQuery=VDatabase::driver()
			->createQuery();

		$obQuery->builder()
			->from('estelife_subscribe_owners')
			->filter()
			->_eq('active',1)
			->_eq('user_id', $nUserId);

		$arUser = $obQuery
			->select()
			->assoc();

		if(empty($arUser)){
			return self::createFromUserId($nUserId);
		}else{
			return new VOwner(
				$arUser['user_id'],
				$arUser['email']
			);
		}
	}

	/**
	 * Получаем список пользователей
	 * @param $nDateSend
	 * @param \subscribe\aggregators\VAggregator $obAggregator
	 * @throws \subscribe\exceptions\VOwnerCreatorEx
	 * @return array
	 */
	public static function getByDateSend($nDateSend, VAggregator $obAggregator=null){
		$nDateSend=intval($nDateSend);

		if($nDateSend<=0)
			throw new errors\VOwnerCreatorEx('empty date send');

		$obQuery=VDatabase::driver()
			->createQuery();

		$obQuery->builder()
			->from('estelife_subscribe_owners')
			->filter()
			->_lte('date_send',date('Y-m-d H:i:s'))
			->_eq('active',1);

		$arOwners=$obQuery
			->select()
			->all();

		if(!empty($arOwners)){
			$arTemp=array();

			foreach($arOwners as $arOwner){
				$obOwner=new VOwner(
					$arOwner['id'],
					$arOwner['email']
				);

				if($obAggregator)
					$obAggregator->aggregateItem($obOwner);

				$arTemp[]=$obOwner;
			}

			$arOwners=$arTemp;
			unset($arTemp);
		}

		return $arOwners;
	}

	/**
	 * Создает пользователя подписки по мылу
	 * @param $sEmail
	 * @return VOwner
	 * @throws \subscribe\exceptions\VOwnerCreatorEx
	 */
	protected function createFromEmail($sEmail){
		$sEmail=addslashes($sEmail);

		if(!VString::isEmail($sEmail))
			throw new errors\VOwnerCreatorEx('invalid user email');

		$obQuery=VDatabase::driver()
			->createQuery();
		$obQuery->builder()
			->from('b_user')
			->field('ID')
			->filter()
			->_eq('EMAIL',$sEmail);
		$arUser=$obQuery
			->select()
			->assoc();
		$nUserId=!empty($arUser) ?
			$arUser['ID'] : 0;

		$nTime=time();
		$obQuery=VDatabase::driver()
			->createQuery();

		$obQuery->builder()
			->from('estelife_subscribe_user')
			->value('email', $sEmail)
			->value('user_id', $nUserId)
			->value('active', 0)
			->value('date_send', $nTime);
		$nOwnerId=$obQuery
			->insert()
			->insertId();

		CEvent::Send(
			"SEND_SUBSCRIBE_EMAIL","s1",
			array(
				'EMAIL_TO'=>$sEmail,
				'LINK'=>md5($nOwnerId.$sEmail.$nTime),
			),
			"Y", 59
		);
		return new VOwner($nOwnerId,$sEmail);
	}

	/**
	 * Создает пользователя подписик по ид реального пользователя
	 * @param $nUserId
	 * @return VOwner
	 * @throws \subscribe\exceptions\VOwnerCreatorEx
	 */
	protected function createFromUserId($nUserId){
		$nUserId=intval($nUserId);

		if($nUserId<=0)
			throw new errors\VOwnerCreatorEx('invalid user id');

		$obQuery=VDatabase::driver()
			->createQuery();
		$obQuery->builder()
			->from('b_user')
			->field('EMAIL')
			->filter()
			->_eq('ID',$nUserId);
		$arUser=$obQuery
			->select()
			->assoc();

		if(empty($arUser) || !VString::isEmail($arUser['EMAIL']))
			throw new errors\VOwnerCreatorEx('invalid user record');

		$nTime=time();
		$obQuery=VDatabase::driver()
			->createQuery();

		$obQuery->builder()
			->from('estelife_subscribe_user')
			->value('email', $arUser['EMAIL'])
			->value('user_id', $nUserId)
			->value('active', 0)
			->value('date_send', $nTime);
		$nOwnerId=$obQuery
			->insert()
			->insertId();

		CEvent::Send(
			"SEND_SUBSCRIBE_EMAIL","s1",
			array(
				'EMAIL_TO'=>$arUser['EMAIL'],
				'LINK'=>md5($nOwnerId.$arUser['EMAIL'].$nTime),
			),
			"Y", 59
		);
		return new VOwner($nOwnerId,$arUser['EMAIL']);
	}
}