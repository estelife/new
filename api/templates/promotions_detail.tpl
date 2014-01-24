<!--if($detail)!-->
	<div class="item promotion detail">
		<h1><!--$detail.preview_text!--></h1>
		<div class="data">
			<!--if($detail.big_photo)!-->
					<img src="<!--$detail.big_photo.src!-->" alt="<!--if($detail.big_photo.description)!--><!--$detail.big_photo.description!--><!--else!--><!--$detail.preview_text!--><!--endif!-->" title="" />
			<!--endif!-->
			<div class="current">
				<h3><!--$detail.clinic.name!--></h3>
				<span class="city">г. <!--$detail.clinic.city_name!--></span>
				<!--if($detail.view_type!=2)!-->
					<span class="perc"><!--$detail.base_sale!-->%</span>
				<!--endif!-->
				<!--if($detail.view_type!=3)!-->
				<div class="cols prices">
					<b><!--$detail.new_price!--> <i></i></b>
					<!--if($detail.view_type==1)!-->
						<s><!--$detail.old_price!--> <i></i></s>
					<!--endif!-->
				</div>
				<!--endif!-->
				<div class="cols time">
					<!--$detail.day_count!-->
					<i></i>
					<span>до <!--$detail.end_date!--></span>
				</div>
				<!--if($detail.more_information)!-->
					<a href="<!--$detail.more_information!-->" target="_blank" class="more">Подробная информация и цены</a>
				<!--endif!-->
			</div>
		</div>

		<!--$detail.detail_text!-->

		<div class="clinic">
			<a href="<!--$detail.clinic.link!-->" class="more"><i></i></a>
			<div class="about">
				<h3><!--$detail.clinic.name!--></h3>
				<span>г. <!--$detail.clinic.city_name!--></span>
				<span><a href="<!--$detail.clinic.web!-->" target="_blank"><!--$detail.clinic.web_short!--></a></span>
			</div>
			<h4>Акции проводятся по адресам:</h4>
			<ul class="contacts">
				<li>
					<!--$detail.clinic.address!--><br />
					<!--$detail.clinic.phone!-->
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
				<span class="lat"><!--$detail.clinic.latitude!--></span>
				<span class="lng"><!--$detail.clinic.longitude!--></span>
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
			<!--foreach($detail.similar as $key=>$val)!-->
			<div class="item promotion">
				<div class="item-rel">
					<span class="perc"><!--$val.base_sale!-->%</span>
					<a href="<!--$val.link!-->">
						<img src="<!--$val.src!-->" alt="<!--$val.name!-->" title="<!--$val.name!-->" />
					</a>
					<h3><a href="<!--$val.link!-->"><!--$val.name!--></a></h3>
					<div class="cols prices">
						<b><!--$val.new_price!--> <i></i></b>
						<s><!--$val.old_price!--> <i></i></s>
					</div>
					<div class="cols time">
						<!--$val.time!--> <!--$val.day!-->
						<i></i>
					</div>
					<a href="/cl<!--$val.clinic_id!-->/" class="clinic-link"><!--$val.clinic_name!--></a>
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