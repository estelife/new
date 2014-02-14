<?php
use core\exceptions\VException;
use core\types\VString;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("EsteLife.RU - информационный портал о косметологии и пластической хирургии");
$APPLICATION->SetPageProperty("description", "EsteLife.RU - информационный портал о косметологии и пластической хирургии");
$APPLICATION->SetPageProperty("keywords", "косметология, пластическая хирургия");
$APPLICATION->SetPageProperty("title", "EsteLife.RU - информационный портал о косметологии и пластической хирургии");

CModule::IncludeModule('estelife');

try{
	$obGet=new \core\types\VArray($_GET);
	$sEmail=$obGet->one('email','');
	$sHash=$obGet->one('hash','');

	if(!VString::isEmail($sEmail))
		throw new VException('Не верный email');

	if(empty($sHash))
		throw new VException('Пустой хэш');

	$obQuery = \core\database\VDatabase::driver()
		->createQuery();

	$obQuery->builder()
		->from('estelife_subscribe_events','event')
		->field('event.id','id')
		->field('event.owner_id','owner_id')
		->field('owner.active','owner_active')
		->filter()
		->_eq('event.active',0)
		->_eq(
			$obQuery->builder()->_md5(
				'event.id',
				'event.type',
				'owner.id',
				'owner.email',
				'itsthesalt'
			),
			$sHash
		)
		->_eq('owner.email',$sEmail);

	$obQuery->builder()->join()
		->_left()
		->_from('event','owner_id')
		->_to('estelife_subscribe_owners','id','owner');

	$arEvent = $obQuery
		->select()
		->assoc();

	if(empty($arEvent))
		throw new VException('Подписка не найдена');

	$obQuery->builder()
		->from('estelife_subscribe_events')
		->value('active',1)
		->filter()
		->_eq('id',$arEvent['id']);
	$obQuery->update();

	if(empty($arEvent['owner_active'])){
		$obQuery->builder()
			->from('estelife_subscribe_owners')
			->value('active',1)
			->filter()
			->_eq('id',$arEvent['owner_id']);
		$obQuery->update();
	}

	\notice\VNotice::registerSuccess('Подписка успешно подтверждена');
}catch(VException $e){
	\notice\VNotice::registerError('Подтверждение подписки не удалось');
}

LocalRedirect('/');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");