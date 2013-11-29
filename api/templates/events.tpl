<div class="news-item clinic-list el-item"><div class='item-wrap'>
		<h2 class='clinic-title'><a href="<!--$link!-->"><!--$name!--></a></h2>
		<div class="full_name"><!--$full_name!--></div>
		<div class='news-picture'>
			<div>
				<a href="<!--$link!-->">
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
							<td><!--$country_name!-->, г. <!--$city_name!-->, </td>
						</tr>

						<!--if ($web)!-->
						<tr>
							<td><i class='icon link'></i></td>
							<td><a target='_blank' href="<!--$web!-->"><!--$web_short!--></a></td>
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
							<td><!--$val.full_date!--></td>
						</tr>
						<!--endforeach!-->
						</tbody>
					</table>
					<!--endif!-->
				</td>
			</tr>
			<tr>
				<td colspan="2" class="preview_table">
					<!--$preview_text!-->
				</td>
			</tr>
		</table>
		<div class='clear'></div>
	</div>
</div>