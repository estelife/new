<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<div class="inner">
	<ul class="crumb">
		<li><a href="/">Главная</a></li>
		<li><b>Клиники</b></li>
	</ul>
	<div class="title">
		<h1>Клиники <?if ($_GET['city']==359):?>Москвы<?elseif($_GET['city']==358):?>Санкт-Петербурга<?endif?></h1>
	</div>
	<div class="items">
		<?php if (!empty($arResult['clinics'])):?>
			<?php foreach ($arResult['clinics'] as $arClinic):?>
			<div class="item company">
				<div class="item-rel">
					<h2><?php if ($arClinic["recomended"] == 1):?><span class="checked"></span><?php endif?><a href="<?=$arClinic["link"]?>" class="el-get-detail"><?=$arClinic["name"]?></a></h2>
					<div class="item-in">
						<?php if (!empty($arClinic['specialization'])):?>
						<p><?=$arClinic['specialization']?></p>
						<?php endif?>
						<a href="<?=$arClinic["link"]?>" class="el-get-detail">
							<div class="img">
								<div class="img-in">
									<?php if(!empty($arClinic["logo"])): ?>
										<?=$arClinic["logo"]?>
									<?php else: ?>
										<img src="/img/icon/unlogo.png" />
									<?endif?>
								</div>
							</div>
						</a>
						<div class="cols col1">
							<span><?=$arClinic["address"]?></span>
							<span><?=$arClinic['phone']?></span>
							<a href="#"><a target='_blank' href="<?=$arClinic["web"]?>"><?=$arClinic["web_short"]?></a></a>
						</div>
					</div>
				</div>
				<div class="border"></div>
			</div>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
	<div class="not-found<?=(!empty($arResult['clinics']) ? ' none' : '')?>">Клиники не найдены ...</div>
	<?php if (!empty($arResult['nav'])):?>
		<?=$arResult['nav']?>
	<?php endif; ?>
</div>