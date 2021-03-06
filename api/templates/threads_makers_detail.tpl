<!--if($detail)!-->
	<div class="item detail producer">
		<h1><!--$detail.name!--></h1>
		<div class="current">
			<div class="img">
				<div class="img-in">
					<!--$detail.img!-->
				</div>
			</div>
			<ul>
				<!--if($detail.country_name)!-->
					<li class="country c<!--$detail.country_id!-->"><!--$detail.country_name!--></li>
				<!--endif!-->
				<!--if($detail.web)!-->
					<li><a href="<!--$detail.web!-->" target="_blank"><!--$detail.web_short!--></a></li>
				<!--endif!-->
			</ul>
		</div>
		<p><!--$detail.detail_text!--></p>
		<!--if($detail.production)!-->
			<h3>Продукция</h3>
			<div class="items products">
				<!--foreach($detail.production as $key=>$val)!-->
					<div class="item product">
						<div class="item-rel">
							<div class="img">
								<div class="img-in">
									<!--if($val.img)!-->
										<!--$val.img!-->
									<!--else!-->
										<div class="default">Изображение отсутствует</div>
									<!--endif!-->
								</div>
							</div>
							<div class="cols">
								<h4><a href="<!--$val.link!-->"><!--$val.name!--></a></h4>
								<p><!--$val.preview_text!--></p>
							</div>
						</div>
						<div class="border"></div>
					</div>
				<!--endforeach!-->
			</div>
		<!--endif!-->
	</div>
<!--else!-->
	<div class="not-found">Препарат не найден ...</div>
<!--endif!-->