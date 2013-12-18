<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="inner">
		<ul class="crumb">
			<li><a href="/">Главная</a></li>
			<li><a href="/clinics/<?if ($arResult['clinic']['main_contact']['city_id']==359):?>?=359<?elseif($arResult['clinic']['main_contact']['city_id']==358):?>?=358<?endif?>">Клиники <?if ($arResult['clinic']['main_contact']['city_id']==359):?>Москвы<?elseif($arResult['clinic']['main_contact']['city_id']==358):?>Санкт-Петербурга<?endif?></a></li>
			<li><b><?=$arResult['clinic']['name']?></b></li>
		</ul>
		<div class="item detail company">
			<h1><?=$arResult['clinic']['name']?></h1>
			<div class="img">
				<div class="img-in">
					<?=$arResult['clinic']['logo']?>
				</div>
			</div>

			<div class="cols col1">

				<?php if (!empty($arResult['clinic']['specializations_string'])):?>
					<p>
						<?=$arResult['clinic']['specializations_string']?><i></i>
					</p>
				<?php endif?>

				<?php if (!empty($arResult['clinic']['main_contact'])):?>
					<span>г. <?=$arResult['clinic']['main_contact']['city']?>, <?=$arResult['clinic']['main_contact']['address']?></span>
					<span><?=$arResult['clinic']['main_contact']['phone']?></span>
					<a href="<?=$arResult['clinic']['main_contact']['web']?>" target="_blank"><?=$arResult['clinic']['main_contact']['web_short']?></a>
				<?php endif?>
			</div>
			<div class="menu menu_tab">
				<ul>
					<li class="active t1"><a href="#"><span>О клинике</span></a></li>
					<li class="t3"><a href="#"><span>Услуги и цены</span></a></li>
					<li class="t2"><a href="#"><span>Акции</span></a></li>
					<li class="t4"><a href="#"><span>Контакты</span></a></li>
				</ul>
			</div>
			<div class="tabs tab1">
				<?php if (!empty($arResult['clinic']['gallery'])):?>
					<div class="gallery">
						<div class="gallery-in">
							<?php foreach ($arResult['clinic']['gallery'] as $val):?>
								<div class="item">
									<div class="img">
										<img src="<?=$val['original']?>" alt="<?=$val['description']?>" title="<?=$val['description']?>" />
									</div>
									<div class="desc">
										<?=$val['description']?>
									</div>
								</div>
							<?php endforeach?>
						</div>
						<div class="gallery-desc">
							<?=$val['description']?>
						</div>
						<a href="#" class="arrow left">Назад<i></i></a>
						<a href="#" class="arrow right">Вперед<i></i></a>
					</div>
				<?php endif?>
				<p><?=$arResult['clinic']['detail_text']?></p>
			</div>
			<div class="tabs tab2 none">
				<?php foreach ($arResult['clinic']['specializations'] as $key=>$val):?>
					<h2><?=$val['s_name']?></h2>
					<?php foreach ($arResult['clinic']['service'] as $k=>$v):?>
						<?php if ($key == $v['s_id']):?>
								<h3><?=$v['ser_name']?></h3>
								<table>
									<?php foreach ($arResult['clinic']['con'] as $kk=>$vv):?>
										<?php if ($k == $vv['ser_id']):?>
										<tr>
											<td><?=$vv['con_name']?></td>
											<td class="prices"><span>от <?=$vv['price_from']?><i></i></span></td>
										</tr>
										<?php endif?>
									<?php endforeach?>
								</table>
						<?php endif?>
					<?php endforeach?>
				<?php endforeach?>
			</div>
			<div class="tabs tab3 none">
				<div class="promotions">
					<div class="items">
						<?php if (!empty($arResult['clinic']['akzii'])):?>
							<?php foreach ($arResult['clinic']['akzii'] as $arValue):?>
								<div class="item promotion">
									<div class="item-rel">
										<span class="perc"><?=$arValue['sale']?>%</span>
										<a href="<?=$arValue['link']?>">
											<img src="<?=$arValue['logo']?>" alt="<?=$arValue["name"]?>" title="<?=$arValue["name"]?>" width="227px" height="159px">
										</a>
										<h3><?=$arValue["name"]?></h3>
										<div class="cols prices">
											<b><?=$arValue['new_price']?> <i></i></b>
											<s><?=$arValue['old_price']?> <i></i></s>
										</div>
										<div class="cols time">
											<?=$arValue['time']?> <?=$arValue['day']?>
											<i></i>
										</div>
									</div>
									<div class="border"></div>
								</div>
							<?php endforeach; ?>
						<?php else:?>
							<h2>Нет доступных акций</h2>
						<?php endif?>
					</div>
				</div>
			</div>
			<div class="tabs tab-c tab4 none">
				<?php if (!empty($arResult['clinic']['contacts'])):?>
					<?php foreach ($arResult['clinic']['contacts'] as $val):?>
						<h3><?=$val['name']?></h3>
						<ul>
							<li>
								<b>Адрес</b>
								<span>м. <?=$val['metro']?>, <?=$val['address']?></span>
							</li>
							<?php if (!empty($val['phone'])):?>
								<li>
									<b>Телефон</b>
									<span><?=$val['phone']?></span>
								</li>
							<?php endif?>
							<!--<li>-->
							<!--<b>Режим работы</b>-->
							<!--<span>с 9:00 до 21:00 <i>без выходных</i></span>-->
							<!--</li>-->
							<?php if (!empty($val['web'])):?>
								<li>
									<b>Сайт клиники</b>
									<a href="<?=$val['web']?>"><?=$val['web_short']?></a>
								</li>
							<?php endif?>
							<?php if (!empty($val['pays'])):?>
								<li>
									<b>Принимают к оплате</b>
									<span><?=$val['pays']?></span>
								</li>
							<?php endif?>
						</ul>
						<div class="map">
							<span class="lat"><?=$val['lat']?></span>
							<span class="lng"><?=$val['lng']?></span>
						</div>
					<?php endforeach?>
				<?php endif?>
			</div>
		</div>
</div>