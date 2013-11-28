<div class="el-ajax-detail">
	<div class="block" rel="clinic">
		<div class='block-header red'>
			<span><!--$name!--></span>
		</div>
		<div class='shadow'></div>
		<div class="el-ditem el-ditem-h">
			<div class="logo el-col">
				<!--if ($img)!-->
					<!--$img!-->
				<!--endif!-->
			</div>
			<div class="el-scroll">
				<div class="el-scroll-in">
					<table><tr>
							<td>
								<ul class="contacts el-col el-ul el-contacts">
									<!--if ($country_name)!-->
									<li><span>Страна</span><span><!--$country_name!--></span><i class="icon" style="background:url('/img/countries/c<!--$country_id!-->.png')"></i></li>
									<!--endif!-->
									<!--if ($web)!-->
									<li><span>Сайт</span><span><a href="<!--$web!-->" target="_blank"><!--$web_short!--></a></span></li>
									<!--endif!-->
								</ul>
							</td>
						</tr></table>
				</div>
			</div>
			<h3>О компании</h3>
			<p><!--$detail_text!--></p>
		</div>
	</div>

	<!--if($production)!-->
	<div class="block" rel="clinic">
		<div class='block-header blue'>
			<span>Продукция</span>
		</div>
		<div class="el-ditem-action production-events production" data-scroll="true">
			<!--foreach ($production as $key=>$val)!-->
			<div class="section big">
				<a href="<!--$val.link!-->">
					<div class="h"><!--$val.name!--></div>
				</a>
				<div class="i">
					<!--if($val.logo_id)!-->
					<a href="<!--$val.link!-->">
						<!--$val.img!-->
					</a>
					<!--endif!-->
				</div>

				<div class="t"><!--$val.preview_text!--></div>

			</div>
			<!--endforeach!-->
			<div class="clear"></div>
		</div>
	</div>
	<!--endif!-->
	<!--if ($training)!-->
	<div class="block" rel="clinic">
		<div class='block-header blue'>
			<span>Обучение</span>
		</div>
		<div class="el-ditem-action training-events production" >
			<!--foreach ($training as $key=>$val)!-->
			<div class="section big">
				<a href="<!--$val.link!-->">
					<div class="h"><!--$val.event_name!--></div>
				</a>
				<div class="if">
					<!--if($val.company_logo)!-->
						<!--$val.img!-->
					<!--else!-->
					<img src ="/bitrix/templates/web20/images/unlogo.png" alt="<!--$val.event_name!-->" />
					<!--endif!-->
				</div>
				<div class="d">
					<table class="data">
						<tbody>
						<!--if($val.calendar)!-->
						<!--foreach ($val.calendar as $key=>$val)!-->
						<!--if ($key<4)!-->
						<tr>
							<td><i class="icon calendar"></i></td>
							<td><!--$val.full_date!--></td>
						</tr>
						<!--endif!-->
						<!--endforeach!-->
						<!--endif!-->
						</tbody>
					</table>
				</div>
				<div class="clear"></div>

				<div class="t">
					<table class="data">
						<!--if ($val.address)!-->
						<tr>
							<td><i class="icon address"></i></td>
							<td><div>г. <!--$val.city!--> <!--$val.address!--></div></td>
						</tr>
						<!--endif!-->
						<!--if ($val.company_name)!-->
						<tr>
							<td><i class="icon company"></i></td>
							<td><div><!--$val.company_name!--></div></td>
						</tr>
						<!--endif!-->
						<!--if ($val.web)!-->
						<tr>
							<td><i class="icon link"></i></td>
							<td><div><a target="_blank" href="<!--$val.web!-->"><!--$val.web!--></a></div></td>
						</tr>
						<!--endif!-->
						<!--if ($val.phone)!-->
						<tr>
							<td><i class="icon phone"></i></td>
							<td><div><b><!--$val.phone!--></b></div></td>
						</tr>
						<!--endif!-->
					</table>
				</div>

			</div>
			<!--endforeach!-->
			<div class="clear"></div>
		</div>
	</div>
	<!--endif!-->
</div>