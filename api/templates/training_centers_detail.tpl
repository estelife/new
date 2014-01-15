<!--if($detail)!-->
	<div class="item detail company center">
		<h1><!--$detail.name!--></h1>
		<div class="img">
			<div class="img-in">
				<!--if($detail.img)!-->
					<!--$detail.img!-->
				<!--endif!-->
			</div>
		</div>
		<div class="cols col1">
			<!--if($detail.address)!-->
				<span><!--$detail.address!--></span>
			<!--endif!-->
			<!--if($detail.contacts.phone)!-->
				<span><!--$detail.contacts.phone!--></span>
			<!--endif!-->
			<!--if($detail.web)!-->
				<a href="<!--$detail.web!-->"><!--$detail.web_short!--></a>
			<!--endif!-->
		</div>
		<div class="menu menu_tab">
			<ul>
				<li class="active t1"><a href="#"><span>О центре</span></a></li>
				<li class="t2"><a href="#"><span>Текущие семинары</span></a></li>
				<li class="t3"><a href="#"><span>Контакты</span></a></li>
			</ul>
		</div>
		<div class="tabs tab1 ">
			<p><!--$detail.detail_text!--></p>
		</div>
		<div class="tabs tab2 none">
			<div class="items">
				<!--if($detail.events)!-->
					<!--foreach ($detail.events as $key=>$val)!-->
						<div class="item training">
							<div class="item-rel">
								<h2><a href="<!--$val.link!-->"><!--$val.name!--></a></h2>
								<p><!--$val.preview_text!--></p>
								Период проведения: <b><!--$val.first_period.from!-->
									<!--if($val.first_period.to)!-->
									-
									<!--$val.first_period.to!-->
									<!--endif!--></b><br>
								<span class="date"><!--$val.first_date!--></span>
							</div>
							<div class="border"></div>
						</div>
					<!--endforeach!-->
				<!--else!-->
					<div class="default">
						<h3>Текущих семинаров нет</h3>
						<p>На текущий момент учебный центр <!--$detail.name!--> не проводит семинаров.</p>
						<p>Однако, Вы можете оставить нам свой e-mail, и мы с радостью сообщим Вам о запуске новых семинарах от данного учебного центра.</p>
						<form name="subscribe" method="post" action="" class="subscribe">
							<div class="field">
								<input type="text" name="email" class="text" placeholder="Ваш e-mail..." />
							</div>
							<div class="field check">
								<input type="checkbox" name="always" value="1" id="always" />
								<label for="always">Хочу узнавать обо всех новых семинарах, размещаемых на портале</label>
								<input type="hidden" name="type" value="2" />
								<input type="hidden" name="params[id]" value="<!--$detail.id!-->" />
							</div>
							<input type="submit" class="submit" value="Оставить" />
						</form>
					</div>
				<!--endif!-->
			</div>
		</div>
		<div class="tab-c tabs tab3 none">
			<ul>
				<!--if($detail.address)!-->
					<li>
						<b>Адрес</b>
						<span><!--$detail.address!--></span>
					</li>
				<!--endif!-->
				<!--if($detail.contacts.phone)!-->
					<li>
						<b>Телефон</b>
						<span><!--$detail.contacts.phone!--></span>
					</li>
				<!--endif!-->
				<!--if($detail.web)!-->
					<li>
						<b>Сайт учебного центра</b>
						<a href="<!--$detail.web!-->"><!--$detail.web_short!--></a>
					</li>
				<!--endif!-->
				<!--if($detail.contacts.email)!-->
					<li>
						<b>E-mail</b>
						<span><!--$detail.contacts.email!--></span>
					</li>
				<!--endif!-->

			</ul>

			<div class="map">
				<span class="lat"><!--$detail.contacts.lat!--></span>
				<span class="lng"><!--$detail.contacts.lng!--></span>
			</div>
		</div>
	</div>
<!--else!-->
	<div class="not-found">Учебный центр не найден ...</div>
<!--endif!-->