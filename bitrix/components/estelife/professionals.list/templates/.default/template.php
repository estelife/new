<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<div class="inner">
	<ul class="crumb">
		<li><a href="/">Главная</a></li>
		<li><b>Аппараты</b></li>
	</ul>
	<div class="title">
		<h1>Аппараты</h1>
	</div>
	<div class="items">
		<?php if (!empty($arResult['apps'])):?>
			<?php foreach ($arResult['apps'] as $arApp):?>
				<div class="item product">
					<div class="item-rel">
						<div class="img">
							<div class="img-in">
								<?php if(!empty($arApp["logo_id"])): ?>
									<?=$arApp["logo"]?>
								<?php else: ?>
									<div class="default">Изображение отсутствует</div>
								<?endif?>
							</div>
						</div>
						<div class="cols">
							<h2><a href="<?=$arApp["link"]?>"><?=$arApp["name"]?></a></h2>
							<ul>
								<li class="country c<?=$arApp["country_id"]?>"><?=$arApp["country_name"]?></li>
								<li>Производитель: <a href="<?=$arApp["company_link"]?>"><?=$arApp["company_name"]?></a></li>
							</ul>
							<p><?=$arApp['preview_text']?></p>
						</div>
					</div>
					<div class="border"></div>
				</div>
			<?php endforeach?>
		<?php endif?>
	</div>
	<div class="not-found<?=(!empty($arResult['apps']) ? ' none' : '')?>">Аппараты не найдены ...</div>
	<?php if (!empty($arResult['nav'])):?>
		<?=$arResult['nav']?>
	<?php endif; ?>
</div>