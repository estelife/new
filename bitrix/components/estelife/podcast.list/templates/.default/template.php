<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
?>
<?php if (!empty($arResult["ELEMENTS"])):?>
	<div class="general-news">
		<div class="title">
			<h2>Тема недели</h2>
			<h1><?=$arResult['SECTION_NAME']?></h1>
		</div>
		<div class="cols col1">
			<?php if (!empty($arResult["FIRST"])):?>
				<div class="img">
					<a href="<?=$arResult["FIRST"]["DETAIL_URL"]?>">
						<img src="<?=$arResult["FIRST"]['IMG_B']?>" width="393px" height="218px"" alt="<?=$arResult["FIRST"]['NAME']?>" title="<?=$arResult["FIRST"]['NAME']?>" />
					</a>
					<div>
						<h3><?=$arResult["FIRST"]['NAME']?></h3>
					</div>
					<span>1</span>
				</div>
				<?php if($arResult["FIRST"]["PREVIEW_TEXT_B"]):?>
					<a href="<?=$arResult["FIRST"]["DETAIL_URL"]?>" class="text"><?=$arResult["FIRST"]['PREVIEW_TEXT_B']?></a>
				<?php endif?>
			<?php endif?>
		</div>
		<div class="cols col2">
			<?$i = 2?>
			<?php foreach ($arResult["ELEMENTS"] as $val):?>
				<div class="img">
					<a href="<?=$val['DETAIL_URL']?>">
						<img src="<?=$val['IMG_S']?>" width="143px" height="98px"" alt="<?=$val['NAME']?>" title="<?=$val['NAME']?>" />
					</a>
					<?php if($val["NAME"]):?>
						<div><p><?=$val['NAME']?></p></div>
					<?php endif?>
					<span><?=$i?></span>
				</div>
				<?$i++;?>
			<?php endforeach?>
			<?$APPLICATION->IncludeComponent("estelife:subscribe.bitrix",
				"",
				Array()
			);?>
		</div>
	</div>
<?php endif?>