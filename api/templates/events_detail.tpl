<div class="block el-ajax-detail" rel="event">
	<div class='block-header red'>
		<span><!--$short_name!--></span>
	</div>
	<div class='shadow'></div>
	<div class="el-ditem el-ditem-h">
		<div class="logo el-col">
			<!--if($logo_id)!-->
				<!--$img!-->
			<!--endif!-->
		</div>
		<div class="el-scroll">
			<h2><!--$full_name!--></h2>
			<div class="el-scroll-in">
				<table>
					<tr>
						<td>
							<ul class="contacts el-col el-ul el-contacts">
								<!--if($country_name)!-->
									<li><span>Страна</span><span><!--$country_name!--></span><i class="icon" style="background:url('/img/countries/c<!--$country_id!-->.png')"></i></li>
								<!--endif!-->
								<!--if($city_name)!-->
								<li><span>Город</span><span><!--$city_name!--></span></li>
								<!--endif!-->
								<!--if($web)!-->
								<li><span>Сайт</span><span><a href="<!--$web!-->"><!--$short_web!--></a></span></li>
								<!--endif!-->
							</ul>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<div class="clear"></div>

		<!--if($calendar)!-->
		<div class="el-prop">
			<h3>Даты проведения</h3>
			<ul class="el-ul">
				<!--foreach ($calendar as $key=>$val)!-->
				<li><!--$val.full_date!--></li>
				<!--endforeach!-->
			</ul>
		</div>
		<!--endif!-->
		<h3>Место проведения</h3>
		<!--if($dop_address)!-->
		<p><!--$dop_address!--></p>
		<!--endif!-->
		<!--if($dop_web)!-->
		<p><a href="<!--$dop_web!-->"><!--$short_dop_web!--></a></p>
		<!--endif!-->
		<!--if($address)!-->
			<p><!--$address!--></p>
		<!--endif!-->

		<!--if($org)!-->
		<div class="el-prop">
			<h3>Организаторы</h3>
			<ul class="el-ul">
				<!--foreach ($org as $key=>$val)!-->
				<li><!--$val.company_name!--></li>
				<!--endforeach!-->
			</ul>
		</div>
		<!--endif!-->
		<h3>Описание</h3>
		<p><!--$detail_text!--></p>
		<!--if($contacts)!-->
		<h3>Контактные данные</h3>
		<div class="el-table">
			<table>
				<!--if($main_org)!-->
				<tr>
					<td class="t">Организация:</td>
					<td class="d">
						<!--$main_org.company_name!-->
					</td>
				</tr>
				<!--endif!-->
				<!--if($main_org.full_address)!-->
				<tr>
					<td class="t">Адрес:</td>
					<td class="d">
						<!--$main_org.full_address!-->
					</td>
				</tr>
				<!--endif!-->
				<!--if($contacts.phone)!-->
				<tr>
					<td class="t">Телефон:</td>
					<td class="d">
						<!--$contacts.phone!-->
					</td>
				</tr>
				<!--endif!-->
				<!--if($contacts.fax)!-->
				<tr>
					<td class="t">Факс:</td>
					<td class="d">
						<!--$contacts.fax!-->
					</td>
				</tr>
				<!--endif!-->
				<!--if($contacts.email)!-->
				<tr>
					<td class="t">E-mail:</td>
					<td class="d">
						<!--$contacts.email!-->
					</td>
				</tr>
				<!--endif!-->
				<!--if($main_org.web)!-->
				<tr>
					<td class="t">Официальный сайт:</td>
					<td class="d">
						<a href="<!--$main_org.web!-->"><!--$main_org.short_web!--></a>
					</td>
				</tr>
				<!--endif!-->
			</table>
		</div>
		<!--endif!-->
	</div>
</div>