<!--if($list)!-->
	<!--foreach($list as $key=>$val)!-->
		<div class="item promotion">
			<div class="item-rel">
				<?!--if($val.view_type!=3)!-->
					<span class="perc"><!--$val.sale!-->%</span>
				<?php endif; ?>
				<a href="<!--$val.link!-->">
					<img src="<!--$val.src!-->" alt="<!--$val.name!-->" title="<!--val.name!-->" />
				</a>
				<h3><a href="<!--$val.link!-->"><!--$val.name!--></a></h3>
				<div class="cols prices">
					<b>
						<!--if($val.view_type==2)!-->
						скидка <!--$val.sale!-->%
						<!--else!-->
						<!--$val.new_price!--> <i></i>
						<!--endif!-->
					</b>
					<!--if($val.view_type==1)!-->
					<s><!--$val.old_price!--> <i></i></s>
					<!--endif!-->
				</div>
				<div class="cols time">
					<!--$val.time!--> <!--$val.day!-->
					<i></i>
				</div>
			</div>
			<div class="border"></div>
		</div>
	<!--endforeach!-->
<!--else!-->
	<div class="not-found">Акции не найдены ...</div>
<!--endif!-->