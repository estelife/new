<!--if($detail)!-->
	<div class="item detail company sponsor">
		<h1><!--$detail.name!--></h1>
		<div class="img">
			<div class="img-in">
				<!--if($detail.img)!-->
					<!--$detail.img!-->
				<!--endif!-->
			</div>
		</div>
		<div class="cols col1">
			<span class="country big k<!--$detail.country_id!-->"></span>
			<!--if($detail.location)!-->
				<span><!--$detail.location!--></span>
			<!--endif!-->
			<!--if($detail.contacts.web)!-->
				<a href="<!--$detail.contacts.web!-->" target="_blank"><!--$detail.contacts.web_short!--></a>
			<!--endif!-->
		</div>
		<div class="cl"></div>
		<p><!--$detail.detail_text!--></p>
		<h3>Контактные данные</h3>
		<!--if($detail.address)!-->
			<p>
				<b>Адрес</b><br>
				<!--$detail.address!-->
			</p>
		<!--endif!-->
		<!--if($detail.contacts.phone)!-->
			<p>
				<b>Телефон</b><br>
				<!--$detail.contacts.phone!-->
			</p>
		<!--endif!-->
		<!--if($detail.contacts.fax)!-->
			<p>
				<b>Факс</b><br>
				<!--$detail.contacts.fax!-->
			</p>
		<!--endif!-->
		<!--if($detail.contacts.email)!-->
			<p>
				<b>E-mail</b><br>
				<!--$detail.contacts.email!-->
			</p>
		<!--endif!-->
	</div>

	<!--if($detail.events)!-->
	<div class="items company-events">
		<div class="title">
			<h3>Мероприятия <!--$detail.name!--></h3>
		</div>
		<div class="items">
			<!--foreach($detail.events as $key=>$val)!-->
				<div class="item event">
					<div class="item-rel">
						<span class="date"><!--$val.first_date!--></span>
						<h2>
							<a href="<!--$val.link!-->"><!--$val.name!--></a>
						</h2>
						<p><!--$val.full_name!--></p>
						<div class="img">
							<div class="img-in">
								<!-- if($val.logo)!-->
								<img src="<!--$val.logo!-->" title="<!--$val.name!-->" alt="<!--$val.name!-->" />
								<!--endif!-->
							</div>
						</div>

						<ul class="list1">
							<!--if($val.country_name)!-->
							<li>Место проведения: <b><!--$val.country_name!--><!--if($val.city_name)!-->, г. <!--$val.city_name!--><!--endif!--></b><img src="/bitrix/templates/estelife/images/countries/k<!--$val.country_id!-->"></li>
							<!--endif!-->
							<li>Период проведения: <b><!--$val.first_period.from!-->
									<!--if($val.first_period.to)!-->
									-
									<!--$val.first_period.to!-->
									<!--endif!--></b></li>
							<li>Формат: <b><!--$val.types!--></b></li>
							<li>Направление: <b><!--$val.directions!--></b></li>
						</ul>
						<div class="cl"></div>
					</div>
					<div class="border"></div>
				</div>
			<!--endforeach!-->
		</div>
	</div>
	<!--endif!-->
<!--else!-->
	<div class="not-found">Оргнизатор не найден ...</div>
<!--endif!-->
