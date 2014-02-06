<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

global $USER;
CModule::IncludeModule('estelife');
use core\exceptions\VFormException;
use core\types\VArray;

$arCity=\geo\VGeo::getInstance()->getGeo();
if($arCity){
	$arResult['city_name']=$arCity['NAME'];
	$arResult['city_id']=$arCity['ID'];
}

if($USER->IsAuthorized()){
	$arResult['user_name']=$USER->GetFullName();
	$arResult['user_id']=$USER->GetID();
	$arResult['user_email']=$USER->GetEmail();
}


if($_SERVER['REQUEST_METHOD']=='POST'){
	try{
		$obCityQuery=\core\database\VDatabase::driver()->createQuery();
		$obCityQuery->builder()->from('iblock_element');

		$obException=new VFormException();
		$obPost=new VArray($_POST);
		$sUserName=$obPost['user_name'];
		$sEmail=$obPost['user_email'];
		$sPhoneCode=$obPost['phone_code'];
		$sPhoneNumber=$obPost['phone_number'];
		$sCompanyName=$obPost['company_name'];
		$sCityName=$obPost['city_name'];
		$nCompanyId=intval($obPost['company_id']);
		$nCityId=intval($obPost['city_id']);
		$nUserId=0;

		if(empty($sUserName))
			$obException->setFieldError(
				'Укажите Ваше имя',
				'user_name'
			);

		if(empty($sEmail) || !\core\types\VString::isEmail($sEmail))
			$obException->setFieldError(
				'Укажите правильный email',
				'user_email'
			);

		if(empty($sPhoneCode) || empty($sPhoneNumber))
			$obException->setFieldError(
				'Укажите правильный телефон',
				'phone_code'
			);

		if(empty($sCompanyName) && empty($nCompanyId))
			$obException->setFieldError(
				'Укажите название компании',
				'company_name'
			);

		if(empty($sCityName) && empty($nCityId))
			$obException->setFieldError('Укажите город','city_name');
		else {
			$obFilter=$obCityQuery->builder()->filter();
			$obFilter->_eq('IBLOCK_ID',16);
			$obFilter->_or()->_eq('ID',$nCityId);
			$obFilter->_or()->_eq('NAME',$sCityName);
			$obResult=$obCityQuery->select();

			if($obResult->count()==0)
				$obException->setFieldError(
					'Указанный город не найден в базе портала',
					'city_name'
				);

			$arCity=$obResult->assoc();
			$nCityId=$arCity['ID'];
		}

		$obException->raise();

		$sPhoneNumber='+7('.$sPhoneCode.')'.$sPhoneNumber;
		$obRequest=new \request\VRequest(
			new \request\VUser(
				$sUserName,
				$sEmail,
				$sPhoneNumber,
				$nUserId
			),
			new \request\VCompany(
				$nCityId,
				\request\VCompany::CLINIC,
				$sCompanyName,
				$nCompanyId
			)
		);


			$arFields = array(
				'EMAIL_TO'=>$sEmail,
			);

			CEvent::Send("GET_REQUEST_ADMIN", "s1", $arFields);
			CEvent::Send("GET_REQUEST_USER", "s1", $arFields);


		if(!$obRequest->create())
			throw new \request\exceptions\VRequest('Не удалось создать заявку');

		$arResult['step']=3;
	}catch(VFormException $e) {
		$arResult['error']=$e->getFieldErrors();
	}catch(VException $e){
		$arResult['error']=array(
			'message'=>$e->getMessage(),
			'code'=>$e->getCode()
		);
	}
}

$this->IncludeComponentTemplate();