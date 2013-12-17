<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?php if (!empty($arResult['akzii'])):?>
	<div class="promotions announces">
		<div class="title">
			<h2>Акции клиник</h2>
			<a href="<?=$arResult['link']?>" class="more_promotions">Больше акций</a>
			<a href="#" class="arrow black bottom change_city change_promotions_city"><span><?=$arResult['city']['NAME']?></span><i></i></a>
			<div class="cities none promotions_city"></div>
		</div>
		<div class="items">
			<?php foreach ($arResult['akzii'] as $arValue):?>
				<div class="item promotion">
					<div class="item-rel">
						<span class="perc"><?=$arValue["sale"]?>%</span>
						<a href="<?=$arValue['link']?>">
							<img src="<?=$arValue['src']?>" width="227px" height="158px" alt="<?=$arValue['name']?>" title="<?=$arValue['name']?>" />
						</a>
						<h3><a href="<?=$arValue['link']?>"><?=$arValue['name']?></a></h3>
						<div class="cols prices">
							<b><?=$arValue['new_price']?> <i></i></b>
							<s><?=$arValue['old_price']?> <i></i></s>
						</div>
						<div class="cols time">
							<?=$arValue['time']?> <?=$arValue['day']?>
							<i></i>
						</div>
					</div>
					<div class="border"></div>
				</div>
			<?php endforeach?>
		</div>
	</div>
<?php endif?>