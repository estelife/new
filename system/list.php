<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->IncludeComponent("estelife:mega.complex.section", "", array(
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/",
		"SEF_URL_TEMPLATES" => array(

		),
		'DIRECTORIES'=>array(
			'pro'
		)
	)
);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");