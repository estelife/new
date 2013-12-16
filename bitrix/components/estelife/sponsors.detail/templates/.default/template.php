<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="inner">
	<ul class="crumb">
		<li><a href="/">Главная</a></li>
		<li><a href="/sponsors/">Организаторы</a></li>
		<li><b><?=$arResult['company']['name']?></b></li>
	</ul>
	<div class="item detail company sponsor">
		<h1><?=$arResult['company']['name']?></h1>
		<div class="img">
			<div class="img-in">
				<?if (!empty($arResult['company']['img'])):?>
					<?=$arResult['company']['img']?>
				<?php endif?>
			</div>
		</div>
		<div class="cols col1">
			<span class="country big k<?=$arResult['company']['country_id']?>"></span>
			<?php if (!empty($arResult['company']['address'])):?>
				<span><?=$arResult['company']['address']?></span>
			<?php endif?>
			<?php if (!empty($arResult['company']['contacts']['web'])):?>
				<a href="<?=$arResult['company']['contacts']['web']?>"><?=$arResult['company']['contacts']['web_short']?></a>
			<?php endif?>
		</div>
		<div class="cl"></div>
		<p><?=$arResult['company']['detail_text']?></p>
		<h3>Контактные данные</h3>
		<?php if (!empty($arResult['company']['address'])):?>
		<p>
			<b>Адрес</b><br>
			<?=$arResult['company']['address']?>
		</p>
		<?php endif?>
		<?php if (!empty($arResult['company']['contacts']['phone'])):?>
		<p>
			<b>Телефон</b><br>
			<?=$arResult['company']['contacts']['phone']?>
		</p>
		<?php endif?>
		<?php if (!empty($arResult['company']['contacts']['fax'])):?>
		<p>
			<b>Факс</b><br>
			<?=$arResult['company']['contacts']['fax']?>
		</p>
		<?php endif?>
		<?php if (!empty($arResult['company']['contacts']['email'])):?>
		<p>
			<b>E-mail</b><br>
			<?=$arResult['company']['contacts']['email']?>
		</p>
		<?php endif?>
	</div>
</div>