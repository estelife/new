<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();
throw new core\exceptions\VHttpEx('Page not found', 404);
?>
<div class="content">
<?php
	global $APPLICATION;
	$APPLICATION->IncludeComponent(
		"estelife:pro.detail",
		"",
		array(
			'IBLOCK_ID'=>37,
			'PATH' => $arResult['VARIABLES']['PATH']
		),
		false
	);
?>
</div>