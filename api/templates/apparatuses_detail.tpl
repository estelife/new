<div class="el-ajax-detail">
	<div class="block" rel="app">
		<div class='block-header red'>
			<span><!--$name!--></span>
		</div>
		<div class='shadow'></div>
		<div class="el-ditem el-ditem-h">
			<div class="logo el-col">
				<!--$img!-->
			</div>
			<div class="el-scroll">
				<div class="el-scroll-in">
					<table>
						<tr>
							<td>
								<ul class="contacts el-col el-ul el-contacts">
									<!--if($country_name)!-->
									<li><span>Страна</span><span><!--$country_name!--></span><i class="icon" style="background:url('/img/countries/c<!--$country_id!-->.png')"></i></li>
									<!--endif!-->
									<!--if($company_name)!-->
									<li><span>Компания</span><span><a href="<!--$company_link!-->"><!--$company_name!--></a></span></li>
									<!--endif!-->
									<!--if($type_name)!-->
									<li><span>Вид препарата</span><span><!--$type_name!--></span></li>
									<!--endif!-->
								</ul>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="clear"></div>
			<!--if($types)!-->
			<div class="el-prop">
				<h3>Типы препарата</h3>
				<ul class="el-ul">
					<!--foreach ($types as $key=>$val)!-->
					<li><!--$val!--></li>
					<!--endforeach!-->
				</ul>
			</div>
			<!--endif!-->
			<div class="el-tab">
				<div>Описание<span class="open"></span></div>
				<p><!--$detail_text!--></p>
			</div>

			<!--if($registration)!-->
			<div class="el-tab">
				<div>Регистрация<span class="close"></span></div>
				<p class="none"><!--$registration!--></p>
			</div>
			<!--endif!-->
			<!--if($action)!-->
			<div class="el-tab">
				<div>Действие<span class="close"></span></div>
				<p class="none"><!--$action!--></p>
			</div>
			<!--endif!-->
			<!--if(func)!-->
			<div class="el-tab">
				<div>Функции<span class="close"></span></div>
				<p class="none"><!--$func!--></p>
			</div>
			<!--endif!-->
			<!--if($undesired)!-->
			<div class="el-tab">
				<div>Побочные действие<span class="close"></span></div>
				<p class="none"><!--$undesired!--></p>
			</div>
			<!--endif!-->
			<!--if($evidence)!-->
			<div class="el-tab">
				<div>Показания<span class="close"></span></div>
				<p class="none"><!--$evidence!--></p>
			</div>
			<!--endif!-->
			<!--if($procedure)!-->
			<div class="el-tab">
				<div>Курс процедур<span class="close"></span></div>
				<p class="none"><!--$procedure!--></p>
			</div>
			<!--endif!-->
			<!--if($contra)!-->
			<div class="el-tab">
				<div>Противопоказания<span class="close"></span></div>
				<p class="none"><!--$contra!--></p>
			</div>
			<!--endif!-->
			<!--if($advantages)!-->
			<div class="el-tab">
				<div>Преимущества<span class="close"></span></div>
				<p class="none"><!--$advantages!--></p>
			</div>
			<!--endif!-->
			<!--if($security)!-->
			<div class="el-tab">
				<div>Безопасность<span class="close"></span></div>
				<p class="none"><!--$security!--></p>
			</div>
			<!--endif!-->
			<!--if($protocol)!-->
			<div class="el-tab">
				<div>Протокол процедуры<span class="close"></span></div>
				<p class="none"><!--$protocol!--></p>
			</div>
			<!--endif!-->
			<!--if($specs)!-->
			<div class="el-tab">
				<div>Технические характеристики<span class="close"></span></div>
				<p class="none"><!--$specs!--></p>
			</div>
			<!--endif!-->
			<!--if($equipment)!-->
			<div class="el-tab">
				<div>Комплектация<span class="close"></span></div>
				<p class="none"><!--$equipment!--></p>
			</div>
			<!--endif!-->

		</div>
	</div>
	<!--if($gallery)!-->
	<div class="block" rel="app">
		<div class='block-header blue'>
			<span>Фотографии до/после</span>
		</div>
		<div class="dl_item">
			<div class="el-gallery">
				<!--foreach ($gallery as $key=>$val)!-->
				<div class="image">
					<a href="<!--$val!-->" class="colorbox" rel="app" target="_blank" title="">
						<img src="<!--$val!-->" alt="" title="" />
						<span class="desc"></span>
					</a>
				</div>
				<!--endforeach!-->
			</div>
		</div>
	</div>
	<!--endif!-->
	<!--if($production)!-->
	<div class="block" rel="clinic">
		<div class='block-header blue'>
			<span>Другие препараты <!--$company_name!--></span>
		</div>
		<div class="el-ditem-action production-events production" >
			<!--foreach ($production as $key=>$value)!-->
			<div class="section big">
				<a href="<!--$value.link!-->">
					<div class="h"><!--$value.name!--></div>
				</a>
				<div class="i">
					<!--if($value.logo_id)!-->
					<!--$value.img!-->
					<!--endif!-->
				</div>

				<div class="t"><!--$value.preview_text!--></div>
			</div>
			<!--endforeach!-->
			<div class="clear"></div>
		</div>
	</div>
	<!--endif!-->
</div>