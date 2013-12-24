<!--if($list)!-->
	<!--foreach($list as $key=>$val)!-->
		<div class="item producer">
			<div class="item-rel">
				<div class="img">
					<div class="img-in">
						<!--if($val.logo_id)!-->
							<!--$val.img!-->
						<!--else!-->
							<img src="/img/icon/unlogo.png" />
						<!--endif!-->
					</div>
				</div>
				<div class="cols">
					<h2><a href="<!--$val.link!-->"><!--$val.name!--></a></h2>
					<ul>
						<li class="country c<!--$val.country_id!-->"><!--$val.country_name!--></li>
						<!--if($val.web)!-->
						<li><a href="<!--$val.web!-->" target="_blank"><!--$val.web_short!--></a></li>
						<!--endif!-->
					</ul>
					<p><!--$val.preview_text!--></p>
				</div>
			</div>
			<div class="border"></div>
		</div>
	<!--endforeach!-->
<!--else!-->
	<div class="not-found">Производители не найдены ...</div>
<!--endif!-->