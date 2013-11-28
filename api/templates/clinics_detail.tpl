<div class="el-ajax-detail">
	<div class="block" rel="clinic">
		<div class='block-header red'>
			<h1><!--$name!--></h1>
		</div>
		<div class='shadow'></div>
		<div class="el-ditem el-ditem-h">
			<div class="logo el-col">
				<!--$logo!-->
			</div>
			<div class="el-scroll next_prev_contact">
				<div class="slider_content">
					<!--foreach($contacts as $key=>$val)!-->
					<div class=" el-scroll-in">
						<table><tr>
								<td>
									<ul class="contacts el-col el-ul el-contacts">
										<!--if ($val.city)!-->
										<li><span>Город</span><span><!--$val.city!--></span></li>
										<!--endif!-->
										<!--if ($val.address)!-->
										<li><span>Адрес</span><span><!--$val.address!--></span></li>
										<!--endif!-->
										<!--if ($val.metro)!-->
										<li><span>Метро</span><span><!--$val.metro!--></span></li>
										<!--endif!-->
										<!--if ($val.phone)!-->
										<li><span>Телефон</span><span><!--$val.phone!--></span></li>
										<!--endif!-->
										<!--if ($val.web)!-->
										<li><span>Сайт</span><span><a href="<!--$val.web!-->" target="_blank"><!--$val.web_short!--></a></span></li>
										<!--endif!-->
									</ul>
								</td>
							</tr></table>
					</div>
					<!--endforeach!-->
				</div>
				<!--if ($count>1)!-->
					<span class="left"></span>
					<span class="right"></span>
				<!--endif!-->
			</div>
			<div class="clear"></div>
			<!--if ($detail_text)!-->
			<h3>О клинике</h3>
			<p><!--$detail_text!--></p>
			<!--endif!-->
			<!--if ($pays)!-->
			<div class="el-prop">
				<h3>Способы оплаты</h3>
				<ul class="el-ul">
					<!--foreach($pays as $key=>$val)!-->
					<li><!--$val.name!--></li>
					<!--endforeach!-->
				</ul>
			</div>
			<!--endif!-->
		</div>
	</div>
	<!--if ($specialization)!-->
	<div class="block" rel="clinic">
		<div class='block-header blue'>
			<span>Услуги</span>
		</div>
		<!--foreach($specialization as $key=>$val)!-->
		<!--$i=0!-->
		<div class="el-ditem">
			<div class="title"><!--$val.s_name!--></div>
			<div class="el-row">
			<!--foreach ($service as $k=>$v)!-->
				<!--if ($i%2 == 0)!-->
					</div>
					<div class="el-row">
				<!--endif!-->
				<!--if ($key==$v.s_id)!-->
				<div class="el-prop el-col">
					<h3><!--$v.ser_name!--></h3>
					<ul class="el-ul">
						<!--foreach ($con as $kk=>$vv)!-->
						<!--if ($k == $vv.ser_id)!-->
						<li><!--$vv.con_name!--></li>
						<!--endif!-->
						<!--endforeach!-->
					</ul>
				</div>
				<!--endif!-->
			<!--$i++!-->
			<!--endforeach!-->
			</div>
			<div class="clear"></div>
		</div>
		<!--endforeach!-->
	</div>
	<!--endif!-->
	<!--if ($akzii)!-->
	<div class="block" rel="clinic">
		<div class='block-header blue'>
			<span>Акции</span>
		</div>
		<div class="el-ditem-action" id="actions" >
			<!--foreach($akzii as $key=>$value)!-->
				<div class="articlee">
					<div class="section big">
						<a href="<!--$value.link!-->">
						<div class="h"><!--$value.name!--></div>
						<!--if($value.logo)!-->
							<img class="photo" src="<!--$value.logo!-->" alt="<!--$value.name!-->" title="<!--$value.name!-->" />
						<!--endif!-->
						<span class="new_price"><!--$value.new_price!--> руб.</span>
						<span class="old_price"><span></span><!--$value.old_price!--> руб.</span>
						<span class="days"><span></span>Осталось <!--$value.time!--> <!--$value.day!--></span>
						<span class="discount"><!--$value.sale!--> %</span>
						</a>
					</div>
				</div>
			<!--endforeach!-->
			<div class="clear"></div>
		</div>
	</div>
	<!--endif!-->
	<!--if($gallery)!-->
	<div class="block" rel="clinic">
		<div class='block-header blue'>
			<span>Фотографии</span>
		</div>
		<div class="dl_item">
			<div class="el-gallery">
				<!--foreach ($gallery as $key=>$val)!-->
				<div class="image">
					<a href="<?=$val.original?>" class="colorbox" rel="clinic" target="_blank" title="<!--$val.description!-->">
						<img src="<?=$val.original?>" alt="<!--$val.description!-->" title="<!--$val.description!-->" />
						<span class="desc"><!--$val.description!--></span>
					</a>
				</div>
				<!--endforeach!-->
			</div>
		</div>
	</div>
	<!--endif!-->
</div>