<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$APPLICATION->IncludeComponent("estelife:mega.complex.section", "rest", array(
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/rest/",
		"SEF_URL_TEMPLATES" => array(

		),
		'DIRECTORIES'=>array(
			'clinics_filter',
			'promotions_filter',
			'preparations_makers_filter',
			'apparatuses_makers_filter',
			'preparations_filter',
			'implants_filter',
			'threads_filter',
			'apparatuses_filter',
			'events_filter',
			'sponsors_filter',
			'training_centers_filter',
			'trainings_filter',
			'professionals_filter',
			'home',
			'search',
			'podcast',
			'articles',
			'banner'
		)
	)
);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");