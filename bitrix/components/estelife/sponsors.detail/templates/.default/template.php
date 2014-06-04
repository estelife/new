<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="inner">
	<ul class="crumb">
		<li><a href="/">Главная</a></li>
		<li><a href="/organizers/">Организаторы</a></li>
		<li><b><?=$arResult['company']['name']?></b></li>
	</ul>
	<div class="wrap_item">
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
				<?php if (!empty($arResult['company']['location'])):?>
					<span><?=$arResult['company']['location']?></span>
				<?php endif?>
				<?php if (!empty($arResult['company']['contacts']['web'])):?>
					<a href="<?=$arResult['company']['contacts']['web']?>" target="_blank"><?=$arResult['company']['contacts']['web_short']?></a>
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
	<?php if (!empty($arResult['company']['events'])):?>
		<div class="items company-events">
			<div class="title">
				<h3>Мероприятия <?=$arResult['company']['name']?></h3>
			</div>
			<div class="items">
				<?php foreach ($arResult['company']['events'] as $arEvent):?>
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
									<li>Место проведения: <b><?=$arEvent["country_name"]?><?if (!empty($arEvent["city_name"])):?>, г. <?=$arEvent["city_name"]?><?endif?></b><img src="/bitrix/templates/estelife/images/countries/k<?=$arEvent["country_id"]?>"></li>
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
			</div>
		</div>
	<?php endif?>
</div>