<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
?>
<?php
$APPLICATION->IncludeComponent("estelife:mega.complex.section", "", array(
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/",
		"SEF_URL_TEMPLATES" => array(

		)
	)
);
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>