<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<div class="inner">
	<ul class="crumb">
		<li><a href="/">Главная</a></li>
		<li><b>Учебные центры</b></li>
	</ul>
	<div class="title">
		<h2>Учебные центры</h2>
	</div>
	<div class="items">
		<?php if (!empty($arResult['org'])):?>
			<?php foreach ($arResult['org'] as $arOrg):?>
				<div class="item company">

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
							<?php if(!empty($arOrg["address"])):?>
								<span><?=$arOrg["address"]?></span>
							<?php endif?>
							<?php if (!empty($arOrg["phone"])):?>
								<span><?=$arOrg['phone']?></span>
							<?php endif;?>
							<?php if (!empty($arOrg["web"])):?>
								<a href="<?=$arOrg["web"]?>"><?=$arOrg["short_web"]?></a>
							<?php endif;?>
						</div>
					</div>
				</div>
			<?php endforeach?>
		<?php endif; ?>
	</div>
	<div class="not-found<?=(!empty($arResult['org']) ? ' none' : '')?>">Учебные центры не найдены ...</div>
	<?php if (!empty($arResult['nav'])):?>
		<?=$arResult['nav']?>
	<?php endif; ?>
</div>