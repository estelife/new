 <?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("EsteLife.RU - информационный портал о косметологии и пластической хирургии");
$APPLICATION->SetPageProperty("description", "EsteLife.RU - информационный портал о косметологии и пластической хирургии");
$APPLICATION->SetPageProperty("keywords", "косметология, пластическая хирургия");
$APPLICATION->SetPageProperty("title", "EsteLife.RU - информационный портал о косметологии и пластической хирургии");
?>
<div class="content">
	<?$APPLICATION->IncludeComponent(
		"estelife:podcast.list",
		"",
		array(
			"IBLOCK_ID"=>36,
			"NEWS_COUNT" => 7,
			"MAIN_URL" => "podcast",
			"PREFIX" => "pt",
		)
	)?>
	<div class="adv adv-out right">
		<?$APPLICATION->IncludeComponent("bitrix:advertising.banner","",Array(
				"TYPE" => "main_right_1",
				"CACHE_TYPE" => "A",
				"NOINDEX" => "N",
				"CACHE_TIME" => "3600"
			)
		);?>
	</div>
	<div class="adv top">
		<?$APPLICATION->IncludeComponent("bitrix:advertising.banner","",Array(
				"TYPE" => "main_center_1",
				"CACHE_TYPE" => "A",
				"NOINDEX" => "N",
				"CACHE_TIME" => "3600"
			)
		);?>
	</div>
	<?$APPLICATION->IncludeComponent(
		"estelife:expert.list",
		"",
		array(
			"IBLOCK_ID"=>35,
			"NEWS_COUNT" => 2,
			"MAIN_URL" => "",
			"TITLE"=>"Экспертное мнение",
			"MORE_TITLE"=>"",
			"IMG" => 174,
			"AUTOR"=> 172,
			"PROFESSION" => 173,
			"PREVIEW"=>178,
			"PREFIX" => ""
		)
	)?>
	<?php
		$APPLICATION->IncludeComponent(
			"estelife:promotions.clinics.main.list",
			"index",
			array(
				"COUNT" => 3
			),
			false
		);
	?>
	<?$APPLICATION->IncludeComponent(
		"estelife:articles.list",
		"",
		array(
			"IBLOCK_ID"=>3,
			"SECTIONS_ID"=> array(172,177,173,176),
			"SECTIONS_NAME"=> array("Косметология","Обо всем","Пластическая хирургия","События"),
			"NEWS_COUNT" => 4,
			"NEED_SECTION" => "N",
			"MAIN_URL" => "novosti",
			"TITLE"=>"Новости сферы",
			"MORE_TITLE"=>"Архив новостей",
			"IMG_FIELD" =>145,
			"PREFIX" => "ns"
		)
	)?>
	<div class="adv bottom">
		<?$APPLICATION->IncludeComponent("bitrix:advertising.banner","",Array(
				"TYPE" => "main_center_2",
				"CACHE_TYPE" => "A",
				"NOINDEX" => "N",
				"CACHE_TIME" => "3600"
			)
		);?>
	</div>
</div>
<?php
$APPLICATION->IncludeComponent(
	"estelife:photogallery",
	"",
	array(
		"COUNT" => 18,
		"ONLY_VIDEO"=>"Y",
		"ONLY_PHOTO"=>"Y",
	),
	false
);
?>
<div class="content">
	<?$APPLICATION->IncludeComponent(
		"estelife:articles.list",
		"",
		array(
			"IBLOCK_ID"=>14,
			"SECTIONS_ID"=> array(212,197,196,195,194),
			"SECTIONS_NAME"=> array("Разное","Прекрасные ножки","Изящные ручки","Идеальное тело","Красивое лицо"),
			"NEWS_COUNT" => 4,
			"NEED_SECTION" => "N",
			"MAIN_URL" => "articles",
			"TITLE"=>"О красоте",
			"MORE_TITLE"=>"Больше советов о красоте",
			"IMG_FIELD" => 151,
			"ANONS_FIELD"=> 175,
			"PREFIX" => "ar"
		)
	)?>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>