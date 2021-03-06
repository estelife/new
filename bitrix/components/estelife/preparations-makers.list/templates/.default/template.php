<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<div class="inner">
	<ul class="crumb">
		<li><a href="/">Главная</a></li>
		<li><b>Производители препаратов</b></li>
	</ul>
	<div class="title">
		<h2>Производители препаратов</h2>
		<ul class="menu">
			<li><a href="/preparations-makers/" class="active">Препараты</a></li>
			<li><a href="/apparatuses-makers/">Аппараты</a></li>
			<li><a href="/threads-makers/">Нити</a></li>
		</ul>
	</div>
	<div class="items">
		<?php if (!empty($arResult['pills'])):?>
			<?php foreach ($arResult['pills'] as $arPill):?>
				<div class="item producer">
					<div class="item-rel">
						<div class="img">
							<div class="img-in">
								<?php if(!empty($arPill["logo_id"])): ?>
									<?=$arPill["img"]?>
								<?php else: ?>
									<img src="/img/icon/unlogo.png" />
								<?endif?>
							</div>
						</div>
						<div class="cols">
							<h2><a href="<?=$arPill["link"]?>"><?=$arPill["name"]?></a></h2>
							<ul>
								<li class="country c<?=$arPill["country_id"]?>"><?=$arPill["country_name"]?></li>
								<?php if (!empty($arPill["web"])):?>
									<li><a href="<?=$arPill["web"]?>" target="_blank"><?=$arPill["web_short"]?></a></li>
								<?php endif?>
							</ul>
							<p><?=$arPill['preview_text']?></p>
						</div>
					</div>
					<div class="border"></div>
				</div>
			<?php endforeach?>
		<?php endif;?>
	</div>
	<div class="not-found<?=(!empty($arResult['pills']) ? ' none' : '')?>">Производители не найдены ...</div>
	<?php if (!empty($arResult['nav'])):?>
		<?=$arResult['nav']?>
	<?php endif; ?>
</div>