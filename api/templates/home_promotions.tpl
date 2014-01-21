<!--if ($PROMOTIONS.akzii)!-->
<div class="promotions announces">
	<div class="title">
		<h2>Акции клиник</h2>
		<a href="<!--$PROMOTIONS.link!-->" class="more_promotions">Больше акций</a>
		<a href="#" class="arrow black bottom change_city change_promotions_city"><span><!--$PROMOTIONS.city_name!--></span><i></i></a>
		<div class="cities none promotions_city"></div>
	</div>
	<div class="items">
		<!--foreach ($PROMOTIONS.akzii as $key=>$arValue)!-->
		<div class="item promotion">
			<div class="item-rel">
				<!--if($arValue.view_type!=2)!-->
				<span class="perc"><!--$arValue.sale!-->%</span>
				<!--endif!-->
				<a href="<!--$arValue.link!-->">
					<img src="<!--$arValue.src!-->" alt="<!--$arValue.name!-->" title="<!--$arValue.name!-->" />
				</a>
				<h3><a href="<!--$arValue.link!-->"><!--$arValue.name!--></a></h3>
				<div class="cols prices">
					<b>
						<!--if($arValue.view_type==3)!-->
						скидка <!--$arValue.sale!-->%
						<!--else!-->
							<!--$arValue.new_price!--> <i></i>
						<!--endif!-->
					</b>
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