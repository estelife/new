<!--if($detail)!-->
	<div class="item detail company">
		<h1>
			<!--if($detail.recomended==1)!--><span class="checked"></span><!--endif!-->
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
						<h2>Нет доступных акций</h2>
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
	</div>
<!--else!-->
	<div class="not-found">Клиника не найдена ...</div>
<!--endif!-->