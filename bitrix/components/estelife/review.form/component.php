<?php
use core\exceptions\VException;
use core\types\VString;

if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)
	die();

global $USER;

$obQuery = \core\database\VDatabase::driver()->createQuery();
$nClinicId = isset($arParams['clinic_id']) ? intval($arParams['clinic_id']) : 0;

$obQuery->builder()
	->field('id')
	->field('title', 'name')
	->from('estelife_clinic_problems')
	->filter();

$arProblems = $obQuery->select()->all();
$arResult['problems'] = $arProblems;

if (!empty($arProblems)) {
	$arTemp = array();

	foreach($arProblems as $arProblem)
		$arTemp[$arProblem['id']] = $arProblem;

	$arProblems = $arTemp;
}

if ($nClinicId) {
	$obJoin = $obQuery->builder()
		->from('estelife_professionals_clinics', 'pf_link')
		->field('pf.id', 'id')
		->field('us.NAME', 'name')
		->field('us.LAST_NAME', 'last_name')
		->field('us.SECOND_NAME', 'second_name')
		->join();
	$obJoin->_left()
		->_from('pf_link', 'professional_id')
		->_to('estelife_professionals', 'id', 'pf');
	$obJoin->_left()
		->_from('pf', 'user_id')
		->_to('user', 'ID', 'us');
	$obQuery->builder()
		->filter()
		->_eq('clinic_id', $nClinicId);
	$arTemp = $obQuery->select()->all();
	$arProfessionals = array();

	if (!empty($arTemp)) {
		foreach($arTemp as &$arProf) {
			if (!empty($arProf['last_name']))
				$arProf['name'] = trim($arProf['last_name'] . ' '.$arProf['name'] . ' '.$arProf['second_name']);

			$arProfessionals[$arProf['id']] = $arProf['name'];
		}
	}

	$arResult['specialists'] = $arTemp;
}

