<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?php if(!empty($arResult['clinics'])):?>
	<div class="promotions announces">
		<div class="title">
			<h2><?=$arResult['city']['T_NAME'];?></h2>
			<a href="<?=$arResult['clinics_link']?>" class="more_promotions">Больше клиник</a>
			<a href="#" class="arrow black bottom change_city change_promotions_city"><span><?=$arResult['city']['NAME']?></span><i></i></a>
			<div class="cities none promotions_city"></div>
		</div>
		<div class="items">
			<? foreach($arResult['clinics'] as $arClinic):?>
			<div class="item company">
				<div class="item-rel">
					<h2>
						<a href="<?=$arClinic["link"]?>" class="el-get-detail"><?=$arClinic["name"]?></a>
						<?php if ($arClinic["recomended"] == 1):?><a href="/about/quality-mark.php" class="checked">Знак качества Estelife</a><?php endif?>
					</h2>
					<div class="item-in">
						<?if (!empty($arClinic['specialization'])):?>
							<p><?=$arClinic['specialization']?></p>
						<?endif?>

						<div class="img">
							<div class="img-in">
								<?php if(!empty($arClinic["logo"])): ?>
									<?=$arClinic["logo"]?>
								<?php else: ?>
									<div class="default">Изображение отсутствует</div>
								<?endif?>
							</div>
						</div>

						<div class="cols col1">
							<span>г. <?=$arClinic['city_name']?>, <?=$arClinic["address"]?></span>
							<span><?=$arClinic['phone']?></span>
							<a href="#"><a target='_blank' href="<?=$arClinic["web"]?>"><?=$arClinic["web_short"]?></a></a>
						</div>
					</div>
				</div>
				<div class="border"></div>
			</div>
			<?php endforeach?>
		</div>
	</div>
<?php elseif (!empty($arResult['akzii'])):?>
	<div class="promotions announces">
		<div class="title">
			<h2>Акции клиник</h2>
			<a href="<?=$arResult['akzii_link']?>" class="more_promotions">Больше акций</a>
			<a href="#" class="arrow black bottom change_city change_promotions_city"><span><?=$arResult['city']['NAME']?></span><i></i></a>
			<div class="cities none promotions_city"></div>
		</div>
		<div class="items">
			<?php foreach ($arResult['akzii'] as $arValue):?>
				<div class="item promotion">
					<div class="item-rel">
						<?php if($arValue['view_type']!=2): ?>
							<span class="perc"><?=$arValue["sale"]?>%</span>
						<?php endif; ?>
						<a href="<?=$arValue['link']?>">
							<img src="<?=$arValue['src']?>" alt="<?=$arValue['name']?>" title="<?=$arValue['name']?>" />
						</a>
						<h3><a href="<?=$arValue['link']?>"><?=$arValue['name']?></a></h3>
						<div class="cols prices">
							<?php if($arValue['view_type']==3): ?>
								<b class="only-perc">скидка <?=$arValue["sale"]?>%</b>
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