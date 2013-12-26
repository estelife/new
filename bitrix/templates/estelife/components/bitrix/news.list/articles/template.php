<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
?>
<div class="content">
	<div class="articles inner">
		<ul class="crumb">
			<li><a href="/">Главная</a></li>
			<li><a href="#"><?=$arResult['LAST_SECTION']['NAME']?></a></li>
		</ul>
		<?php if (!empty($arResult["ITEMS"])):?>
			<div class="title">
				<h2><?=$arResult['LAST_SECTION']['NAME']?></h2>
			</div>
			<div class="items">
				<? foreach($arResult["ITEMS"] as $arItem):?>
				<div class="item article">
					<?$img = CFile::GetFileArray($arItem['PROPERTIES']['LISTIMG']['VALUE']);?>
					<img src="<?=$img['SRC']?>" alt="<?=$arItem['NAME']?>" title="<?=$arItem['NAME']?>" width="229" height="160" />
					<h3><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem['NAME']?></a></h3>
					<p><?=\core\types\VString::truncate($arItem['PREVIEW_TEXT'], 70, '...')?></p>
					<ul class="stat">

						<?php if (!empty($arItem['ACTIVE_FROM'])):?>
							<li class="date"><?=date('d.m.Y',strtotime($arItem['ACTIVE_FROM']))?></li>
						<?php endif?>
<!--						<li class="comments">9<i></i></li>-->
<!--						<li class="likes">41<i></i></li>-->
<!--						<li class="unlikes">2<i></i></li>-->
					</ul>
				</div>
				<?endforeach?>

			</div>
			<?=$arResult["NAV_STRING"]?>
		</div>
	<?php endif?>
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
</div>
