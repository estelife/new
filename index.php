 <?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("EsteLife.RU - информационный портал о косметологии и пластической хирургии");
$APPLICATION->SetPageProperty("description", "EsteLife.RU - информационный портал о косметологии и пластической хирургии");
$APPLICATION->SetPageProperty("keywords", "косметология, пластическая хирургия");
$APPLICATION->SetPageProperty("title", "EsteLife.RU - информационный портал о косметологии и пластической хирургии");
?>
<div class="content">
	<div style="text-align: center; color:#E50364; margin-top:40px;"><h1>Сайт находится на стадии разработки</h1></div>
	<div class="adv bottom b2">
		<?$APPLICATION->IncludeComponent("bitrix:advertising.banner","",Array(
				"TYPE" => "main_center_2",
				"CACHE_TYPE" => "A",
				"NOINDEX" => "N",
				"CACHE_TIME" => "3600"
			)
		);?>
	</div>
	<div class="adv bottom b3">
		<?$APPLICATION->IncludeComponent("bitrix:advertising.banner","",Array(
				"TYPE" => "main_center_3",
				"CACHE_TYPE" => "A",
				"NOINDEX" => "N",
				"CACHE_TIME" => "3600"
			)
		);?>
	</div>
	<div class="adv bottom b4">
		<?$APPLICATION->IncludeComponent("bitrix:advertising.banner","",Array(
				"TYPE" => "main_center_4",
				"CACHE_TYPE" => "A",
				"NOINDEX" => "N",
				"CACHE_TIME" => "3600"
			)
		);?>
	</div>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>