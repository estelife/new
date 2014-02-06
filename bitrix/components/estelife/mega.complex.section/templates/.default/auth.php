<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?$APPLICATION->SetTitle("Авторизация");?>
<div class="content">
	<?$APPLICATION->IncludeComponent("estelife:system.auth.form","",Array(
			"REGISTER_URL" => "register.php",
			"FORGOT_PASSWORD_URL" => "",
			"PROFILE_URL" => "profile.php",
			"SHOW_ERRORS" => "Y"
		)
	);?>

</div>