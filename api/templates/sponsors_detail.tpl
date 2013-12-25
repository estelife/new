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
			<!--if($detail.address)!-->
				<span><!--$detail.address!--></span>
			<!--endif!-->
			<!--if($detail.contacts.web)!-->
				<a href="<!--$detail.contacts.web!-->"><!--$detail.contacts.web_short!--></a>
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
<!--else!-->
	<div class="not-found">Оргнизатор не найден ...</div>
<!--endif!-->
