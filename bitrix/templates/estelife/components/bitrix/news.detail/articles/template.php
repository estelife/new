<?  if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$APPLICATION->SetPageProperty("description",$arResult['PROPERTIES']['DESCRIPTION']['VALUE']);
$APPLICATION->SetPageProperty("keywords",$arResult['PROPERTIES']['KEYWORDS']['VALUE']);
$APPLICATION->SetPageProperty("title", $arResult['PROPERTIES']['BROWSER_TITLE']['VALUE']);
$APPLICATION->SetTitle($arResult['PROPERTIES']['BROWSER_TITLE']['VALUE']);
?>
<div class="content">
	<div class="inner">
		<ul class="crumb">
			<li><a href="/">Главная</a></li>
			<?php if($arResult['LAST_SECTION']['NAME']!=$arResult["NAME"]): ?>
				<li><a href="<?=$arResult['LAST_SECTION']['SECTION_PAGE_URL']?>"><?=$arResult['LAST_SECTION']['NAME']?></a></li>
			<?php endif; ?>
			<li><b><?=$arResult["NAME"]?></b></li>
		</ul>
		<div class="item detail big-font">
			<h1><?=$arResult["NAME"]?></h1>
			<ul class="stat notlike" data-elid="<?=$arResult['LIKES']['element_id']?>" data-type="<?=$arResult['LIKES']['type']?>">
				<?php if (!empty($arResult['ACTIVE_FROM'])):?>
					<li class="date"><?=date('d.m.Y',strtotime($arResult['ACTIVE_FROM']))?></li>
				<?php endif?>
<!--				<li class="comments">9<i></i></li>-->
				<li class="likes islike"><?=$arResult['LIKES']['countLike']?><?if ($arResult['LIKES']['typeLike']==1):?> и Ваш<?endif?><i></i></li>
				<li class="unlikes islike"><?=$arResult['LIKES']['countDislike']?><?if ($arResult['LIKES']['typeLike']==2):?> и Ваш<?endif?><i></i></li>
			</ul>
			<div class="announce">
				<?=$arResult["PREVIEW_TEXT"];?>
			</div>
			<div class="article-img">
				<div class="article-img-in">
					<img src="<?=$arResult['IMG']['SRC']?>" alt="<?=$arResult["NAME"]?>" title="<?=$arResult["NAME"]?>">
				</div>
				<div class="article-img-desc">
					<?php if (!empty($img['DESCRIPTION'])):?>
						<?=$img['DESCRIPTION']?>
					<?php else:?>
						<?=$arResult["NAME"]?>
					<?php endif?>
				</div>
			</div>
			<?=$arResult["DETAIL_TEXT"];?>
			<div class="info">
				<ul class="stat" data-elid="<?=$arResult['LIKES']['element_id']?>" data-type="<?=$arResult['LIKES']['type']?>">
					<li><a href="#" class="likes islike<?if ($arResult['LIKES']['typeLike']==1):?> active<?endif?>" data-help="Нравится"><?=$arResult['LIKES']['countLike']?><?if ($arResult['LIKES']['typeLike']==1):?> и Ваш<?endif?><i></i></a></li>
					<li><a href="#" class="unlikes islike<?if ($arResult['LIKES']['typeLike']==2):?> active<?endif?>" data-help="Не нравится"><?=$arResult['LIKES']['countDislike']?><?if ($arResult['LIKES']['typeLike']==2):?> и Ваш<?endif?><i></i></a></li>
				</ul>
				<div class="social cols repost">
					<span>Поделиться: </span>
					<a href="http://vkontakte.ru/share.php?url=http://estelife.ru/ar<?=$arResult['ID']?>/" target="_blank" class="vk">ВКонтакте</a>
					<a href="https://www.facebook.com/sharer.php?u=http://estelife.ru/ar<?=$arResult['ID']?>/" target="_blank" class="fb">Facebook</a>
				</div>
				<div class="author cols">
					<?php if (!empty($arResult['PROPERTIES']['SOURCE']['VALUE'])):?>
					Автор статьи
					<b><?=$arResult['PROPERTIES']['SOURCE']['VALUE']?></b>
					<?php endif?>
				</div>
			</div>
		</div>
	</div>
	<div class="adv adv-out right">
		<?$APPLICATION->IncludeComponent("bitrix:advertising.banner","",Array(
				"TYPE" => "main_right_1",
				"CACHE_TYPE" => "A",
				"NOINDEX" => "N",
				"CACHE_TIME" => "3600"
			)
		);?>
	</div>
<!--	<div class="adv adv-out right">-->
<!--		--><?//$APPLICATION->IncludeComponent("bitrix:advertising.banner","",Array(
//				"TYPE" => "main_right_2",
//				"CACHE_TYPE" => "A",
//				"NOINDEX" => "N",
//				"CACHE_TIME" => "3600"
//			)
//		);?>
<!--	</div>-->
<!--	<div class="adv top">-->
<!--		--><?//$APPLICATION->IncludeComponent("bitrix:advertising.banner","",Array(
//				"TYPE" => "main_center_1",
//				"CACHE_TYPE" => "A",
//				"NOINDEX" => "N",
//				"CACHE_TIME" => "3600"
//			)
//		);?>
<!--	</div>-->

	<?
		GLOBAL $samefilter;
		$samefilter = array("!=ID"=>$arResult['ID']);
		$APPLICATION->IncludeComponent(
		"bitrix:news.list",
		"same_articles",
		Array(
		"DISPLAY_DATE" => "Y",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"AJAX_MODE" => "N",
		"IBLOCK_TYPE" => "news",
		"IBLOCK_ID" => "14",
		"NEWS_COUNT" => "3",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "SORT",
		"SORT_ORDER2" => "ASC",
		"FILTER_NAME" => "samefilter",
		"FIELD_CODE" => array("ID", "CODE", "XML_ID", "NAME", "TAGS", "SORT", "PREVIEW_TEXT", "PREVIEW_PICTURE", "DETAIL_TEXT", "DETAIL_PICTURE", "DATE_ACTIVE_FROM", "ACTIVE_FROM", "DATE_ACTIVE_TO", "ACTIVE_TO", "SHOW_COUNTER", "SHOW_COUNTER_START", "IBLOCK_TYPE_ID", "IBLOCK_ID", "IBLOCK_CODE", "IBLOCK_NAME", "IBLOCK_EXTERNAL_ID", "DATE_CREATE", "CREATED_BY", "CREATED_USER_NAME", "TIMESTAMP_X", "MODIFIED_BY", "USER_NAME"),
		"PROPERTY_CODE" => array("FORUM_MESSAGE_CNT"),
		"CHECK_DATES" => "Y",
		"PREVIEW_TRUNCATE_LEN" => "0",
		"ACTIVE_DATE_FORMAT" => "j F Y",
		"SET_TITLE" => "N",
		"SET_STATUS_404" => "N",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
		"ADD_SECTIONS_CHAIN" => "Y",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"PARENT_SECTION" => $arResult['IBLOCK_SECTION_ID'],
		"PARENT_SECTION_CODE" => "",
		"INCLUDE_SUBSECTIONS" => "Y",
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
		"AJAX_OPTION_HISTORY" => "N",
		"DETAIL_URL"	=>	$arParams['DETAIL_URL'],
		)
		);?>
</div>

