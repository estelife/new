<div class="news-item clinic-list el-item">
	<!-- if ($recomended!=0) !--><span class="checkit"></span><!-- endif !-->
	<div class="item-wrap">
		<h2 class="clinic-title"><a href="<!--$link!-->" class="el-get-detail"><!-- $name !--></a></h2>
		<div class="news-picture">
			<div>
				<a href="<!--$link!-->" class="el-get-detail">
				<!-- if($logo) !-->
					<!--$logo!-->
				<!--endif!-->
				</a>
			</div>
		</div>
		<table class="clinic-table float">
			<tbody><tr>
				<td valign="top">
					<table class="data">
						<tbody><tr>
							<td><i class="icon address"></i></td>
							<td><!-- $address !--></td>
						</tr>
						<!-- if($metro_name) !-->
						<tr>
							<td><i class="icon metro<!-- $city_id !-->"></i></td>
							<td><!-- $metro_name !--></td>
						</tr>
						<!-- endif !-->
						<!-- if($web) !-->
						<tr>
							<td><i class='icon link'></i></td>
							<td><a target='_blank' href="<!-- $web !-->"><!-- $web_short !--></a></td>
						</tr>
						<!-- endif !-->
						<!-- if($phone) !-->
						<tr>
							<td><i class='icon phone'></i></td>
							<td><b><!-- $phone !--></b></td>
						</tr>
						<!-- endif !-->
						</tbody></table>
				</td>
				<td valign="top" class="profs">
					<!-- $dop_text !-->
				</td>
			</tr>
			</tbody></table>
		<div class="clear"></div>
	</div>
</div>