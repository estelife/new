<!--if($list)!-->
	<!--foreach($list as $key=>$val)!-->
		<div class="item specialist">
			<div class="img">
				<div class="img-in">
					<!--if($val.logo)!-->
						<!--$val.logo!-->
					<!--else!-->
						<div class="default">Изображение отсутствует</div>
					<!--endif!-->
				</div>
			</div>
			<h2><a href="<!--$val.link!-->"><!--$val.name!--></a></h2>
			<!--if ($val.country_name)!-->
				<span class="country c<!--$val.country_id!-->"><!--$val.country_name!--></span>
			<!--endif!-->
		</div>
	<!--endforeach!-->
<!--else!-->
	<div class="not-found">Специалисты не найдены ...</div>
<!--endif!-->
