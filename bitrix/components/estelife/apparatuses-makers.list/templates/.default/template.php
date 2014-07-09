<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<div class="inner">
	<ul class="crumb">
		<li><a href="/">Главная</a></li>
		<li><b>Производители аппаратов</b></li>
	</ul>
	<div class="title">
		<h2>Производители аппаратов</h2>
		<ul class="menu">
			<li><a href="/preparations-makers/">Препараты</a></li>
			<li><a href="/apparatuses-makers/" class="active">Аппараты</a></li>
			<li><a href="/threads-makers/">Нити</a></li>
		</ul>
	</div>
	<div class="items">
		<?php if (!empty($arResult['apparatus'])):?>
			<?php foreach ($arResult['apparatus'] as $arApp):?>
				<div class="item producer">
					<div class="item-rel">
						<div class="img">
							<div class="img-in">
								<?php if(!empty($arApp["logo_id"])): ?>
									<?=$arApp["img"]?>
								<?php else: ?>
									<img src="/img/icon/unlogo.png" />
								<?endif?>
							</div>
						</div>
						<div class="cols">
							<h2><a href="<?=$arApp["link"]?>"><?=$arApp["name"]?></a></h2>
							<ul>
								<li class="country c<?=$arApp["country_id"]?>"><?=$arApp["country_name"]?></li>
								<?php if (!empty($arApp["web"])):?>
									<li><a href="<?=$arApp["web"]?>" target="_blank"><?=$arApp["web_short"]?></a></li>
								<?php endif?>
							</ul>
							<p><?=$arApp['preview_text']?></p>
						</div>
					</div>
					<div class="border"></div>
				</div>
			<?php endforeach?>
		<?php endif;?>
	</div>
	<div class="not-found<?=(!empty($arResult['apparatus']) ? ' none' : '')?>">Производители не найдены ...</div>
	<?php if (!empty($arResult['nav'])):?>
		<?=$arResult['nav']?>
	<?php endif; ?>
</div>