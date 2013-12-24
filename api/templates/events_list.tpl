<!--if($list)!-->
	<!--foreach($list as $key=>$val)!-->
		<div class="item event">
			<div class="item-rel">
				<span class="date"><!--$val.first_date!--></span>
				<h2>
					<a href="<!--$val.link!-->"><!--$val.name!--></a>
				</h2>
				<p><!--$val.full_name!--></p>
				<div class="img">
					<div class="img-in">
						<!-- if($val.logo)!-->
						<img src="<!--$val.logo!-->" title="<!--$val.name!-->" alt="<!--$val.name!-->" />
						<!--endif!-->
					</div>
				</div>

				<ul class="list1">
					<li class="country big k<!--$val.country_id!-->"></li>
					<!--if($val.country_name)!-->
					<li>Место проведения: <b><!--$val.country_name!--><!--if($val.city_name)!-->, г. <!--$val.city_name!--><!--endif!--></b></li>
					<!--endif!-->
					<li>Период проведения: <b><!--$val.first_period.from!-->
							<!--if($val.first_period.to)!-->
							-
							<!--$val.first_period.to!-->
							<!--endif!--></b></li>
				</ul>
				<ul class="list2">
					<li>Формат: <b><!--$val.types!--></b></li>
					<li>Направление: <b><!--$val.directions!--></b></li>
				</ul>
			</div>
			<div class="border"></div>
		</div>
	<!--endforeach!-->
<!--else!-->
	<div class="not-found">События не найдены ...</div>
<!--endif!-->