<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?$APPLICATION->SetTitle("Регистрация");?>
<div class="content">
	<?$APPLICATION->IncludeComponent("estelife:register","",Array(
			"USER_PROPERTY_NAME" => "",
			"SEF_MODE" => "Y",
			"SHOW_FIELDS" => Array("NAME", "SECOND_NAME", "LAST_NAME", "PERSONAL_MOBILE", "PERSONAL_NOTES"),
			"REQUIRED_FIELDS" => Array(),
			"AUTH" => "Y",
			"USE_BACKURL" => "Y",
			"SUCCESS_PAGE" => "",
			"SET_TITLE" => "Y",
			"USER_PROPERTY" => Array(),
			"SEF_FOLDER" => "/",
			"VARIABLE_ALIASES" => Array()
		)
	);?>

</div>