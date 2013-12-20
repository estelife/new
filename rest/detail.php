<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$APPLICATION->IncludeComponent("estelife:mega.complex.detail", "", array(
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/rest/",
		"SEF_URL_TEMPLATES" => array(

		)
	)
);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");