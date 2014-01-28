<!--if($list)!-->
	<!--foreach($list as $key=>$val)!-->
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
<!--else!-->
	<div class="not-found">Акции не найдены ...</div>
<!--endif!-->