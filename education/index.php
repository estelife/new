<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetPageProperty("description", "Практический телемост по ботулинотерапии");
$APPLICATION->SetPageProperty("keywords", "Практический телемост по ботулинотерапии");
$APPLICATION->SetPageProperty("title", "Практический телемост по ботулинотерапии");
?>
<div class="content">
	<ul class="crumb">
		<li><a href="/">Главная</a></li>
		<li><a href="/about/">О проекте</a></li>
		<li><b>Практический телемост по ботулинотерапии</b></li>
	</ul>
	<?$APPLICATION->IncludeComponent("estelife:education",
		"",
		Array()
	);?>
</div>
<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");