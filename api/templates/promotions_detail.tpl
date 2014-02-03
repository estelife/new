<!--if($detail)!-->
	<div class="item promotion detail">
		<h1><!--$detail.preview_text!--></h1>
		<div class="data">
			<!--if($detail.big_photo)!-->
					<img src="<!--$detail.big_photo.src!-->" alt="<!--if($detail.big_photo.description)!--><!--$detail.big_photo.description!--><!--else!--><!--$detail.preview_text!--><!--endif!-->" title="" />
			<!--endif!-->
			<div class="current">
				<h3><!--$detail.clinic.main.name!--></h3>
				<span class="city">г. <!--$detail.clinic.main.city_name!--></span>
				<!--if($detail.view_type!=2)!-->
					<span class="perc"><!--$detail.base_sale!-->%</span>
				<!--endif!-->
				<div class="cols prices">
					<!--if($detail.view_type!=3)!-->
						<b><!--$detail.new_price!--> <i></i></b>
						<!--if($detail.view_type==1)!-->
							<s><!--$detail.old_price!--> <i></i></s>
						<!--endif!-->
					<!--else!-->
						<b>скидка <!--$detail.base_sale!-->%</b>
					<!--endif!-->
				</div>
				<div class="cols time">
					<!--if($detail.end_date<$detail.now)!-->
						<span class="old-promotion"><b>Акция завершена</b></span>
					<!--else!-->
						<!--$detail.day_count!-->
						<i></i>
						<span>до <!--$detail.end_date_format!--></span>
					<!--endif!-->
				</div>
				<!--if($detail.end_date<$detail.now)!-->
					<a href="<!--$detail.clinic.link!-->" class="more">Действующие акции клиники<span></span></a>
				<!--else!-->
					<!--if($detail.more_information)!-->
						<a href="<!--$detail.more_information!-->" target="_blank" class="more">Подробная информация и цены<span></span></a>
					<!--endif!-->
				<!--endif!-->
			</div>
		</div>

		<!--$detail.detail_text!-->

		<div class="clinic">
			<a href="<!--$detail.clinic.link!-->" class="more"><i></i></a>
			<div class="about">
				<h3><!--$detail.clinic.main.name!--></h3>
				<span>г. <!--$detail.clinic.main.city_name!--></span>
				<span><a href="<!--$detail.clinic.main.web!-->" target="_blank"><!--$detail.clinic.main.web_short!--></a></span>
			</div>
			<h4>Акции проводятся по адресам:</h4>
			<ul class="contacts">
				<li>
					<!--$detail.clinic.main.address!--><br />
					<!--$detail.clinic.main.phone!-->
				</li>
				<!--if($detail.clinic.offices)!-->
					<!--foreach($detail.clinic.offices as $nKey=>$arOffice)!-->
					<li>
						<!--$arOffice.address!--><br />
						<!--$arOffice.phone!-->
					</li>
					<!--endforeach!-->
				<!--endif!-->
			</ul>
			<div class="map">
				<span class="lat"><!--$detail.clinic.main.latitude!--></span>
				<span class="lng"><!--$detail.clinic.main.longitude!--></span>
			</div>
		</div>

		<!--
		<div class="info nobo">
			<div class="social cols">
				<?$APPLICATION->IncludeComponent("estelife:social.estelife","",array());?>
			</div>
		</div>
		-->
	</div>
	<!--if ($detail.similar)!-->
	<div class="similars">
		<div class="title">
			<h2>Похожие акции</h2>
		</div>
		<div class="items">
			<!--foreach($detail.similar as $key=>$arValue)!-->
			<div class="item promotion">
				<div class="item-rel">
					<!--if($arValue.view_type!=2)!-->
					<span class="perc"><!--$arValue.base_sale!-->%</span>
					<!--endif!-->
					<a href="<!--$arValue.link!-->">
						<img src="<!--$arValue.src!-->" alt="<!--$arValue.name!-->" title="<!--$arValue.name!-->" />
					</a>
					<h3><a href="<!--$arValue.link!-->"><!--$arValue.name!--></a></h3>
					<div class="cols prices">
						<!--if($arValue.view_type==3)!-->
						<b class="only-perc">скидка <!--$arValue.base_sale!-->%</b>
						<!--else!-->
						<b><!--$arValue.new_price!--> <i></i></b>
						<!--endif!-->
						<!--if($arValue.view_type==1)!-->
						<s><!--$arValue.old_price!--> <i></i></s>
						<!--endif!-->
					</div>
					<div class="cols time">
						<!--$arValue.time!--> <!--$arValue.day!-->
						<i></i>
					</div>
					<a href="/cl<!--$arValue.clinic_id!-->/" class="clinic-link"><!--$arValue.clinic_name!--></a>
				</div>
				<div class="border"></div>
			</div>
			<!--endforeach!-->
		</div>
	</div>
	<!--endif!-->
<!--else!-->
	<div class="not-found">Акция не найдена ...</div>
<!--endif!-->