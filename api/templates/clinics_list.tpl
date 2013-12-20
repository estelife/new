<!--if($list)!-->
	<!--foreach($list as $key=>$val)!-->
		<div class="item company">
			<div class="item-rel">
				<h2><!--if($val.recomended==1)!--><span class="checked"></span><!--endif!--><a href="<!--$val.link!-->" class="el-get-detail"><!--$val.name!--></a></h2>
				<div class="item-in">
					<!--if($val.specialization)!-->
					<p><!--$val.specialization!--></p>
					<!--endif!-->
					<div class="img">
						<div class="img-in">
							<a href="<!--$val.link!-->">
								<!--if($val.logo)!-->
									<!--$val.logo!-->
								<!--else!-->
									<img src="/img/icon/unlogo.png" />
								<!--endif!-->
							</a>
						</div>
					</div>
					<div class="cols col1">
						<span><!--$val.address!--></span>
						<span><!--$val.phone!--></span>
						<a href="#"><a target='_blank' href="<!--$val.web!-->"><!--$val.web_short!--></a></a>
					</div>
				</div>
			</div>
			<div class="border"></div>
		</div>
	<!--endforeach!-->
<!--endif!-->