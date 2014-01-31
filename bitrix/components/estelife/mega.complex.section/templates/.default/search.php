<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?$APPLICATION->SetTitle("Поиск");?>
<div class="content">
	<?$APPLICATION->IncludeComponent(
		"estelife:search.page",
		"",
		Array()
	);?>

</div>