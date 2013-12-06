<div class="news-item clinic-list el-item"><div class='item-wrap'>
		<h2 class='clinic-title'><a href="<!--$link!-->" class="el-get-detail"><!--$short_name!--></a></h2>
		<div class='news-picture'>
			<div>
				<a href="<!--$link!-->" class="el-get-detail">
				<!--if ($logo)!-->
				<!--$logo!-->
				<!--endif!-->
				</a>
			</div>
		</div>
		<table class='clinic-table float'>
			<tr>
				<td valign="top">
					<table class='data'>
						<tr>
							<td><i class='icon address'></i></td>
							<td>г. <!--$city_name!-->, <!--$address!--></td>
						</tr>

						<tr>
							<td><i class="icon company"></i></td>
							<td><!--$company_name!--></td>
						</tr>

						<!--if ($web)!-->
						<tr>
							<td><i class='icon link'></i></td>
							<td><a target='_blank' href="<!--$web!-->"><!--$web_short!--></a></td>
						</tr>
						<!--endif!-->

						<!--if ($phone)!-->
						<tr>
							<td><i class='icon phone'></i></td>
							<td><b><!--$phone!--></b></td>
						</tr>
						<!--endif!-->
					</table>
				</td>
				<td valign="top" class="profs">
					<!--if ($calendar)!-->
					<table class="data">
						<tbody>
						<!--foreach($calendar as $key=>$val)!-->
						<tr>
							<td class="<!--$key!-->"><i class="icon calendar"></i></td>
							<td>
								<!--$val.from!-->
								<!--if($val.to)!-->
									-
									<!--$val.to!-->
								<!--endif!-->
							</td>
						</tr>
						<!--endforeach!-->
						</tbody>
					</table>
					<!--endif!-->
				</td>
			</tr>
			<tr>
				<td colspan="2" class="preview_table">
				</td>
			</tr>
		</table>
		<div class='clear'></div>
	</div>
</div>