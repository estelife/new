<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<div class="inner">
	<ul class="crumb">
		<li><a href="/">Главная</a></li>
		<li><b>Календарь событий</b></li>
	</ul>
	<div class="title">
		<h2>Календарь событий</h2>
	</div>
	<div class="items">
		<?php if (!empty($arResult['events'])):?>
			<?php foreach ($arResult['events'] as $arEvent):?>
				<div class="item event">
					<div class="item-rel">
						<span class="date"><?=$arEvent["first_date"]?></span>
						<h2>
							<a href="<?=$arEvent["link"]?>"><?=$arEvent["name"]?></a>
						</h2>
						<p><?=$arEvent["full_name"]?></p>
						<div class="img">
							<div class="img-in">
								<?php if(!empty($arEvent["logo"])):?>
									<img src="<?=$arEvent['logo']?>" title="<?=$arEvent["name"]?>" alt="<?=$arEvent["name"]?>" />
								<?endif?>
							</div>
						</div>
					
						<ul class="list1">
							<?php if(!empty($arEvent["country_name"])):?>
								<li>Место проведения: <b><?=$arEvent["country_name"]?><?if (!empty($arEvent["city_name"])):?>, г. <?=$arEvent["city_name"]?><?endif?></b><img src="/bitrix/templates/estelife/images/countries/k<?=$arEvent["country_id"]?>.png"></li>
							<?php endif?>
							<li>Период проведения: <b><?=$arEvent['first_period']['from']?>
									<?php if(!empty($arEvent['first_period']['to'])):?>
										-
										<?=$arEvent['first_period']['to']?>
									<?php endif; ?></b></li>
							<li>Формат: <b><?=$arEvent['types']?></b></li>
							<li>Направление: <b><?=$arEvent['directions']?></b></li>
						</ul>
						<div class="cl"></div>
					</div>
					<div class="border"></div>
				</div>
			<?php endforeach?>
		<?php endif?>
	</div>
	<div class="not-found<?=(!empty($arResult['events']) ? ' none' : '')?>">События не найдены ...</div>
	<?php if (!empty($arResult['nav'])):?>
		<?=$arResult['nav']?>
	<?php endif; ?>
</div>