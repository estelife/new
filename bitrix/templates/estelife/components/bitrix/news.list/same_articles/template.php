<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
?>
<?php if (!empty($arResult["ITEMS"])):?>
	<div class="articles">
		<div class="title">
			<h2>Материалы по теме</h2>
		</div>
		<div class="items ">
			<? foreach($arResult["ITEMS"] as $arItem):?>
				<div class="item article">
					<div class="item-in">
						<?$img = CFile::GetFileArray($arItem['PROPERTIES']['LISTIMG']['VALUE']);?>
						<img src="<?=$img['SRC']?>" alt="<?=$arItem['NAME']?>" title="<?=$arItem['NAME']?>" width="229" height="160" />
						<h3><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem['NAME']?></a></h3>
						<p><?=\core\types\VString::truncate($arItem['PREVIEW_TEXT'], 70, '...')?><span></span></p>
					</div>
					<ul class="stat">

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
	</div>
<?php endif?>