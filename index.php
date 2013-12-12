 <?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("EsteLife.RU - информационный портал о косметологии и пластической хирургии");
$APPLICATION->SetPageProperty("description", "EsteLife.RU - информационный портал о косметологии и пластической хирургии");
$APPLICATION->SetPageProperty("keywords", "косметология, пластическая хирургия");
$APPLICATION->SetPageProperty("title", "EsteLife.RU - информационный портал о косметологии и пластической хирургии");
?>
<div class="content">
	<?$APPLICATION->IncludeComponent(
		"bitrix:news.list",
		"kp",
		Array(
			"DISPLAY_DATE" => "Y",
			"DISPLAY_NAME" => "Y",
			"DISPLAY_PICTURE" => "Y",
			"DISPLAY_PREVIEW_TEXT" => "Y",
			"AJAX_MODE" => "N",
			"IBLOCK_TYPE" => "news",
			"IBLOCK_ID" => "14",
			"NEWS_COUNT" => "6",
			"SORT_BY1" => "ACTIVE_FROM",
			"SORT_ORDER1" => "DESC",
			"SORT_BY2" => "SORT",
			"SORT_ORDER2" => "ASC",
			"FILTER_NAME" => "",
			"FIELD_CODE" => array("ID", "CODE", "NAME", "TAGS", "SORT", "PREVIEW_TEXT", "PREVIEW_PICTURE", "IBLOCK_TYPE_ID", "IBLOCK_ID", "IBLOCK_CODE", "IBLOCK_NAME", "IBLOCK_EXTERNAL_ID", "DATE_CREATE"),
			"PROPERTY_CODE" => array("FORUM_MESSAGE_CNT"),
			"CHECK_DATES" => "Y",
			"DETAIL_URL" => "/ar#ELEMENT_ID#/",
			"PREVIEW_TRUNCATE_LEN" => "0",
			"ACTIVE_DATE_FORMAT" => "j F Y",
			"SET_TITLE" => "N",
			"SET_STATUS_404" => "N",
			"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
			"ADD_SECTIONS_CHAIN" => "Y",
			"HIDE_LINK_WHEN_NO_DETAIL" => "N",
			"PARENT_SECTION" => 209,
			"PARENT_SECTION_CODE" => '',
			"INCLUDE_SUBSECTIONS" => "N",
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "3600",
			"CACHE_FILTER" => "N",
			"CACHE_GROUPS" => "Y",
			"DISPLAY_TOP_PAGER" => "N",
			"DISPLAY_BOTTOM_PAGER" => "N",
			"PAGER_TITLE" => "Новости",
			"PAGER_SHOW_ALWAYS" => "N",
			"PAGER_TEMPLATE" => "",
			"PAGER_DESC_NUMBERING" => "N",
			"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
			"PAGER_SHOW_ALL" => "N",
			"AJAX_OPTION_JUMP" => "N",
			"AJAX_OPTION_STYLE" => "Y",
			"AJAX_OPTION_HISTORY" => "N"
		)
	);
	?>

	<div class="adv adv-out right">
		<?$APPLICATION->IncludeComponent("bitrix:advertising.banner","",Array(
				"TYPE" => "main_right",
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
			"NEWS_COUNT" => 3,
			"MAIN_URL" => "",
			"TITLE"=>"Экспертное мнение",
			"MORE_TITLE"=>"",
			"IMG" => 176,
			"AUTOR"=> 174,
			"PROFESSION" => 175,
			"PREFIX" => ""
		)
	)?>
	<?php
		$APPLICATION->IncludeComponent(
			"estelife:promotions.list",
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
			"IBLOCK_ID"=>14,
			"SECTIONS_ID"=> array(194,195,196,197),
			"SECTIONS_NAME"=> array("Красивое лицо", "Идеальное тело", "Прекрасные ножки", "Изящные ручки"),
			"NEWS_COUNT" => 4,
			"NEED_SECTION" => "N",
			"MAIN_URL" => "articles",
			"TITLE"=>"Советы о красоте",
			"MORE_TITLE"=>"Больше советов о красоте",
			"IMG_FIELD" => 151,
			"PREFIX" => "ar"
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
			"IBLOCK_ID"=>3,
			"SECTIONS_ID"=> array(172,173,176,177),
			"SECTIONS_NAME"=> array("Косметология", "Пластическая хирургия", "Косметика", "Обо всем"),
			"NEWS_COUNT" => 4,
			"NEED_SECTION" => "N",
			"MAIN_URL" => "novosti",
			"TITLE"=>"Новости сферы",
			"MORE_TITLE"=>"Архив новостей",
			"IMG_FIELD" =>145,
			"PREFIX" => "ns"
		)
	)?>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>