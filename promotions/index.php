<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Акции");
?>

<?php
$APPLICATION->IncludeComponent("estelife:promotions", "", array(
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/promotions/",
		"SEF_URL_TEMPLATES" => array(

		)
	)
);
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>