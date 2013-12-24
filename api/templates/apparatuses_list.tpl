<!--if($list)!-->
	<!--foreach($list as $key=>$val)!-->
		<div class="item product">
			<div class="item-rel">
				<div class="img">
					<div class="img-in">
						<!--if($val.logo_id)!-->
							<!--$val.logo!-->
						<!--else!-->
							<img src="/img/icon/unlogo.png" />
						<!--endif!-->
					</div>
				</div>
				<div class="cols">
					<h2><a href="<!--$val.link!-->"><!--$val.name!--></a></h2>
					<ul>
						<li class="country c<!--$val.country_id!-->"><!--$val.country_name!--></li>
						<li>Производитель: <a href="<!--$val.company_link!-->"><!--$val.company_name!--></a></li>
					</ul>
					<p><!--$val.preview_text!--></p>
				</div>
			</div>
			<div class="border"></div>
		</div>
	<!--endforeach!-->
<!--else!-->
	<div class="not-found">Аппараты не найдены ...</div>
<!--endif!-->