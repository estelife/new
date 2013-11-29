<div class="el-ajax-detail">
	<div class="block" rel="pill">
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
			<!--if($undesired)!-->
			<div class="el-tab">
				<div>Побочные действия<span class="close"></span></div>
				<p class="none"><!--$undesired!--></p>
			</div>
			<!--endif!-->
			<!--if($evidence)!-->
			<div class="el-tab">
				<div>Показания<span class="close"></span></div>
				<p class="none"><!--$evidence!--></p>
			</div>
			<!--endif!-->
			<!--if($structure)!-->
			<div class="el-tab">
				<div>Состав<span class="close"></span></div>
				<p class="none"><!--$structure!--></p>
			</div>
			<!--endif!-->
			<!--if($effect)!-->
			<div class="el-tab">
				<div>Достигаемый эффект<span class="close"></span></div>
				<p class="none"><!--$effect!--></p>
			</div>
			<!--endif!-->
			<!--if($form)!-->
			<div class="el-tab">
				<div>Форма выпуска<span class="close"></span></div>
				<p class="none"><!--$form!--></p>
			</div>
			<!--endif!-->
			<!--if($contra)!-->
			<div class="el-tab">
				<div>Противопоказания<span class="close"></span></div>
				<p class="none"><!--$contra!--></p>
			</div>
			<!--endif!-->
			<!--if($usage)!-->
			<div class="el-tab">
				<div>Способ применения<span class="close"></span></div>
				<p class="none"><!--$usage!--></p>
			</div>
			<!--endif!-->
			<!--if($storage)!-->
			<div class="el-tab">
				<div>Условие хранения<span class="close"></span></div>
				<p class="none"><!--$storage!--></p>
			</div>
			<!--endif!-->
			<!--if($advantages)!-->
			<div class="el-tab">
				<div>Преимущества<span class="close"></span></div>
				<p class="none"><!--$advantages!--></p>
			</div>
			<!--endif!-->
			<!--if($area)!-->
			<div class="el-tab">
				<div>Зона применения<span class="close"></span></div>
				<p class="none"><!--$area!--></p>
			</div>
			<!--endif!-->
			<!--if($security)!-->
			<div class="el-tab">
				<div>Безопасность<span class="close"></span></div>
				<p class="none"><!--$security!--></p>
			</div>
			<!--endif!-->
			<!--if($mix)!-->
			<div class="el-tab">
				<div>Сочетание<span class="close"></span></div>
				<p class="none"><!--$mix!--></p>
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

		</div>
	</div>
	<!--if($gallery)!-->
	<div class="block" rel="pill">
		<div class='block-header blue'>
			<span>Фотографии до/после</span>
		</div>
		<div class="dl_item">
			<div class="el-gallery">
				<!--foreach ($gallery as $key=>$val)!-->
				<div class="image">
					<a href="<!--$val!-->" class="colorbox" rel="pill" target="_blank" title="">
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
			<!--foreach ($production as $key=>$val)!-->
			<div class="section big">
				<a href="<!--$val.link!-->">
					<div class="h"><!--$val.name!--></div>
				</a>
				<div class="i">
					<!--if($val.logo_id)!-->
						<!--$val.img!-->
					<!--endif!-->
				</div>

				<div class="t"><!--$val.preview_text!--></div>
			</div>
			<!--endforeach!-->
			<div class="clear"></div>
		</div>
	</div>
	<!--endif!-->
</div>