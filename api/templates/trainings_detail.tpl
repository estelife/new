<!--if($detail)!-->
	<div class="item detail training">
		<h1><!--$detail.full_name!--></h1>
		<div class="current">
			Период проведения: <b><!--$detail.calendar.first_period!--></b>
			<!--if($detail.city_name)!-->
			Город: <b><!--$detail.city_name!--></b>
			<!--endif!-->
			<span class="date"><!--$detail.calendar.first_date!--></span>
		</div>
		<p><!--$detail.detail_text!--></p>
		<!--
		<h3>Тренер</h3>
		<div class="user">
			<img src="images/content/user.png">
			<h4>Саромыцкая<br>Алена Николаевна</h4>
			<span>Врач дерматолог, косметолог</span>
			<a href="#">Узать больше</a>
		</div>
		-->
		<h3>Организатор</h3>
		<div class="item company">
			<h4><a href="<!--$detail.company_link!-->"><!--$detail.company_name!--></a></h4>
			<div class="cols">
				<div class="img">
					<div class="img-in">
						<!--if($detail.logo_id)!-->
							<!--$detail.img!-->
						<!--endif!-->
					</div>
				</div>
				<!--if($detail.address)!-->
					<div><!--$detail.address!--></div>
				<!--endif!-->
				<div>
					<!--if($detail.contacts.phone)!-->
						<!--$detail.contacts.phone!-->
					<!--endif!-->
					<br />
					<!--if($detail.contacts.fax)!-->
						<!--$detail.contacts.fax!--> (факс)
					<!--endif!-->
				</div>
				<!--if($detail.contacts.email)!-->
					<div><!--$detail.contacts.email!--></div>
				<!--endif!-->
				<!--if($detail.contacts.web)!-->
					<a href="<$detail.contacts.web!-->" class="link"><!--$detail.contacts.web_short!--></a>
				<!--endif!-->
			</div>
			<div class="map">
				<span class="lat"><!--$detail.contacts.lat!--></span>
				<span class="lng"><!--$detail.contacts.lng!--></span>
			</div>
		</div>
	</div>
<!--else!-->
	<div class="not-found">Семинар не найден ...</div>
<!--endif!-->