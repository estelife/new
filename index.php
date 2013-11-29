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
			"DETAIL_URL" => "/articles/#ELEMENT_CODE#/",
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
<div class="experts">
	<h2>Экспертное мнение</h2>
	<div class="item">
		<div class="user">
			<img src="images/content/expert.png" alt="" title="" />
			<b>Саромыцкая Алена Николаевна</b>
			<i>Врач дерматолог косметолог</i>
		</div>
		<div class="quote">
			<h3>Как не утонуть в море советов</h3>
			<p>В мире красоты распространено великое множество «полезных» советов. Попробуем разобраться, какие из них соответствуют действительности, а какие – нет.</p>
		</div>
	</div>
	<ul class="menu">
		<li><a href="#"><i></i></a></li>
		<li class="active"><a href="#"><i></i></a></li>
		<li><a href="#"><i></i></a></li>
	</ul>
</div>
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
		"NEWS_COUNT" => 4
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
<div class="media">
	<div class="content">
		<div class="title">
			<h2>Медиа</h2>
			<a href="#">Смотреть больше</a>
		</div>
		<ul class="menu">
			<li class="first"><a href="#">x</a></li>
			<li><a href="#">Только фото</a></li>
			<li><a href="#">Только видео</a></li>
		</ul>
		<div class="items">
			<div class="item">
				<img src="images/content/photo_preview1.png" alt="" title="" />
				<div class="border"></div>
			</div>
			<div class="item">
				<img src="images/content/photo_preview2.png" alt="" title="" />
				<div class="border"></div>
			</div>
			<div class="item">
				<img src="images/content/photo_preview3.png" alt="" title="" />
				<div class="border"></div>
			</div>
			<div class="item">
				<img src="images/content/photo_preview4.png" alt="" title="" />
				<div class="border"></div>
			</div>
			<div class="item">
				<img src="images/content/photo_preview1.png" alt="" title="" />
				<div class="border"></div>
			</div>
			<div class="item last">
				<img src="images/content/photo_preview2.png" alt="" title="" />
				<div class="border"></div>
			</div>
			<div class="item">
				<img src="images/content/photo_preview3.png" alt="" title="" />
				<div class="border"></div>
			</div>
			<div class="item">
				<img src="images/content/photo_preview4.png" alt="" title="" />
				<div class="border"></div>
			</div>
			<div class="item">
				<img src="images/content/photo_preview1.png" alt="" title="" />
				<div class="border"></div>
			</div>
			<div class="item">
				<img src="images/content/photo_preview2.png" alt="" title="" />
				<div class="border"></div>
			</div>
			<div class="item">
				<img src="images/content/photo_preview3.png" alt="" title="" />
				<div class="border"></div>
			</div>
			<div class="item last">
				<img src="images/content/photo_preview4.png" alt="" title="" />
				<div class="border"></div>
			</div>
			<div class="item video">
				<span></span>
				<img src="images/content/photo_preview1.png" alt="" title="" />
				<div class="border"></div>
			</div>
			<div class="item video">
				<span></span>
				<img src="images/content/photo_preview2.png" alt="" title="" />
				<div class="border"></div>
			</div>
			<div class="item">
				<img src="images/content/photo_preview3.png" alt="" title="" />
				<div class="border"></div>
			</div>
			<div class="item">
				<img src="images/content/photo_preview4.png" alt="" title="" />
				<div class="border"></div>
			</div>
			<div class="item">
				<img src="images/content/photo_preview1.png" alt="" title="" />
				<div class="border"></div>
			</div>
			<div class="item last">
				<img src="images/content/photo_preview2.png" alt="" title="" />
				<div class="border"></div>
			</div>
		</div>
	</div>
</div>
<div class="content">
	<div class="articles">
		<div class="title">
			<h2>Новости сферы</h2>
			<a href="#">Архив новостей</a>
		</div>
		<ul class="menu">
			<li><a href="#"><span>Важное</span></a></li>
			<li class="active"><a href="#"><span>Первый раздел</span></a></li>
			<li><a href="#"><span>Второй раздел</span></a></li>
			<li><a href="#"><span>Третий раздел</span></a></li>
		</ul>
		<div class="items">
			<div class="item">
				<img src="images/content/article1.png" alt="" title="" />
				<h3>«KOSMETIK EXPO Урал»: профессионально о красоте</h3>
				<p>Главная встреча профессионалов «красивого» бизнеса региона – VIII Выставка «KOSMETIK EXPO Урал» состоялась в Екатеринбурге с 13 по 15 ноября.</p>
				<ul class="stat">
					<li class="date">14.11.2013</li>
					<li class="comments">9<i></i></li>
					<li class="likes">41<i></i></li>
					<li class="unlikes">2<i></i></li>
				</ul>
			</div>
			<div class="item">
				<img src="images/content/article2.png" alt="" title="" />
				<h3>Международный Фестиваль красоты «Невские Берега»</h3>
				<p>С 20 по 23 февраля 2014 года при поддержке Правительства Санкт-Петербурга в петербургском СКК пройдет Международный Фестиваль красоты «Невские берега».</p>
				<ul class="stat">
					<li class="date">14.11.2013</li>
					<li class="comments">9<i></i></li>
					<li class="likes">41<i></i></li>
					<li class="unlikes">2<i></i></li>
				</ul>
			</div>
			<div class="item">
				<img src="images/content/article3.png" alt="" title="" />
				<h3>Международная выставка парфюмерии и косметики InterCHARM</h3>
				<p>Вот уже 20 лет InterCHARM доказывает, что по праву считается крупнейшей выставкой индустрии красоты в России, СНГ и Восточной Европы.</p>
				<ul class="stat">
					<li class="date">14.11.2013</li>
					<li class="comments">9<i></i></li>
					<li class="likes">41<i></i></li>
					<li class="unlikes">2<i></i></li>
				</ul>
			</div>
			<div class="item">
				<img src="images/content/article4.png" alt="" title="" />
				<h3>Портал EsteLife – информационный спонсор «KOSMETIK EXPO Урал»</h3>
				<p>С 13 по 15 ноября в Екатеринбурге проходит VIII Выставка для профессионалов индустрии красоты «KOSMETIK EXPO Урал».</p>
				<ul class="stat">
					<li class="date">14.11.2013</li>
					<li class="comments">9<i></i></li>
					<li class="likes">41<i></i></li>
					<li class="unlikes">2<i></i></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>