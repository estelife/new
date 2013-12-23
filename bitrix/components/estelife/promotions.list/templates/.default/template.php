<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?php
$APPLICATION->SetPageProperty("title", $arResult['SEO']['title']);
$APPLICATION->SetPageProperty("description", $arResult['SEO']['description']);
$APPLICATION->SetPageProperty("keywords", $arResult['SEO']['keywords']);
?>

<div class="inner">
	<ul class="crumb">
		<li><a href="#">Главная</a></li>
		<li><b>Акции <?if ($_GET['city']==359):?>Москвы<?elseif($_GET['city']==358):?>Санкт-Петербурга<?endif?></b></li>
	</ul>
	<div class="title">
		<h2>Акции <?if ($_GET['city']==359):?>Москвы<?elseif($_GET['city']==358):?>Санкт-Петербурга<?endif?></h2>
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
							<b>
								<?php if($arValue['view_type']==3): ?>
									скидка <?=$arValue["sale"]?>%
								<?php else: ?>
									<?=$arValue['new_price']?> <i></i>
								<?php endif; ?>
							</b>
							<?php if($arValue['view_type']==1): ?>
								<s><?=$arValue['old_price']?> <i></i></s>
							<?php endif; ?>
						</div>
						<div class="cols time">
							<?=$arValue['time']?> <?=$arValue['day']?>
							<i></i>
						</div>
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
