<!--if ($PROMOTIONS.list)!-->
<div class="promotions announces">
	<div class="title">
		<!--if($PROMOTIONS.list.active==1)!-->
			<h2><!--$PROMOTIONS.city_t_name!--></h2>
		<!--else!-->
			<h2>Акции клиник</h2>
		<!--endif!-->
		<a href="<!--$PROMOTIONS.link!-->" class="more_promotions">Больше акций</a>
		<a href="#" class="arrow black bottom change_city change_promotions_city"><span><!--$PROMOTIONS.city_name!--></span><i></i></a>
		<div class="cities none promotions_city"></div>
	</div>
	<div class="items">
		<!--if($PROMOTIONS.list.active==1)!-->
			<!--foreach ($PROMOTIONS.list.clinics as $key=>$arValue)!-->
			<div class="item company">
				<div class="item-rel">
					<h2>
						<a href='<!--$arValue.link!-->' class="el-get-detail"><!--$arValue.name!--></a>
						<!--if($arValue.recomended==1)!--><a href="/about/quality-mark.php" class="checked">Знак качества Estelife</a><!--endif!-->
					</h2>
					<div class="item-in">
						<!--if($arValue.specialization)!-->
						<p><!--$arValue.specialization!--></p>
						<!--endif!-->
						<div class="img">
							<div class="img-in">
								<!--if($arValue.logo)!-->
								<!--$arValue.logo!-->
								<!--else!-->
								<div class="default">Изображение отсутствует</div>
								<!--endif!-->
							</div>
						</div>
						<div class="cols col1">
							<span>г. <!--$arValue.city_name!-->, <!--$arValue.address!--></span>
							<span><!--$arValue.phone!--></span>
							<a href="#"><a target='_blank' href="<!--$val.web!-->"><!--$arValue.web_short!--></a></a>
						</div>
					</div>
				</div>
				<div class="border"></div>
			</div>
			<!--endforeach!-->
			<!--else!-->
			<!--foreach($PROMOTIONS.list.akzii as $key=>$val)!-->
			<div class="item promotion">
				<div class="item-rel">
					<!--if($val.view_type!=2)!-->
					<span class="perc"><!--$val.sale!-->%</span>
					<!--endif!-->
					<a href="<!--$val.link!-->">
						<img src="<!--$val.src!-->" alt="<!--$val.name!-->" title="<!--$val.name!-->" />
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
					<a href="/cl<!--$val.clinic_id!-->/" class="clinic-link"><!--$val.clinic_name!--></a>
				</div>
				<div class="border"></div>
			</div>
			<!--endforeach!-->
		<!--endif!-->
	</div>
</div>
<!--endif!-->