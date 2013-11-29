<div class="block el-block el-ajax-detail" rel="action">
	<div class='block-header red'>
		<span><!--$preview_text!--></span>
		<div class="clear"></div>
		<div class='shadow'></div>
	</div>
	<div class="el-ditem el-ditem-h photo_slider">
		<!--if ($photos_count>0) !-->
		<div class="big-photo">
			<!--foreach($photos as $key=>$val)!-->
			<!--$val!-->
			<!--endforeach!-->

			<!--if ($photos_count>1) !-->
			<span class="arrow left el-cm"></span>
			<span class="arrow right el-cm"></span>
			<!--endif!-->
		</div>
		<!--endif!-->
		<br />
		<div>
			<!--$detail_text!-->
		</div>
		<!--if ($prices)!-->
		<h3>Примеры цен</h3>
		<div class="el-prop">
			<ul>
				<!--foreach($prices as $key=>$val)!-->
				<li><!--$val.procedure!--> <s>от <!--$val.old_price!-->р.</s> от <!--$val.new_price!-->р.</li>
				<!--endforeach!-->
			</ul>
		</div>
		<!--endif!-->
		<!--if ($clinics)!-->
		<!--foreach($clinics as $key=>$val)!-->
			<h3><a href="<!--$val.link!-->" target="_blank"><!--$val.clinic_name!--></a></h3>
			<div class="el-all-contacts">
				<!--if ($val.clinic_address) !-->
				<div class="el-col el-prop">
					<h4>Адрес</h4>
					<ul>
						<li>г. <!--$val.city!--> <!--$val.clinic_address!--></li>
					</ul>
				</div>
				<!--endif!-->
				<!--if ($val.phone)!-->
				<div class="el-col el-col-r el-prop">
					<h4>Телефон</h4>
					<ul>
						<li><!--$val.phone!--></li>
					</ul>
				</div>
				<!--endif!-->
				<div class="clear"></div>
			</div>
		<!--endforeach!-->
		<!--endif!-->
	</div>
	<div class='block-header blue'>
		<span class="el-center">Срок действия акции по <!--$end_date!--></span>
		<div class="clear"></div>
		<div class='shadow'></div>
	</div>
</div>