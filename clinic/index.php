<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Клиники");
?>
<?php
$APPLICATION->IncludeComponent("estelife:clinics", "", array(
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/clinic/",
		"SEF_URL_TEMPLATES" => array(

		)
	)
);
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>