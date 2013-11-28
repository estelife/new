<div class="news-item clinic-list el-item ">
	<div class="item-wrap">
		<b class="clinic-title"><a href="<!-- $link !-->" class="el-get-detail"><!-- $name !--></a></b>
		<b class="news-picture">
			<a href="<!--$link!-->" class="el-get-detail">
			<!-- if ($logo) !-->
			<!--$logo!-->
			<!-- endif !-->
			</a>
		</b>
		<table class="clinic-table float">
			<tbody><tr>
				<td valign="top">
					<table class="data">
						<tbody>
						<!-- if ($country_name) !-->
						<tr>
							<td><i class="icon " style="background:url('/img/countries/c<!-- $country_id !-->.png')"></i></td>
							<td><!-- $country_name !--></td>
						</tr>
						<!-- endif !-->
						<!-- if ($company_name) !-->
						<tr>
							<td><i class="icon company"></i></td>
							<td><a href="<!-- $company_link !-->"><!-- $company_name !--></a></td>
						</tr>
						<!-- endif !-->
						</tbody></table>
				</td>
				<td valign="top" class="about">
					<!-- $preview_text !-->
				</td>
			</tr>
			</tbody></table>
		<div class="clear"></div>
	</div>
</div>