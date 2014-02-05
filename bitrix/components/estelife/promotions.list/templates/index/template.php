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
						<?php if($arValue['view_type']!=2): ?>
							<span class="perc"><?=$arValue["sale"]?>%</span>
						<?php endif; ?>
						<a href="<?=$arValue['link']?>">
							<img src="<?=$arValue['src']?>" alt="<?=$arValue['name']?>" title="<?=$arValue['name']?>" />
						</a>
						<h3><a href="<?=$arValue['link']?>"><?=$arValue['name']?></a></h3>
						<div class="cols prices">
							<?php if($arValue['view_type']==3): ?>
								<b class="only-perc">скидка <?=$arValue["sale"]?>%</b>
							<?php else: ?>
								<b><?=$arValue['new_price']?> <i></i></b>
							<?php endif; ?>
							<?php if($arValue['view_type']==1): ?>
								<s><?=$arValue['old_price']?> <i></i></s>
							<?php endif; ?>
						</div>
						<div class="cols time">
							<?=$arValue['time']?> <?=$arValue['day']?>
							<i></i>
						</div>
						<a href="/cl<?=$arValue['clinic_id']?>/" class="clinic-link"><?=$arValue['clinic_name']?></a>
					</div>
					<div class="border"></div>
				</div>
			<?php endforeach?>
		</div>
	</div>
<?php endif?>