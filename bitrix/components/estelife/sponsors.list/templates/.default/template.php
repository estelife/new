<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<div class="inner">
	<ul class="crumb">
		<li><a href="/">Главная</a></li>
		<li><b>Организаторы</b></li>
	</ul>
	<div class="title">
		<h2>Организаторы</h2>
	</div>
	<div class="items">
		<?php if (!empty($arResult['org'])):?>
			<?php foreach ($arResult['org'] as $arOrg):?>
				<div class="item company sponsor">
					<h2><a href="<?=$arOrg["link"]?>"><?=$arOrg["name"]?></a></h2>
					<div class="item-in">
						<div class="img">
							<div class="img-in">
								<?php if(!empty($arOrg["logo_id"])):?>
									<?=$arOrg['img']?>
								<?endif?>
							</div>
						</div>
						<div class="cols col1">
							<span class="country big k<?=$arOrg['country_id']?>"></span>
							<? if(!empty($arOrg["address"])):?>
								<span><?=$arOrg["address"]?></span>
							<? endif?>
							<? if (!empty($arOrg["web"])):?>
								<a target='_blank' href="<?=$arOrg["web"]?>"><?=$arOrg["short_web"]?></a>
							<? endif?>
						</div>
					</div>
				</div>
			<?php endforeach?>
		<?php endif; ?>
	</div>
	<div class="not-found<?=(!empty($arResult['org']) ? ' none' : '')?>">Организаторы не найдены ...</div>
	<?php if (!empty($arResult['nav'])):?>
		<?=$arResult['nav']?>
	<?php endif; ?>
</div>