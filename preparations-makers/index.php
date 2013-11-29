<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Препараты");
?>
<?php
$APPLICATION->IncludeComponent("estelife:preparations_makers", "", array(
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/preparations-makers/",
		"SEF_URL_TEMPLATES" => array(

		)
	)
);
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>