<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
?>
<?php if (!empty($arResult["ITEMS"])):?>
	<div class="general-news">
		<div class="title">
			<h1>Контурная пластика</h1>
			<h2>Точка зрения</h2>
		</div>
			<div class="cols col1">
				<?php if (!empty($arResult["FIRST"])):?>
					<div class="img">
						<a href="<?=$arResult["FIRST"]["DETAIL_PAGE_URL"]?>">
							<?$img = CFile::GetFileArray($arResult["FIRST"]['PROPERTIES']['FRONTBIG']['VALUE']);?>
							<img src="<?=$img['SRC']?>" width="393px" height="218px"" alt="<?=$arResult["FIRST"]['NAME']?>" title="<?=$arResult["FIRST"]['NAME']?>" />
						</a>
						<div>
							<h3><?=$arResult["FIRST"]['NAME']?></h3>
						</div>
						<span>1</span>
					</div>
					<?php if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arResult["FIRST"]["PREVIEW_TEXT"]):?>
						<a href="<?=$arResult["FIRST"]["DETAIL_PAGE_URL"]?>" class="text"><?=\core\types\VString::truncate($arResult["FIRST"]['PREVIEW_TEXT'], 165, '...')?> &rarr;</a>
					<?php endif?>
<!--					<ul class="stat">-->
<!--						<li class="comments"><i></i>0</li>-->
<!--						<li class="likes"><i></i>0</li>-->
<!--						<li class="unlikes"><i></i>0</li>-->
<!--					</ul>-->
				<?php endif?>
			</div>
			<div class="cols col2">
				<?php if (!empty($arResult["ITEMS"])):?>
					<?$i = 2?>
					<?php foreach ($arResult["ITEMS"] as $val):?>
						<div class="img">
							<a href="<?=$val['DETAIL_PAGE_URL']?>">
								<?$img = CFile::GetFileArray($val['PROPERTIES']['FRONTRIGHT']['VALUE']);?>
								<img src="<?=$img['SRC']?>" width="143px" height="98px"" alt="<?=$arResult["FIRST"]['NAME']?>" title="<?=$arResult["FIRST"]['NAME']?>" />
							</a>
							<?php if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $val["PREVIEW_TEXT"]):?>
								<div><p><?=\core\types\VString::truncate($val['PREVIEW_TEXT'], 30, '...')?></p></div>
							<?php endif?>
							<span><?=$i?></span>
						</div>
						<?$i++;?>
					<?php endforeach?>
				<?php endif?>
				<div class="subscribe">
					<h3>Хотите всегда быть в курсе?</h3>
					<a href="#" class="submit">Подпишитесь</a>
				</div>
			</div>
	</div>
<?php endif?>