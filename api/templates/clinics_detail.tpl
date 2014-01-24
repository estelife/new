<!--if($detail)!-->
	<div class="item detail company">
		<h1>
			<!--if($detail.recomended==1)!--><a href="/about/quality-mark.php" class="checked">Знак качества Estelife</a><!--endif!-->
			<!--$detail.name!-->
		</h1>
		<div class="img">
			<div class="img-in">
				<!--$detail.logo!-->
			</div>
		</div>
		<div class="cols col1">
			<!--if($detail.specializations_string)!-->
			<p>
				<!--$detail.specializations_string!--><i></i>
			</p>
			<!--endif!-->
			<!--if($detail.main_contact)!-->
				<span>г. <!--$detail.main_contact.city!-->, <!--$detail.main_contact.address!--></span>
				<span><!--$detail.main_contact.phone!--></span>
				<a href="<!--$detail.main_contact.web!-->" target="_blank"><!--$detail.main_contact.web_short!--></a>
			<!--endif!-->
		</div>
		<!--if($detail.recomended==1)!-->
			<div class="menu menu_tab">
				<ul>
					<li class="active t1"><a href="#"><span>О клинике</span></a></li>
					<li class="t3"><a href="#"><span>Услуги и цены</span></a></li>
					<li class="t2"><a href="#"><span>Акции</span></a></li>
					<li class="t4"><a href="#"><span>Контакты</span></a></li>
				</ul>
			</div>
			<div class="tabs tab1">
				<!--if($detail.gallery)!-->
					<div class="gallery">
						<div class="gallery-in">
							<!--foreach($detail.gallery as $key=>$val)!-->
							<div class="item">
								<div class="img">
									<img src="<!--$val.original!-->" alt="<!--$val.description!-->" title="<!--$val.description!-->" />
								</div>
								<div class="desc">
									<!--$val.description!-->
								</div>
							</div>
							<!--endforeach!-->
						</div>
						<div class="gallery-desc">
							<!--$val.description!-->
						</div>
						<a href="#" class="arrow left">Назад<i></i></a>
						<a href="#" class="arrow right">Вперед<i></i></a>
					</div>
				<!--endif!-->
				<p><!--$detail.detail_text!--></p>
			</div>
			<div class="tabs tab2 services none">
				<!--foreach($detail.specializations as $key=>$val)!-->
					<!--foreach($detail.service as $k=>$v)!-->
						<!--if($key==$v.s_id)!-->
							<h3><!--$v.ser_name!--></h3>
							<table>
								<!--foreach($detail.con as $kk=>$vv)!-->
									<!--if($k==$vv.ser_id)!-->
										<tr>
											<td><!--$vv.con_name!--></td>
											<td class="prices"><span>от <!--$vv.price_from!--><i></i></span></td>
										</tr>
									<!--endif!-->
								<!--endforeach!-->
							</table>
						<!--endif!-->
					<!--endforeach!-->
				<!--endforeach!-->
			</div>
			<div class="tabs tab3 none">
				<div class="promotions">
					<div class="items">
						<!--if($detail.akzii)!-->
							<!--foreach($detail.akzii as $key=>$val)!-->
								<div class="item promotion">
									<div class="item-rel">
										<!--if($val.view_type!=2)!-->
											<span class="perc"><!--$val.sale!-->%</span>
										<!--endif!-->
										<a href="<!--$val.link!-->">
											<img src="<!--$val.logo!-->" alt="<!--$val.name!-->" title="<!--$val.name!-->" />
										</a>
										<h3><a href="<!--$val.link!-->"><!--$val.name!--></a></h3>
										<div class="cols prices">
											<b>
												<!--if($val.view_type==3)!-->
													скидка <!--$val.sale!-->%
												<!--else!-->
													<!--$val.new_price!--> <i></i>
												<!--endif!-->
											</b>
											<!--if($val.view_type==1)!-->
												<s><!--$val.old_price!--> <i></i></s>
											<!--endif!-->
										</div>
										<div class="cols time">
											<!--$val.time!--> <!--$val.day!-->
											<i></i>
										</div>
									</div>
									<div class="border"></div>
								</div>
							<!--endforeach!-->
						<!--else!-->
							<div class="default">
								<h3>Текущих акций нет</h3>
								<p>На текущий момент Клиника <!--$detail.name!--> не проводит акций.</p>
								<p>Однако, Вы можете оставить нам свой e-mail, и мы с радостью сообщим Вам о запуске новых акций от данной клиники.</p>
								<form name="subscribe" method="post" action="" class="subscribe">
									<div class="field">
										<input type="text" name="email" class="text" placeholder="Ваш e-mail..." />
									</div>
									<div class="field check">
										<input type="checkbox" name="always" checked="true" value="1" id="always" />
										<label for="always">Хочу узнавать обо всех новых акциях, размещаемых на портале</label>
										<input type="hidden" name="type" value="1" />
										<input type="hidden" name="params[id]" value="<!--$detail.id!-->" />
									</div>
									<input type="submit" class="submit" value="Оставить" />
								</form>
							</div>
						<!--endif!-->
					</div>
				</div>
			</div>
			<div class="tabs tab-c tab4 none">
				<!--if ($detail.contacts)!-->
					<!--foreach($detail.contacts as $key=>$val)!-->
						<ul>
							<li>
								<b>Адрес</b>
								<span>м. <!--$val.metro!-->, <!--$val.address!--></span>
							</li>
							<!--if($val.phone)!-->
								<li>
									<b>Телефон</b>
									<span><!--$val.phone!--></span>
								</li>
							<!--endif!-->
							<!--<li>-->
							<!--<b>Режим работы</b>-->
							<!--<span>с 9:00 до 21:00 <i>без выходных</i></span>-->
							<!--</li>-->
							<!--if($val.web)!-->
								<li>
									<b>Сайт клиники</b>
									<a href="<!--$val.web!-->" target="_blank"><!--$val.web_short!--></a>
								</li>
							<!--endif!-->
							<!--if ($val.pays)!-->
								<li>
									<b>Принимают к оплате</b>
									<span><!--$val.pays!--></span>
								</li>
							<!--endif!-->
						</ul>
						<div class="map">
							<span class="lat"><!--$val.lat!--></span>
							<span class="lng"><!--$val.lng!--></span>
						</div>
					<!--endforeach!-->
				<!--endif!-->
			</div>
		<!--else!-->
		<div class="tabs tab-c">
			<div class="info-clinic">
				К сожалению, на данный момент клиника не предоставила нам официальные данные об оказываемых услугах и проводимых акциях.
				<br /><br />
				Если данная организация Вас заинтересовала, предлагаем воспользоваться возможностью быстрого перехода на <a href="<!--$detail.main_contact.web!-->" target="_blank">официальный сайт</a> клиники.
			</div>
		</div>
		<!--endif!-->
	</div>
<!--else!-->
	<div class="not-found">Клиника не найдена ...</div>
<!--endif!-->