$arResult['user_id'] = '';
$arResult['user_name'] = '';
$arResult['user_email'] = '';
$arResult['user_last_name'] = '';
$arResult['user_phone'] = '';
$arResult['rating_doctor'] = 0;
$arResult['rating_stuff'] = 0;
$arResult['rating_service'] = 0;
$arResult['rating_quality'] = 0;
$arResult['rating_text'] = array('никак', 'ужасно', 'плохо', 'удовл.', 'хор.', 'оч.хор.');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	try {
		$obPost = new \core\types\VArray($_POST);
		$obError = new \core\exceptions\VFormException();

		// Тянем из поста данные
		$nClinicId = intval($obPost->one('clinic_id', 0));

		$nUserId = intval($obPost->one('user_id', 0));
		$sUserName = VString::secure($obPost->one('user_name', ''));
		$sUserLastName = VString::secure($obPost->one('user_last_name', ''));
		$sUserEmail = VString::secure($obPost->one('user_email', ''));
		$sUserPhone = VString::secure($obPost->one('user_phone', ''));
		$sDateVisit = VString::secure($obPost->one('date_visit', ''));

		$nProblemId = intval($obPost->one('problem_id', 0));
		$sProblemName = VString::secure($obPost->one('problem_name', ''));

		$nSpecialistId = intval($obPost->one('specialist_id', 0));
		$sSpecialistName = VString::secure($obPost->one('specialist_name', ''));

		$nRatingDoctor = intval($obPost->one('rating_doctor', 0));
		$nRatingStuff = intval($obPost->one('rating_stuff', 0));
		$nRatingService = intval($obPost->one('rating_service', 0));
		$nRatingQuality = intval($obPost->one('rating_quality', 0));

		$sPositive = VString::secure($obPost->one('positive', ''));
		$sNegative = VString::secure($obPost->one('negative', ''));

		$nRecommend = intval($obPost->one('recommend', 0));
		$nReadTerm = intval($obPost->one('read_term', 0));

		// Реализуем ряд проверочек
		if (!$nClinicId)
			throw new VException('Системная ошибка: не удалось идентифицировать клинику.');

		$obQuery->builder()
			->from('estelife_clinics')
			->filter()
			->_eq('id', $nClinicId);

		if (!$obQuery->count())
			throw new VException('Системная ошибка: не удалось идентифицировать клинику.');

		if (!$nUserId) {
			if ($sUserName == '')
				$obError->setFieldError('Необходимо указать Имя.', 'user_name');

			if (!VString::isEmail($sUserEmail))
				$obError->setFieldError('E-mail указан неверно или уже существует.', 'user_email');
			else {
				$obCheckLogin = CUser::GetList($b, $o, array("=EMAIL" => $sUserEmail));

				if($obCheckLogin->Fetch())
					$obError->setFieldError('E-mail указан неверно или уже существует.','user_email');
			}
		} else {
			$obQuery->builder()
				->from('user')
				->filter()
				->_eq('id', $nUserId);

			if (!$obQuery->count())
				throw new VException('Не удалось идентифицировать пользователя');
		}

		if (!preg_match('/^([0-9]{2}\.){2}[0-9]{2,4}$/', $sDateVisit))
			$obError->setFieldError('Не корректно указана дата посещения', 'date_visit');

		if (strlen($sUserPhone)<10)
			$obError->setFieldError('Не корректно указан номер телефона', 'user_phone');

		if (!$nProblemId && $sProblemName == '') {
			$obError->setFieldError('Необходимо указать проблему или услугу обращения в клинику', 'problem_id');
		} else if ($nProblemId) {
			if (!isset($arProblems[$nProblemId])) {
				if ($sProblemName == '')
					$obError->setFieldError('Не удалось опознать указанную проблему или услугу', 'problem_id');
				else
					$nProblemId = 0;
			}
		}

		if (!$nSpecialistId && $sSpecialistName == '')
			$obError->setFieldError('Необхоидимо указать специалиста, с которым общались', 'specialist_id');
		else if ($nSpecialistId) {
			$obQuery->builder()
				->from('estelife_professionals_clinics')
				->filter()
				->_eq('clinic_id', $nClinicId)
				->_eq('professional_id', $nSpecialistId);

			if (!$obQuery->count()) {
				if ($sSpecialistName == '')
					$obError->setFieldError('Указан специалист, который не является сотрудником клиники', 'specialist_id');
				else
					$nSpecialistId = 0;
			}
		}

		if ($sPositive == '')
			$obError->setFieldError('Укажите, что понравилось при обращении в клинику', 'positive');

		if ($sNegative == '')
			$obError->setFieldError('Укажите, что не понравилось при обращении в клинику', 'negative');

		if (!$nRecommend)
			$obError->setFieldError('Необходимо указать, рекомендуете ли Вы клинику', 'recommend');

		if (!$nReadTerm)
			$obError->setFieldError('Необходимо ознакомиться с правилами размещения отзывов', 'read_term');

		if (!$nRatingQuality)
			$obError->setFieldError('Необхоидмо поставить оценки по всем харрактеристикам', 'rating_quality');

		if (!$nRatingService)
			$obError->setFieldError('Необхоидмо поставить оценки по всем харрактеристикам', 'rating_service');

		if (!$nRatingStuff)
			$obError->setFieldError('Необхоидмо поставить оценки по всем харрактеристикам', 'rating_stuff');

		if (!$nRatingDoctor)
			$obError->setFieldError('Необхоидмо поставить оценки по всем харрактеристикам', 'rating_doctor');

		$obError->raise();

		$sDateVisit = date('Y-m-d H:i:s', strtotime($sDateVisit));

		if (!$nUserId) {
			$sPassword = mb_substr(md5(time()), 0, 6);

			$arUser = array(
				'NAME' => $sUserName,
				'LAST_NAME' => $sUserLastName,
				'EMAIL' => $sUserEmail,
				'LOGIN' => $sUserEmail,
				'PASSWORD' => $sPassword,
				'PERSONAL_PHONE' => $sUserPhone,
				'CONFIRM_PASSWORD' => $sPassword,
				'CHECKWORD' => randString(8),
				'~CHECKWORD_TIME' => $DB->CurrentTimeFunction(),
				'ACTIVE' => 'N',
				'CONFIRM_CODE' => randString(8),
				'LID' => SITE_ID,
				'USER_IP' => $_SERVER["REMOTE_ADDR"],
				'USER_HOST' => @gethostbyaddr($_SERVER["REMOTE_ADDR"])
			);

			if (($sDefGroup = COption::GetOptionString("main", "new_user_registration_def_group", "")) != '')
				$arUser["GROUP_ID"] = explode(",", $nDefGroup);

			$nUserId = $USER->Add($arUser);

			if($nUserId > 0){
				$arUser["USER_ID"] = $nUserId;
				CEvent::Send("NEW_REVIEW_USER", SITE_ID, $arUser);
			}else
				throw new VException($user->LAST_ERROR);
		}

		$obQuery->builder()
			->from('estelife_clinic_reviews')
			->value('active', 1)
			->value('user_id', $nUserId)
			->value('clinic_id', $nClinicId)
			->value('date_visit', $sDateVisit)
			->value('date_add', date('Y-m-d H:i:s'))
			->value('problem_id', $nProblemId)
			->value('problem_name', $sProblemName)
			->value('specialist_id', $nSpecialistId)
			->value('specialist_name', $sSpecialistName)
			->value('positive_description', $sPositive)
			->value('negative_description', $sNegative)
			->value('is_recomended', $nRecommend);

		$obResult = $obQuery->insert();
		$nReviewId = $obResult->insertId();

		if (!$nReviewId)
			throw new VException('Системная ошибка: сохранение отзыва не удалось');

		$obQuery->builder()
			->from('estelife_clinic_user_rating')
			->value('review_id', $nReviewId)
			->value('rating_doctor', $nRatingDoctor)
			->value('rating_stuff', $nRatingStuff)
			->value('rating_service', $nRatingService)
			->value('rating_quality', $nRatingQuality);

		$obQuery->insert();
		$sNotice = 'Отзыв успешно добавлен. После подтверждения модератором он будет виден остальным пользователям.';

		if (\core\http\VHttp::isAjaxRequest()) {
			$arResult['complete'] = array(
				'clinic_id' => $nClinicId,
				'review_id' => $nReviewId,
				'text' => $sNotice
			);
		} else {
			\notice\VNotice::registerSuccess('', $sNotice);
			LocalRedirect('/cl'.$nClinicId.'/?review_list');
		}
	} catch(\core\exceptions\VFormException $e) {
		$arResult['errors'] = $e->getFieldErrors();
		$arResult = array_merge($arResult, $_POST);
	} catch(VException $e) {
		$arResult['error'] = array(
			'message' => $e->getMessage(),
			'code' => $e->getCode()
		);
		$arResult = array_merge($arResult, $_POST);
	}
} else {
	$arResult['clinic_id'] = $nClinicId;

	if ($USER->IsAuthorized()) {
		$arResult['user_id'] = $USER->GetID();
		$arResult['user_name'] = $USER->GetFirstName();
		$arResult['user_email'] = $USER->GetEmail();
		$arResult['user_last_name'] = $USER->GetLastName();
	}
}

$this->IncludeComponentTemplate();