<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="inner">
	<ul class="crumb">
		<li><a href="/">Главная</a></li>
		<li><a href="/events/">Календарь событий</a></li>
		<li><b><?=$arResult['event']['short_name']?></b></li>
	</ul>
	<div class="wrap_item">
		<div class="item detail event">
			<h1><?=$arResult['event']['short_name']?></h1>
			<div class="current">
				<span class="date"><?=$arResult['event']['calendar']['first_date']?></span>
				<?=$arResult['event']['full_name']?>
			</div>
			<p>
				Период проведения: <b><?=$arResult['event']['calendar']['first_period']['from']?>
					<?php if(!empty($arResult['event']['calendar']['first_period']['to'])):?>
						-
						<?=$arResult['event']['calendar']['first_period']['to']?>
					<?php endif;?></b><br>
				Место проведения: <b><?=$arResult['event']['country_name']?><?=(!empty($arResult['event']['city_name']) ? ', г. '.$arResult['event']['city_name'] : '' )?><?=(!empty($arResult['event']['dop_address']) ? ', '.$arResult['event']['dop_address'] : '')?></b><br>
				<?php if (!empty($arResult['event']['address'])):?>
					Адрес проведения: <b><?=$arResult['event']['address']?></b><br>
				<?php endif?>
				<?php if (!empty($arResult['event']['types'])):?>
					Формат: <b><?=$arResult['event']['types']?></b><br>
				<?php endif?>
				<?php if (!empty($arResult['event']['directions'])):?>
				Направление: <b><?=$arResult['event']['directions']?></b>
				<?php endif?>
			</p>
			<h3>Организаторы</h3>
			<ul>
				<?php foreach ($arResult['event']['org'] as $val):?>
					<li><a href="/sp<?=$val['company_id']?>/" target="_blank"><?=$val['company_name']?></a></li>
				<?php endforeach?>
			</ul>
			<h3>Описание</h3>
			<p><?=$arResult['event']['detail_text']?></p>
			<h3>Контактные данные</h3>
			<p>
				<?php if (!empty($arResult['event']['main_org'])):?>
					Организация: <b><?=$arResult['event']['main_org']['company_name']?></b><br>
				<?php endif?>
				<?php if (!empty($arResult['event']['main_org']['full_address'])):?>
					Адрес: <b><?=$arResult['event']['main_org']['full_address']?></b><br>
				<?php endif?>
				<?php if (!empty($arResult['event']['contacts']['phone'])):?>
					Телефон: <b><?=$arResult['event']['contacts']['phone']?></b><br>
				<?php endif?>
				<?php if (!empty($arResult['event']['contacts']['fax'])):?>
					Факс: <b><?=$arResult['event']['contacts']['fax']?></b>
				<?php endif?>
			</p>
			<?php if (!empty($arResult['event']['contacts']['email'])):?>
			<p>
				E-mail: <b><?=$arResult['event']['contacts']['email']?></b>
			</p>
			<?php endif?>
			<p>
				<?php if (!empty($arResult['event']['main_org']['web'])):?>
					Сайт организатора: <a href="<?=$arResult['event']['main_org']['web']?>" target="_blank"><?=$arResult['event']['main_org']['short_web']?></a><br>
				<?php endif?>
				<?php if (!empty($arResult['event']['dop_web'])):?>
					Сайт площадки проведения: <span><a href="<?=$arResult['event']['dop_web']?>" target="_blank"><?=$arResult['event']['short_dop_web']?></a><br>
				<?php endif?>
				<?php if (!empty($arResult['event']['web'])):?>
					Сайт события: <a href="<?=$arResult['event']['web']?>" target="_blank"><?=$arResult['event']['short_web']?></a>
				<?php endif?>
			</p>
		</div>
	</div>
</div>
