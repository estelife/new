<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
?>
<?php if (!empty($arResult["ITEMS"])):?>
	<div class="articles">
		<div class="title">
			<h2>Материалы по теме</h2>
		</div>
		<div class="items">
			<? foreach($arResult["ITEMS"] as $arItem):?>
				<div class="item">
					<?$img = CFile::GetFileArray($arItem['PROPERTIES']['LISTIMG']['VALUE']);?>
					<img src="<?=$img['SRC']?>" alt="<?=$arItem['NAME']?>" title="<?=$arItem['NAME']?>" width="229px" height="160px" />
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
	</div>
<?php endif?>