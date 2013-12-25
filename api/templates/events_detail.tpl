<!--if($detail)!-->
	<div class="item detail event">
		<h1><!--$detail.short_name!--></h1>
		<div class="current">
			<span class="date"><!--$detail.calendar.first_date!--></span>
			<!--$detail.event.full_name!-->
		</div>
		<p>
			Период проведения: <b><!--$detail.calendar.first_period.from!-->
				<!--if($detail.calendar.first_period.to)!-->
				-
				<!--$detail.calendar.first_period.to!-->
				<!--endif!--></b><br>
			Место проведения: <b><!--$detail.country_name!--><!--if($detail.city_name)!-->, г.<!--$detail.city_name!--><!--endif!--><!--if($detail.dop_address)!-->, <!--$detail.dop_address!--><!--endif!--></b><br>
			<!--if($detail.address)!-->
				Адрес проведения: <b><!--$detail.address!--></b><br>
			<!--endif!-->
			<!--if($detail.types)!-->
				Формат: <b><!--$detail.types!--></b><br>
			<!--endif!-->
			<!--if($detail.directions)!-->
				Направление: <b><!--$detail.directions!--></b>
			<!--endif!-->
		</p>
		<h3>Организаторы</h3>
		<ul>
			<!--foreach($detail.org as $key=>$val)!-->
			<li><a href="/sp<!--$val.company_id!-->/" target="_blank"><!--$val.company_name!--></a></li>
			<!--endforeach!-->
		</ul>
		<h3>Описание</h3>
		<p><!--$detail.detail_text!--></p>
		<h3>Контактные данные</h3>
		<p>
			<!--if($detail.main_org)!-->
				Организация: <b><!--$detail.main_org.company_name!--></b><br>
			<!--endif!-->
			<!--if($detail.main_org.full_address)!-->
				Адрес: <b><!--$detail.main_org.full_address!--></b><br>
			<!--endif!-->
			<!--if($detail.contacts.phone)!-->
				Телефон: <b><!--$detail.contacts.phone!--></b><br>
			<!--endif!-->
			<!--if($detail.contacts.fax)!-->
				Факс: <b><!--$detail.contacts.fax!--></b>
			<!--endif!-->
		</p>
		<!--if($detail.contacts.email)!-->
		<p>
			E-mail: <b><!--$detail.contacts.email!--></b>
		</p>
		<!--endif!-->
		<p>
			<!--if($detail.main_org.web)!-->
				Сайт организатора: <a href="<!--$detail.main_org.web!-->" target="_blank"><!--$detail.main_org.short_web!--></a><br>
			<!--endif!-->
			<!--if($detail.dop_web)!-->
				Сайт площадки проведения: <span><a href="<!--$detail.dop_web!-->" target="_blank"><!--$detail.short_dop_web!--></a><br>
			<!--endif!-->
			<!--if ($detail.web)!-->
				Сайт события: <a href="<!--$detail.web!-->" target="_blank"><!--$detail.short_web!--></a>
			<!--endif!-->
		</p>
	</div>
<!--else!-->
	<div class="not-found">Событие не найдено ...</div>
<!--endif!-->