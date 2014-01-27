<!--if($list)!-->
	<!--foreach($list as $key=>$val)!-->
		<div class="item company sponsor">
			<div class="item-rel">
				<h2><a href="<!--$val.link!-->"><!--$val.name!--></a></h2>
				<div class="item-in">
					<div class="img">
						<div class="img-in">
							<!--if($val.img)!-->
								<!--$val.img!-->
							<!--endif!-->
						</div>
					</div>
					<div class="cols col1">
						<span class="country big k<!--$val.country_id!-->"></span>
						<!--if($val.address)!-->
							<span><!--$val.address!--></span>
						<!--endif!-->
						<!--if($val.web)!-->
							<a target="_blank" href="<!--$val.web!-->"><!--$val.short_web!--></a>
						<!--endif!-->
					</div>
				</div>
			</div>
			<div class="border"></div>
		</div>
	<!--endforeach!-->
<!--else!-->
	<div class="not-found">Организаторы не найдены ...</div>
<!--endif!-->