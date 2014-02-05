<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?$APPLICATION->SetTitle("Авторизация");?>
<div class="content">
	<?$APPLICATION->IncludeComponent("estelife:auth",
		"",
		Array()
	);?>

</div>