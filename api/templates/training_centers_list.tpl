<!--if($list)!-->
	<!--foreach($list as $key=>$val)!-->
		<div class="item company">
			<div class="item-rel">
				<h2><a href="<!--$val.link!-->"><!--$val.name!--></a></h2>
				<div class="item-in">
					<div class="img">
						<div class="img-in">
							<!-- if($val.logo_id)!-->
								<!--$val.img!-->
							<!--endif!-->
						</div>
					</div>
					<div class="cols col1">
						<!--if($val.address)!-->
							<span><!--$val.address!--></span>
						<!--endif!-->
						<!--if($val.phone)!-->
							<span><!--$val.phone!--></span>
						<!--endif!-->
						<!--if($val.web)!-->
							<a href="<!--$val.web!-->"><!--$val.short_web!--></a>
						<!--endif!-->
					</div>
				</div>
			</div>
			<div class="border"></div>
		</div>
	<!--endforeach!-->
<!--endif!-->