<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="inner">
	<ul class="crumb">
		<li><a href="/">Главная</a></li>
		<li><a href="/">Учебные центры</a></li>
		<li><b><?=$arResult['company']['name']?></b></li>
	</ul>
	<div class="wrap_item">
		<div class="item detail company center">
			<h1><?=$arResult['company']['name']?></h1>
			<div class="img">
				<div class="img-in">
					<?if (!empty($arResult['company']['img'])):?>
						<?=$arResult['company']['img']?>
					<?php endif?>
				</div>
			</div>
			<div class="cols col1">
				<?php if (!empty($arResult['company']['address'])):?>
					<span><?=$arResult['company']['address']?></span>
				<?php endif?>
				<?php if (!empty($arResult['company']['contacts']['phone'])):?>
					<span><?=$arResult['company']['contacts']['phone']?></span>
				<?php endif?>
				<?php if (!empty($arResult['company']['web'])):?>
					<a href="<?=$arResult['company']['web']?>"><?=$arResult['company']['web_short']?></a>
				<?php endif?>
			</div>
			<div class="menu menu_tab">
				<ul>
					<li class="active t1"><a href="#"><span>О центре</span></a></li>
					<li class="t2"><a href="#"><span>Текущие семинары</span></a></li>
					<li class="t3"><a href="#"><span>Контакты</span></a></li>
				</ul>
			</div>
			<div class="tabs tab1 ">
				<p><?=$arResult['company']['detail_text']?></p>
			</div>
			<div class="tabs tab2 none">
				<div class="items">
					<?php if (!empty($arResult['company']['events'])):?>
						<?php foreach ($arResult['company']['events'] as $val):?>
							<div class="item training">
								<div class="item-rel">
									<h2><a href="<?=$val['link']?>"><?=$val['name']?></a></h2>
									<p><?=$val['preview_text']?></p>
									Период проведения: <b><?=$val['first_period']['from']?>
										<?php if(!empty($val['first_period']['to'])):?>
											-
											<?=$val['first_period']['to']?>
										<?php endif; ?></b><br>
									<span class="date"><?=$val["first_date"]?></span>
								</div>
								<div class="border"></div>
							</div>
						<?php endforeach?>
					<?php else:?>
						<div class="default">
							<h3>Текущих семинаров нет</h3>
							<p>На текущий момент учебный центр <?=$arResult['company']['name']?> не проводит семинаров.</p>
							<p>Однако, Вы можете оставить нам свой e-mail, и мы с радостью сообщим Вам о запуске новых семинарах от данного учебного центра.</p>
							<?$APPLICATION->IncludeComponent(
								"estelife:subscribe",
								"",
								array(
									'params'=>array('id'=>$arResult['company']['id'],'city_id'=>$arResult['city_id']),
									'type'=>2,
									'text'=>'Хочу узнавать обо всех новых семинарах, размещаемых на портале'
								)
							)?>
						</div>
					<?php endif?>
				</div>
			</div>
			<div class="tab-c tabs tab3 none">
				<ul>
					<?php if (!empty($arResult['company']['address'])):?>
						<li>
							<b>Адрес</b>
							<span><?=$arResult['company']['address']?></span>
						</li>
					<?php endif?>
					<?php if (!empty($arResult['company']['contacts']['phone'])):?>
					<li>
						<b>Телефон</b>
						<span><?=$arResult['company']['contacts']['phone']?></span>
					</li>
					<?php endif?>
					<?php if (!empty($arResult['company']['web'])):?>
					<li>
						<b>Сайт учебного центра</b>
						<a href="<?=$arResult['company']['web']?>"><?=$arResult['company']['web_short']?></a>
					</li>
					<?php endif?>
					<?php if (!empty($arResult['company']['contacts']['email'])):?>
						<li>
							<b>E-mail</b>
							<span><?=$arResult['company']['contacts']['email']?></span>
						</li>
					<?php endif?>

				</ul>

				<div class="map">
					<span class="lat"><?=$arResult['company']['contacts']['lat']?></span>
					<span class="lng"><?=$arResult['company']['contacts']['lng']?></span>
				</div>
			</div>
		</div>
	</div>
</div>