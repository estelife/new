<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Обучения");
?>

<?php
$APPLICATION->IncludeComponent("estelife:trainings", "", array(
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/trainings/",
		"SEF_URL_TEMPLATES" => array(

		)
	)
);
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>