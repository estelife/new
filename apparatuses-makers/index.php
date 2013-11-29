<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Аппараты");
?>
<?php
$APPLICATION->IncludeComponent("estelife:apparatuses_makers", "", array(
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/apparatuses-makers/",
		"SEF_URL_TEMPLATES" => array(

		)
	)
);
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>