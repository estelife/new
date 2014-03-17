<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
?>
<div class="content">
	<?php
	$APPLICATION->IncludeComponent(
		"estelife:events.program",
		"",
		array(
			"EVENT_ID" => 321,
		),
		false
	);
	?>
</div>
<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");