<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?$APPLICATION->SetTitle("Восстановление пароля");?>
<div class="content">
	<?$APPLICATION->IncludeComponent("estelife:forgotpswd",
		"",
		Array()
	);?>

</div>