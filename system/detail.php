<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->IncludeComponent("estelife:mega.complex.detail", "", array(
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/",
		"SEF_URL_TEMPLATES" => array(

		)
	)
);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");