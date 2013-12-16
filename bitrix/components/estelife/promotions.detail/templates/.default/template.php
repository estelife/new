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
		<?if (!empty($arResult['action']['big_photo'])):?>
			<div class="article-img">
				<div class="article-img-in">
					<img src="<?=$arResult['action']['big_photo']['src']?>" alt="<?=(!empty($arResult['action']['big_photo']['description']) ? $arResult['action']['big_photo']['description'] : $arResult['action']['preview_text'])?>" title="" />
				</div>
				<?php if(!empty($arResult['action']['big_photo']['description'])): ?>
					<div class="article-img-desc">
						<?=$arResult['action']['big_photo']['description']?>
					</div>
				<?php endif; ?>
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
						<a href="<?=$val['link']?>" class="more" target="_blank">Подробнее о клинике</a>
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
	<?php if (!empty($arResult['action']['similar'])):?>
		<div class="similars">
			<div class="title">
				<h2>Похожие акции</h2>
			</div>
			<div class="items">
				<?php foreach ($arResult['action']['similar'] as $val):?>
					<div class="item promotion">
						<span class="perc"><?=$val["base_sale"]?>%</span>
						<a href="<?=$val['link']?>">
							<img src="<?=$val['src']?>" width="227px" height="158px" alt="<?=$val['name']?>" title="<?=$val['name']?>" />
						</a>
						<h3><?=$val['name']?></h3>
						<div class="cols prices">
							<b><?=$val['new_price']?> <i></i></b>
							<s><?=$val['old_price']?> <i></i></s>
						</div>
						<div class="cols time">
							<?=$val['time']?> <?=$val['day']?>
							<i></i>
						</div>
					</div>
				<?php endforeach?>
			</div>
		</div>
	<?php endif?>
</div>