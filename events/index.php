<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("События");
?>

<?php
$APPLICATION->IncludeComponent("estelife:events", "", array(
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/events/",
		"SEF_URL_TEMPLATES" => array(

		)
	)
);
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>