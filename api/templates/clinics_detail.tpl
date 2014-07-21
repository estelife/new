<!--if($detail)!-->
	<div class="item detail company">
		<h1>
			<!--if($detail.recomended==1)!--><a href="/about/quality-mark.php" class="checked">Знак качества Estelife</a><!--endif!-->
			<!--$detail.name!-->
		</h1>
		<div class="img">
			<div class="img-in">
				<!--if($detail.logo)!-->
					<!--$detail.logo!-->
				<!--else!-->
					<div class="default">Изображение отсутствует</div>
				<!--endif!-->
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
			<div class="tabs-menu menu_tab">
				<ul>
					<li class="t1<!--if($detail.CURRENT_TAB==base)!--> active<!--endif!-->"><a href="/cl<!--$detail.id!-->/">О клинике<i></i></a></li>
					<!--if($detail.articles)!-->
					<li class="t4<!--if($detail.CURRENT_TAB==articles)!--> active<!--endif!-->"><a href="/cl<!--$detail.id!-->/articles/">Статьи<i></i></a></li>
					<!--endif!-->

					<li class="t7<!--if($detail.CURRENT_TAB==reviews)!--> active<!--endif!-->"><a href="/cl<!--$detail.id!-->/reviews/">Отзывы<i></i></a></li>
					<li class="t5<!--if($detail.CURRENT_TAB==contacts)!--> active<!--endif!-->"><a href="/cl<!--$detail.id!-->/contacts/">Контакты<i></i></a></li>
				</ul>
			</div>
			<div class="tabs tab1<!--if($detail.CURRENT_TAB!=base)!--> none<!--endif!-->">
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
							<!--if($val.description)!-->
								<!--$val.description!-->
							<!--endif!-->
						</div>
						<a href="#" class="arrow left">Назад<i></i></a>
						<a href="#" class="arrow right">Вперед<i></i></a>
					</div>
				<!--endif!-->
				<p><!--$detail.detail_text!--></p>
			</div>
			<div class="tabs tab2 services<!--if($detail.CURRENT_TAB!=prices)!--> none<!--endif!-->">
				<span>Перечень услуг и цен является ориентировочным и содержит лишь часть полного комплекса процедур и операций, проводимых специалистами клиники.
					Для получения более подробной информации, пожалуйста, позвоните по телефону, указанному в контактных данных.</span>
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
			<div class="tabs tab3<!--if($detail.CURRENT_TAB!=promotions)!--> none<!--endif!-->">
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
											<!--if($val.view_type==3)!-->
												<b class="only-perc">скидка <!--$val.sale!-->%</b>
											<!--else!-->
												<b><!--$val.new_price!--> <i></i></b>
											<!--endif!-->

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
								<p>На текущий момент клиника <!--$detail.name!--> не проводит акций.</p>

							</div>
						<!--endif!-->
					</div>
				</div>
			</div>
			<!--if($detail.articles)!-->
				<div class="tabs tab4<!--if($detail.CURRENT_TAB!=articles)!--> none<!--endif!-->">
					<div class="items">
						<!--foreach($detail.articles as $key=>$val)!-->
							<div class="item article">
								<img src="<!--$val.img!-->" alt="<!--$val.name!-->" title="<!--$val.name!-->">
								<h3><a href="<!--$val.url!-->"><!--$val.name!--></a></h3>
								<p><!--$val.preview!--></p>
								<ul class="stat">
									<li class="date"><!--$val.date!--></li>
									<li class="likes"><!--$val.countLike!--><i></i></li>
									<li class="unlikes"><!--$val.countDislike!--><i></i></li>
								</ul>
							</div>
						<!--endforeach!-->
					</div>
				</div>
			<!--endif!-->

			<div class="tabs tab7<!--if($detail.CURRENT_TAB!=reviews)!--> none<!--endif!-->">
				<div class="reviews"></div>
			</div>

			<div class="tabs tab-c tab5<!--if($detail.CURRENT_TAB!=contacts)!--> none<!--endif!-->">
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