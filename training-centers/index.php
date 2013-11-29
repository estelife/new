<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Учебные центры");
?>
<?php
$APPLICATION->IncludeComponent("estelife:training_centers", "", array(
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/training-centers/",
		"SEF_URL_TEMPLATES" => array(

		)
	)
);
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>