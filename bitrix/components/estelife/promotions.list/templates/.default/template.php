<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<div class="inner">
	<ul class="crumb">
		<li><a href="#">Главная</a></li>
		<li><b>Акции <?=$arResult['city']['R_NAME']?></b></li>
	</ul>
	<div class="title">
		<h2>Акции <?=$arResult['city']['R_NAME']?></h2>
	</div>
	<div class="items">
		<?php if (!empty($arResult['akzii'])):?>
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
		<?php endif?>
	</div>
	<div class="not-found<?=(!empty($arResult['akzii']) ? ' none' : '')?>"><?=GetMessage("ESTELIFE_ACTION_NOT_FOUND")?></div>
	<?php if (!empty($arResult['nav'])):?>
		<?=$arResult['nav']?>
	<?php endif?>
</div>
