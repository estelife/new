<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="inner">
	<ul class="crumb">
		<li><a href="/">Главная</a></li>
		<li><a href="/">Раписание обучений</a></li>
		<li><b><?=$arResult['event']['full_name']?></b></li>
	</ul>
	<div class="item detail training">
		<h1><?=$arResult['event']['full_name']?></h1>
		<div class="current">
			Период проведения: <b><?=$arResult['event']['calendar']['first_period']['from']?>
				<?php if(!empty($arResult['event']['calendar']['first_period']['to'])):?>
					-
					<?=$arResult['event']['calendar']['first_period']['to']?>
				<?php endif;?></b>
			<?php if (!empty($arResult['event']['city_name'])):?>
				Город: <b><?=$arResult['event']['city_name']?></b>
			<?php endif?>
			<span class="date"><?=$arResult['event']['calendar']['first_date']?></span>
		</div>
		<p><?=$arResult['event']['detail_text']?></p>
<!--
		<h3>Тренер</h3>
		<div class="user">
			<img src="images/content/user.png">
			<h4>Саромыцкая<br>Алена Николаевна</h4>
			<span>Врач дерматолог, косметолог</span>
			<a href="#">Узать больше</a>
		</div>
-->
		<h3>Организатор</h3>
		<div class="item company">
			<h4><a href="<?=$arResult['event']['main_org']['link']?>"><?=$arResult['event']['main_org']['company_name']?></a></h4>
			<div class="cols">
				<div class="img">
					<div class="img-in">
						<?php if (!empty ($arResult['event']['logo_id'])):?>
							<?=$arResult['event']['img']?>
						<?php endif?>
					</div>
				</div>
				<?php if (!empty($arResult['event']['main_org']['full_address'])):?>
					<div><?=$arResult['event']['main_org']['full_address']?></div>
				<?php endif?>
				<div>
					<?php if (!empty($arResult['event']['contacts']['phone'])):?>
						<?=$arResult['event']['contacts']['phone']?>
					<?php endif?>
					<br />
					<?php if (!empty($arResult['event']['contacts']['fax'])):?>
						<?=$arResult['event']['contacts']['fax']?> (факс)
					<?php endif?>
				</div>
				<?php if (!empty($arResult['event']['contacts']['email'])):?>
					<div><?=$arResult['event']['contacts']['email']?></div>
				<?php endif?>
				<?php if (!empty($arResult['event']['contacts']['web'])):?>
					<a href="<?=$arResult['event']['contacts']['web']?>" class="link"><?=$arResult['event']['contacts']['web_short']?></a>
				<?php endif?>
			</div>
			<div class="map">

			</div>
		</div>
	</div>
</div>