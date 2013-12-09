<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="inner">
	<ul class="crumb">
		<li><a href="/">Главная</a></li>
		<li><a href="/promotions/">Акции</a></li>
		<li><b><?=$arResult['action']['preview_text']?></b></li>
	</ul>
	<div class="item promotion detail">
		<h1><?=$arResult['action']['preview_text']?></h1>
		<div class="current">
			<span class="perc"><?=$arResult['action']['base_sale']?>%</span>
			<div class="cols prices">
				<b><?=$arResult['action']['new_price']?> <i></i></b>
				<s><?=$arResult['action']['old_price']?> <i></i></s>
			</div>
			<div class="cols time">
				<?=$arResult['action']['day_count']?>
				<i></i>
				<span>до <?=$arResult['action']['end_date']?></span>
			</div>
		</div>
		<?if (!empty($arResult['action']['photos'])):?>
			<div class="gallery">
				<div class="gallery-in">
					<?php foreach ($arResult['action']['photos'] as $val):?>
						<?=$val['original']?>
					<?php endforeach?>
				</div>
				<div class="gallery-desc">
					<?=$arResult['action']['photo_desc']?>
				</div>
				<?php if ($arResult['action']['photos_count']>1):?>
					<a href="#" class="arrow left">Назад<i></i></a>
					<a href="#" class="arrow right">Вперед<i></i></a>
				<?php endif?>
			</div>
		<?endif?>
		<div class="announce">
			<?=$arResult['action']['detail_text']?>
		</div>
		<?php if (!empty($arResult['action']['clinics'])):?>
			<?php foreach ($arResult['action']['clinics'] as $val):?>
				<div class="clinic">
					<div class="cols col1">
						<a href="<?=$val['link']?>"></a>
					</div>
					<div class="cols col2">
						<h3><?=$val['clinic_name']?></h3>
						<span>г. <?=$val['city']?> <?=$val['clinic_address']?></span>
						<span><?=$val['phone']?></span>
					</div>
					<div class="cols col3">
						<a href="<?=$val['link']?>" class="more">Подробнее о клинике</a>
					</div>
				</div>
			<?php endforeach?>
		<?php endif?>
		<div class="info nobo">
			<div class="social cols">
				<?$APPLICATION->IncludeComponent("estelife:social.estelife","",array());?>
			</div>
		</div>
	</div>
</div>