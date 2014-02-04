<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="inner">
	<ul class="crumb">
		<li><a href="/">Главная</a></li>
		<li><a href="/clinics/<?if ($arResult['clinic']['main_contact']['city_id']==359):?>?=359<?elseif($arResult['clinic']['main_contact']['city_id']==358):?>?=358<?endif?>">Клиники <?if ($arResult['clinic']['main_contact']['city_id']==359):?>Москвы<?elseif($arResult['clinic']['main_contact']['city_id']==358):?>Санкт-Петербурга<?endif?></a></li>
		<li><b><?=$arResult['clinic']['name']?></b></li>
	</ul>
	<div class="wrap_item">
		<div class="item detail company">
			<h1>
				<?php if ($arResult['clinic']["recomended"] == 1):?><a href="/about/quality-mark.php" class="checked">Знак качества Estelife</a><?php endif?>
				<?=$arResult['clinic']['name']?>
			</h1>
			<div class="img">
				<div class="img-in">
					<?php if(!empty($arResult['clinic']['logo'])):?>
						<?=$arResult['clinic']['logo']?>
					<?php else: ?>
						<div class="default">Изображение отсутствует</div>
					<?php endif; ?>
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
			<?php if ($arResult['clinic']["recomended"] == 1):?>
				<div class="tabs-menu menu_tab">
					<ul>
						<li class="active t1"><a href="#">О клинике<i></i></a></li>
						<li class="t3"><a href="#">Услуги и цены<i></i></a></li>
						<li class="t2"><a href="#">Акции<i></i></a></li>
						<?php if (!empty($arResult['clinic']["articles"])):?>
							<li class="t4"><a href="#">Статьи<i></i></a></li>
						<?php endif?>
						<?php if ($arResult['clinic']["id"]==2):?>
							<li class="t6"><a href="#">Специалисты<i></i></a></li>
						<?php endif?>
						<li class="t5"><a href="#">Контакты<i></i></a></li>
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
								<?php if (!empty($val['description'])):?>
									<?=$val['description']?>
								<?php endif?>
							</div>
							<a href="#" class="arrow left">Назад<i></i></a>
							<a href="#" class="arrow right">Вперед<i></i></a>
						</div>
					<?php endif?>
					<p><?=$arResult['clinic']['detail_text']?></p>
				</div>
				<div class="tabs tab2 services none">
					<span>Перечень услуг и цен является ориентировочным и содержит лишь часть полного комплекса процедур и операций, проводимых специалистами клиники.
					Для получения более подробной информации, пожалуйста, позвоните по телефону, указанному в контактных данных.</span>
					<?php foreach ($arResult['clinic']['specializations'] as $key=>$val):?>
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
											<?php if($arValue['view_type']!=2): ?>
												<span class="perc"><?=$arValue["sale"]?>%</span>
											<?php endif; ?>
											<a href="<?=$arValue['link']?>">
												<img src="<?=$arValue['logo']?>" alt="<?=$arValue['name']?>" title="<?=$arValue['name']?>" />
											</a>
											<h3><a href="<?=$arValue['link']?>"><?=$arValue['name']?></a></h3>
											<div class="cols prices">
												<b>
													<?php if($arValue['view_type']==3): ?>
														скидка <?=$arValue["sale"]?>%
													<?php else: ?>
														<?=$arValue['new_price']?> <i></i>
													<?php endif; ?>
												</b>
												<?php if($arValue['view_type']==1): ?>
													<s><?=$arValue['old_price']?> <i></i></s>
												<?php endif; ?>
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
								<div class="default">
									<h3>Текущих акций нет</h3>
									<p>На текущий момент Клиника <?=$arResult['clinic']['name']?> не проводит акций.</p>
									<p>Однако, Вы можете оставить нам свой e-mail, и мы с радостью сообщим Вам о запуске новых акций от данной клиники.</p>
									<?$APPLICATION->IncludeComponent(
										"estelife:subscribe",
										"",
										array(
											'params'=>array('id'=>$arResult['clinic']['id']),
											'type'=>1,
											'text'=>'Хочу узнавать обо всех новых акциях, размещаемых на портале'
										)
									)?>
								</div>
							<?php endif?>
						</div>
					</div>
				</div>
				<?php if (!empty($arResult['clinic']['articles'])):?>
					<div class="tabs tab4 none">
						<div class="items ">
							<?php foreach ($arResult['clinic']['articles'] as $val):?>

									<div class="item article">
										<img src="<?=$val['img']?>" alt="<?=$val['name']?>" title="<?=$val['name']?>">
										<h3><a href="<?=$val['url']?>"><?=$val['name']?></a></h3>
										<p><?=$val['preview']?></p>
										<ul class="stat">
											<li class="date"><?=$val['date']?></li>
											<li class="likes"><?=$val['countLike']?><i></i></li>
											<li class="unlikes"><?=$val['countDislike']?><i></i></li>
										</ul>
									</div>

							<?php endforeach?>
						</div>
					</div>
				<?php endif?>
				<?php if ($arResult['clinic']['id']==2):?>
					<div class="tabs tab6 none">
						<div class="items ">
							<div class="item article specialist">
								<img src="/bitrix/templates/estelife/images/spec/rib.jpg" alt="Рыбакин Артур Владимирович" title="Рыбакин Артур Владимирович">
								<h3>Рыбакин Артур Владимирович</h3>
								<p>Пластический хирург, главный врач Института красоты СПИК, заведующий отделением эстетической пластической хирургии Института красоты СПИК</p>
							</div>
							<div class="item article specialist">
								<img src="/bitrix/templates/estelife/images/spec/and.jpg" alt="Андреищев Андрей Русланович" title="Андреищев Андрей Русланович">
								<h3>Андреищев Андрей Русланович</h3>
								<p>Пластический и челюстно-лицевой хирург, ортодонт</p>
							</div>
							<div class="item article specialist">
								<img src="/bitrix/templates/estelife/images/spec/arb.jpg" alt="Арбатов Вячеслав Витальевич" title="Арбатов Вячеслав Витальевич">
								<h3>Арбатов Вячеслав Витальевич</h3>
								<p>Пластический хирург</p>
							</div>
							<div class="item article specialist">
								<img src="/bitrix/templates/estelife/images/spec/sok.jpg" alt="Соколов Григорий Никитич" title="Соколов Григорий Никитич">
								<h3>Соколов Григорий Никитич</h3>
								<p>Дерматолог-онколог, зав.отделением лазерной косметологии, к.м.н</p>
							</div>
							<div class="item article specialist">
								<img src="/bitrix/templates/estelife/images/spec/bag.jpg" alt="Багненко Елена Сергеевна" title="Багненко Елена Сергеевна">
								<h3>Багненко Елена Сергеевна</h3>
								<p>Врач дерматолог-косметолог, трихолог, к.м.н</p>
							</div>
							<div class="item article specialist">
								<img src="/bitrix/templates/estelife/images/spec/gin.jpg" alt="Гинтовт Елизавета Алексеевна" title="Гинтовт Елизавета Алексеевна">
								<h3>Гинтовт Елизавета Алексеевна</h3>
								<p>Врач дерматолог-косметолог, к.м.н</p>
							</div>
						</div>
					</div>
				<?php endif?>
				<div class="tabs tab-c tab5 none">
					<?php if (!empty($arResult['clinic']['contacts'])):?>
						<?php foreach ($arResult['clinic']['contacts'] as $val):?>
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
										<a href="<?=$val['web']?>" target="_blank"><?=$val['web_short']?></a>
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
			<?php else:?>
				<div class="tabs tab-c">
					<div class="info-clinic">
						К сожалению, на данный момент клиника не предоставила нам официальные данные об оказываемых услугах и проводимых акциях.
						<br /><br />
						Если данная организация Вас заинтересовала, предлагаем воспользоваться возможностью быстрого перехода на <a href="<?=$arResult['clinic']['main_contact']['web']?>" target="_blank">официальный сайт</a> клиники.
					</div>
				</div>
			<?php endif?>
		</div>
	</div>
</div>