<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="inner">
	<ul class="crumb">
		<li><a href="/">Главная</a></li>
		<li><a href="/promotions/?city=<?=$arResult['action']['clinic']['main']['city_id']?>">Акции <?=$arResult['action']['city_name']?></a></li>
		<li><b><?=$arResult['action']['preview_text']?></b></li>
	</ul>
	<div class="item promotion detail">
		<h1><?=$arResult['action']['preview_text']?></h1>
		<div class="data">
			<img src="<?=$arResult['action']['big_photo']['src']?>" alt="<?=(!empty($arResult['action']['big_photo']['description']) ? $arResult['action']['big_photo']['description'] : $arResult['action']['preview_text'])?>" title="" />
			<div class="current">
				<h3><?=$arResult['action']['clinic']['main']['name']?></h3>
				<span class="city">г. <?=$arResult['action']['clinic']['main']['city_name']?></span>
				<?php if($arResult['action']['view_type']!=2): ?>
					<span class="perc">
						<?=$arResult['action']['base_sale']?>%
					</span>
				<?php endif; ?>
				<div class="cols prices">
					<?php if($arResult['action']['view_type']==3): ?>
						<b>скидка <?=$arResult['action']['base_sale']?>%</b>
					<?php else: ?>
						<b><?=$arResult['action']['new_price']?> <i></i></b>
						<?php if($arResult['action']['view_type']==1):?>
							<s><?=$arResult['action']['old_price']?> <i></i></s>
						<?php endif; ?>
					<?php endif; ?>
				</div>
				<div class="cols time">
					<?if($arResult['action']['end_date']<$arResult['action']['now']):?>
						<span class="old-promotion"><b>Акция завершена</b></span>
					<?else:?>
						<?=$arResult['action']['day_count']?>
						<i></i>
						<span>до <?=$arResult['action']['end_date_format']?></span>
					<?endif?>
				</div>
				<?if($arResult['action']['end_date']<$arResult['action']['now']):?>
					<a href="<?=$arResult['action']['clinic']['link']?>" class="more">Действующие акции клиники<span></span></a>
				<?else:?>
					<?php if(!empty($arResult['action']['more_information'])): ?>
						<a href="<?=$arResult['action']['more_information']?>" target="_blank" class="more">Подробная информация и цены<span></span></a>
					<?php endif; ?>
				<?endif?>
			</div>
		</div>

		<?=$arResult['action']['detail_text']?>

		<div class="clinic">
			<a href="<?=$arResult['action']['clinic']['main']['link']?>" class="more"><i></i></a>
			<div class="about">
				<h3><?=$arResult['action']['clinic']['main']['name']?></h3>
				<span>г. <?=$arResult['action']['clinic']['main']['city_name']?></span>
				<span><a href="<?=$arResult['action']['clinic']['main']['web']?>" target="_blank"><?=$arResult['action']['clinic']['main']['web_short']?></a></span>
			</div>
			<h4>Акция проводится по адресам:</h4>
			<ul class="contacts">
				<li>
					<?=$arResult['action']['clinic']['main']['address']?><br />
					<?=$arResult['action']['clinic']['main']['phone']?>

					<?php if(!empty($arResult['action']['clinic']['main']['latitude'])): ?>
						<div class="map">
							<span class="lat"><?=$arResult['action']['clinic']['main']['latitude']?></span>
							<span class="lng"><?=$arResult['action']['clinic']['main']['longitude']?></span>
						</div>
					<?php endif; ?>
				</li>
				<?php if(!empty($arResult['action']['clinic']['offices'])): ?>
					<?php foreach($arResult['action']['clinic']['offices'] as $arOffice):?>
					<li>
						<?=$arOffice['address']?><br />
						<?=$arOffice['phone']?>

						<?php if(!empty($arOffice['latitude'])): ?>
							<div class="map">
								<span class="lat"><?=$arOffice['latitude']?></span>
								<span class="lng"><?=$arOffice['longitude']?></span>
							</div>
						<?php endif; ?>
					</li>
					<?php endforeach; ?>
				<?php endif; ?>
			</ul>
		</div>

		<div class="info nobo">

		</div>
	</div>
	<?php if (!empty($arResult['action']['similar'])):?>
		<div class="similars">
			<div class="title">
				<h2>Похожие акции</h2>
			</div>
			<div class="items">
				<?php foreach ($arResult['action']['similar'] as $arValue):?>
					<div class="item promotion">
						<div class="item-rel">
							<?php if($arValue['view_type']!=2): ?>
								<span class="perc"><?=$arValue["base_sale"]?>%</span>
							<?php endif; ?>
							<a href="<?=$arValue['link']?>">
								<img src="<?=$arValue['src']?>" alt="<?=$arValue['name']?>" title="<?=$arValue['name']?>" />
							</a>
							<h3><a href="<?=$arValue['link']?>"><?=$arValue['name']?></a></h3>
							<div class="cols prices">
								<?php if($arValue['view_type']==3): ?>
									<b class="only-perc">скидка <?=$arValue["base_sale"]?>%</b>
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
			</div>
		</div>
	<?php endif?>
</div>