<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->IncludeComponent("estelife:mega.complex.personal", "", array(
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/personal/",
		"SEF_URL_TEMPLATES" => array(

		)
	)
);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");