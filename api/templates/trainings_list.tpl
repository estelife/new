<!--if($list)!-->
	<!--foreach($list as $key=>$val)!-->
		<div class="item training">
			<div class="item-rel">
				<h2><a href="<!--$val.link!-->"><!--$val.name!--></a></h2>
				<div class="item-in">
					<div class="img">
						<div class="img-in">
							<!--if($val.logo)!-->
								<!--$val.logo!-->
							<!--endif!-->
						</div>
					</div>
					<p><!--$val.preview_text!--></p>
					Период проведения: <b><!--$val.first_period.from!-->
						<!--if($val.first_period.to)!-->
						-
						<!--$val.first_period.to!-->
						<!--endif!--></b><br>
					Организатор: <a href="<!--$val.company_link!-->" class="link"><!--$val.company_name!--></a>
					<span class="date"><!--$val.first_date!--></span>
				</div>
			</div>
			<div class="border"></div>
		</div>
	<!--endforeach!-->
<!--endif!-->