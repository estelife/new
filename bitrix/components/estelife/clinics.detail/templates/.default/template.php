<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();
?>
<div class="inner">
		<ul class="crumb">
			<li><a href="/">Главная</a></li>
			<li><a href="/clinics/">Клиники</a></li>
			<li><b><?=$arResult['clinic']['name']?></b></li>
		</ul>
		<div class="item detail clinic">
			<h1><?=$arResult['clinic']['name']?></h1>
			<?=$arResult['clinic']['logo']?>
			<div class="cols col1">
				<p>Косметология, пластическая хирургия<i></i></p>
				<span>г. Москва, ул.Гарибальди, д.3</span>
				<span>+7 (499) 783-29-61</span>
				<a href="#">www.granta-spb.ru</a>
			</div>
			<div class="menu">
				<ul>
					<li class="active"><a href="#"><span>О клинике</span></a></li>
					<li><a href="#"><span>Услуги и цены</span></a></li>
					<li><a href="#"><span>Акции</span></a></li>
					<li><a href="#"><span>Контакты</span></a></li>
				</ul>
			</div>
			<div class="tabs tab1 none">
				<div class="gallery">
					<div class="gallery-in">
						<img src="images/content/gallery1.png" alt="" title="" />
					</div>
					<div class="gallery-desc">
						Одно слово - Валера
					</div>
					<a href="#" class="arrow left">Назад<i></i></a>
					<a href="#" class="arrow right">Вперед<i></i></a>
				</div>
				<p><?=$arResult['clinic']['detail_text']?></p>
			</div>
			<div class="tabs tab2 none">
				<?php foreach ($arResult['clinic']['specialization'] as $key=>$val):?>
					<h2><?=$val['s_name']?></h2>
					<?php foreach ($arResult['clinic']['service'] as $k=>$v):?>
						<?php if ($key == $v['s_id']):?>
								<h3><?=$v['ser_name']?></h3>
								<table>
									<?php foreach ($arResult['clinic']['con'] as $kk=>$vv):?>
										<?php if ($k == $vv['ser_id']):?>
										<tr>
											<td><?=$vv['con_name']?></td>
											<td class="prices"><span><?=$vv['price_from']?><i></i></span></td>
										</tr>
										<?php endif?>
									<?php endforeach?>
								</table>
						<?php endif?>
					<?php endforeach?>
				<?php endforeach?>
			</div>
			<div class="tabs tab3">
				<div class="promotions">
					<div class="items">
						<?php if (!empty($arResult['clinic']['akzii'])):?>
							<?php foreach ($arResult['clinic']['akzii'] as $arValue):?>
								<div class="item">
									<span class="perc"><?=$arValue['base_sale']?>%</span>
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
							<?php endforeach; ?>
						<?php else:?>
							<h2>Нет доступных акций</h2>
						<?php endif?>
					</div>
				</div>
			</div>
			<div class="tabs tab-c none">
				<ul>
					<li>
						<b>Адрес</b>
						<span>м. Лиговский проспект, ул. Черняховского, д. 53</span>
					</li>
					<li>
						<b>Телефон</b>
						<span>+7 (812) 777-03-60</span>
					</li>
					<li>
						<b>Режим работы</b>
						<span>с 9:00 до 21:00 <i>без выходных</i></span>
					</li>
					<li>
						<b>Сайт клиники</b>
						<a href="#">www.gruzdevclinic.ru</a>
					</li>
					<li>
						<b>Принимают к оплате</b>
						<span>наличные, банковские карты, кредит</span>
					</li>
				</ul>
				<div class="map">

				</div>
			</div>
		</div>
</div>