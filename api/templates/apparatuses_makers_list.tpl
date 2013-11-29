<div class="news-item clinic-list el-item">
	<div class='item-wrap'>
		<h2 class='clinic-title'><a href="<!--$link!-->" class="el-get-detail"><!--$name!--></a></h2>
		<div class='news-picture'>
			<div>
				<a href="<!--$link!-->" class="el-get-detail">
				<!--if($logo_id)!-->
					<!--$img!-->
				<!--endif!-->
				</a>
			</div>
		</div>
		<table class='clinic-table float'>
			<tr>
				<td valign="top">
					<table class='data'>
						<!--if($country_name)!-->
						<tr>
							<td><i class='icon' style="background:url('/img/countries/c<!--$country_id!-->.png')"></i></td>
							<td><!--$country_name!--></td>
						</tr>
						<!--endif!-->
						<!--if($web)!-->
						<tr>
							<td><i class='icon link'></i></td>
							<td><a target='_blank' href="<!--$web!-->"><!--$web_short!--></a></td>
						</tr>
						<!--endif!-->
					</table>
				</td>
				<td valign="top" class="about">
					<!--$preview_text!-->
				</td>
			</tr>
		</table>
		<div class='clear'></div>
	</div>
</div>