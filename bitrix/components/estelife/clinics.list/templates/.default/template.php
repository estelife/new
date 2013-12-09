<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<div class="inner">
	<ul class="crumb">
		<li><a href="/">Главная</a></li>
		<li><b>Клиники</b></li>
	</ul>
	<div class="title">
		<h1>Клиники Санкт-Петербурга</h1>
	</div>
	<div class="items">
		<?php if (!empty($arResult['clinics'])):?>
			<?php foreach ($arResult['clinics'] as $arClinic):?>
			<div class="item clinic">
				<h2><a href="<?=$arClinic["link"]?>" class="el-get-detail"><?=$arClinic["name"]?></a></h2>
				<div class="item-in">
					<p>Косметология, пластическая хирургия</p>
					<a href="<?=$arClinic["link"]?>" class="el-get-detail">
						<?php if(!empty($arClinic["logo"])): ?>
							<?=$arClinic["logo"]?>
						<?php else: ?>
							<img src="/img/icon/unlogo.png" />
						<?endif?>
					</a>
					<div class="cols col1">
						<span><?=$arClinic["address"]?></span>
						<span><?=$arClinic['phone']?></span>
						<a href="#"><a target='_blank' href="<?=$arClinic["web"]?>"><?=$arClinic["web_short"]?></a></a>
					</div>
				</div>
			</div>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
	<?php if (!empty($arResult['nav'])):?>
		<?=$arResult['nav']?>
	<?php endif; ?>
</div>