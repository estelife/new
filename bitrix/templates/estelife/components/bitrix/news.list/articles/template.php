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
		<div class="title">
			<h2><?=$arResult['LAST_SECTION']['NAME']?></h2>
		</div>
		<?php if (!empty($arResult["ITEMS"])):?>
			<div class="items">
				<? foreach($arResult["ITEMS"] as $arItem):?>
				<div class="item article">
					<div class="item-in">
						<?$img = CFile::GetFileArray($arItem['PROPERTIES']['LISTIMG']['VALUE']);?>
						<img src="<?=$img['SRC']?>" alt="<?=$arItem['NAME']?>" title="<?=$arItem['NAME']?>" />
						<h3><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem['NAME']?></a></h3>
						<p><?=$arItem['PREVIEW_TEXT']?></p>
					</div>
					<ul class="stat notlike">

						<?php if (!empty($arItem['ACTIVE_FROM'])):?>
							<li class="date"><?=date('d.m.Y',strtotime($arItem['ACTIVE_FROM']))?></li>
						<?php endif?>
<!--						<li class="comments">9<i></i></li>-->
						<li class="likes"><?if ($arItem['LIKES']['countLike']>0):?><?=$arItem['LIKES']['countLike']?><?else:?>0<?endif?><i></i></li>
						<li class="unlikes"><?if ($arItem['LIKES']['countDislike']>0):?><?=$arItem['LIKES']['countDislike']?><?else:?>0<?endif?><i></i></li>
					</ul>
				</div>
				<?endforeach?>

			</div>
			<?=$arResult["NAV_STRING"]?>
		<?php endif?>
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
</div>
