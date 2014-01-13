<!--if($detail)!-->
	<div class="item promotion detail">
		<h1><!--$detail.preview_text!--></h1>
		<div class="current">
					<span class="perc">
					<!--if($detail.view_type!=2)!-->
						<!--$detail.base_sale!-->%
					<!--else!-->
						<span><!--$detail.new_price!--> <i></i></span>
					<!--endif!-->
					</span>
			<!--if($detail.view_type==1)!-->
				<div class="cols prices">
					<b><!--$detail.new_price!--> <i></i></b>
					<s><!--$detail.old_price!--> <i></i></s>
				</div>
			<!--endif!-->
			<div class="cols time">
				<!--$detail.day_count!-->
				<i></i>
				<span>до <!--$detail.end_date!--></span>
			</div>
		</div>

		<!--if($detail.big_photo)!-->
			<div class="article-img">
				<div class="article-img-in">
					<img src="<!--$detail.big_photo.src!-->" alt="<!--if($detail.big_photo.description)!--><!--$detail.big_photo.description!--><!--else!--><!--$detail.preview_text!--><!--endif!-->" title="" />
				</div>
				<!--if($detail.big_photo.description)!-->
				<div class="article-img-desc">
					<!--$detail.big_photo.description!-->
				</div>
				<!--endif!-->
			</div>
		<!--endif!-->

		<div class="announce">
			<!--$detail.detail_text!-->
		</div>

		<!--if($detail.clinics)!-->
			<div class="clinic">
				<div class="cols col1">
					<a href="<!--$detail.clinics.link!-->"></a>
				</div>
				<div class="cols col2">
					<h3><!--$detail.clinics.clinic_name!--></h3>
					<span>г. <!--$detail.clinics.city!--> <!--$detail.clinics.clinic_address!--></span>
					<span><!--$detail.clinics.phone!--></span>
				</div>
				<div class="cols col3">
					<a href="<!--$detail.clinics.link!-->" class="more" target="_blank">Подробнее о клинике</a>
				</div>
			</div>
		<!--endif!-->
		<!--
		<div class="info nobo">
			<div class="social cols">
				<?$APPLICATION->IncludeComponent("estelife:social.estelife","",array());?>
			</div>
		</div>
		-->
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
								<h3><!--$val.name!--></h3>
								<div class="cols prices">
									<b><!--$val.new_price!--> <i></i></b>
									<s><!--$val.old_price!--> <i></i></s>
								</div>
								<div class="cols time">
									<!--$val.time!--> <!--$val.day!-->
									<i></i>
								</div>
							</div>
							<div class="border"></div>
						</div>
					<!--endforeach!-->
				</div>
			</div>
		<!--endif!-->
	</div>
<!--else!-->
	<div class="not-found">Акция не найдена ...</div>
<!--endif!-->