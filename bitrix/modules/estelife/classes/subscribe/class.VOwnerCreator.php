<?php
namespace subscribe;
use core\database\VDatabase;
use subscribe\aggregators\VAggregator;
use subscribe\errors as errors;
use core\types\VString;

/**
 * Описание класса не задано. Обратитесь с вопросом на почту разработчика.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 06.02.14
 */
class VUserCreator {
	/**
	 * Получение пользователя по email
	 * @param $sEmail
	 * @return VOwner
	 * @throws errors\VOwnerEx
	 */
	public static function getByEmail($sEmail){
		if(!VString::isEmail($sEmail))
			throw new errors\VOwnerEx('invalid email for owner');

		$obQuery=VDatabase::driver()
			->createQuery();
		$obQuery->builder()
			->from('estelife_subscribe_user')
			->filter()
			->_eq('email',$sEmail);
		$arUser = $obQuery
			->select()
			->assoc();

		if(empty($arUser))
			throw new errors\VOwnerEx('user for this email not found');

		return new VOwner(
			$arUser['user_id'],
			$arUser['email']
		);
	}

	/**
	 * Получение пользователя по id
	 * @param $nUserId
	 * @return VOwner
	 * @throws errors\VOwnerEx
	 */
	public static function getById($nUserId){
		$nUserId=intval($nUserId);

		if(empty($nUserId))
			throw new errors\VOwnerEx('invalid user id');

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

		if(empty($arUser))
			throw new errors\VOwnerEx('user for this id not found');

		return new VOwner(
			$arUser['user_id'],
			$arUser['email']
		);
	}

	/**
	 * Получение пользователя по дате. Если задан аггрегатор, то весь для каждого найденного
	 * пользователя будет вызван метод VArggregator::nextItem
	 * @param VAggregator $obAggregator
	 * @return array
	 * @throws errors\VOwnerEx
	 * @see subscribe\aggregators\VOwner::nextItem
	 */
	public static function getByDate(VAggregator $obAggregator=null){
		$obQuery=VDatabase::driver()
			->createQuery();
		$obQuery->builder()
			->from('estelife_subscribe_user')
			->filter()
			->_eq('active',1);
		$arOwners = $obQuery
			->select()
			->all();

		if(empty($arOwners))
			throw new errors\VOwnerEx('owners not found');

		$arTemp=array();

		foreach($arOwners as $arOwner){
			$obOwner=new VOwner(
				$arOwner['id'],
				$arOwner['email']
			);

			if($obAggregator)
				$obAggregator->nextItem($obOwner);

			$arTemp[]=$obOwner;
		}

		return $arTemp;
	}
}