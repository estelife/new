<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<div class="inner">
	<ul class="crumb">
		<li><a href="/">Главная</a></li>
		<li><b>Расписание семинаров</b></li>
	</ul>
	<div class="title">
		<h2>Расписание семинаров</h2>
	</div>
	<div class="items">
		<?php if (!empty($arResult['training'])):?>
			<?php foreach ($arResult['training'] as $arTraining):?>
				<div class="item training">
					<div class="item-rel">
						<h2><a href="<?=$arTraining["link"]?>"><?=$arTraining["name"]?></a></h2>
						<div class="item-in">
							<div class="img">
								<div class="img-in">
									<?php if(!empty($arTraining["logo"])):?>
										<?=$arTraining['logo']?>
									<?endif?>
								</div>
							</div>
							<p><?=$arTraining['preview_text']?></p>
							Период проведения: <b><?=$arTraining['first_period']['from']?>
								<?php if(!empty($arTraining['first_period']['to'])):?>
									-
									<?=$arTraining['first_period']['to']?>
								<?php endif; ?></b><br>
							Организатор: <a href="<?=$arTraining['company_link']?>" class="link"><?=$arTraining["company_name"]?></a>
							<span class="date"><?=$arTraining["first_date"]?></span>
						</div>
					</div>
					<div class="border"></div>
				</div>
			<?php endforeach?>
		<?php endif?>
	</div>
	<div class="not-found<?=(!empty($arResult['training']) ? ' none' : '')?>">Семинары не найдены ...</div>
	<?php if (!empty($arResult['nav'])):?>
		<?=$arResult['nav']?>
	<?php endif; ?>
</div>