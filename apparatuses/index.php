<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Аппараты");
?>
<?php
$APPLICATION->IncludeComponent("estelife:apparatuses", "", array(
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/apparatuses/",
		"SEF_URL_TEMPLATES" => array(

		)
	)
);
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>