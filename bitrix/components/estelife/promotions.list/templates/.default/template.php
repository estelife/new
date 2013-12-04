<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?php
$APPLICATION->SetPageProperty("title", $arResult['SEO']['title']);
$APPLICATION->SetPageProperty("description", $arResult['SEO']['description']);
$APPLICATION->SetPageProperty("keywords", $arResult['SEO']['keywords']);
?>
<div class="promotions inner">
	<ul class="crumb">
		<li><a href="#">Главная</a></li>
		<li><b>Акции</b></li>
	</ul>
	<div class="title">
		<h2>Акции</h2>
	</div>
	<div class="items">
		<?php if (!empty($arResult['akzii'])):?>
			<?php foreach ($arResult['akzii'] as $arValue):?>
				<div class="item">
					<span class="perc"><?=$arValue["sale"]?>%</span>
					<a href="<?=$arValue['link']?>">
						<img src="<?=$arValue['logo']?>" alt="<?=$arValue["name"]?>" title="<?=$arValue["name"]?>" width="227px" height="159px">
					</a>
					<h3><?=$arValue["name"]?></h3>
					<div class="cols prices">
						<b><?=$arValue['new_price']?> <i></i></b>
						<s><?=$arValue['old_price']?> <i></i></s>
					</div>
					<div class="cols time">
						<?=$arValue['time']?> <?=$arValue['day']?>
						<i></i>
					</div>
				</div>
			<?php endforeach?>
		<?php endif?>
	</div>
	<div class="not-found<?=(!empty($arResult['akzii']) ? ' none' : '')?>"><?=GetMessage("ESTELIFE_ACTION_NOT_FOUND")?></div>

	<?php if (!empty($arResult['nav'])):?>
		<?=$arResult['nav']?>
	<?php endif?>
</div>
