<!--if($list)!-->
	<!--foreach($list as $key=>$val)!-->
		<div class="item company">
			<div class="item-rel">
				<h2>
					<a href="<!--$val.link!-->" class="el-get-detail"><!--$val.name!--></a>
					<!--if($val.recomended==1)!--><span class="checked">Знак качества Estelife</span><!--endif!-->
				</h2>
				<div class="item-in">
					<!--if($val.specialization)!-->
						<p><!--$val.specialization!--></p>
					<!--endif!-->
					<div class="img">
						<div class="img-in">
							<!--if($val.logo)!-->
								<!--$val.logo!-->
							<!--else!-->
								<div class="default">Изображение отсутствует</div>
							<!--endif!-->
						</div>
					</div>
					<div class="cols col1">
						<span>г. <!--$val.city_name!-->, <!--$val.address!--></span>
						<span><!--$val.phone!--></span>
						<a href="#"><a target='_blank' href="<!--$val.web!-->"><!--$val.web_short!--></a></a>
					</div>
				</div>
			</div>
			<div class="border"></div>
		</div>
	<!--endforeach!-->
<!--else!-->
	<div class="not-found">Клиники не найдены ...</div>
<!--endif!-->