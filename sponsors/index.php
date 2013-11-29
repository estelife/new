<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Организаторы");
?>
<?php
$APPLICATION->IncludeComponent("estelife:sponsors", "", array(
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/sponsors/",
		"SEF_URL_TEMPLATES" => array(

		)
	)
);
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>