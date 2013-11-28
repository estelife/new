<div class="news-item clinic-list el-item">
	<div class="item-wrap">
		<h2 class="clinic-title"><a href="<!--$link!-->" class="el-get-detail"><!-- $name !--></a></h2>
		<div class="news-picture">
			<div>
				<a href="<!--$link!-->" class="el-get-detail">
				<!-- if($img) !-->
					<!--$img!-->
				<!--endif!-->
				</a>
			</div>
		</div>
		<table class="clinic-table float">
			<tbody><tr>
				<td valign="top">
					<table class="data">
						<tbody>
						<!-- if($address) !-->
						<tr>
							<td><i class="icon address"></i></td>
							<td><!-- $address !--></td>
						</tr>
						<!-- endif !-->
						<!-- if($web) !-->
						<tr>
							<td><i class='icon link'></i></td>
							<td><a target='_blank' href="<!-- $web !-->"><!-- $short_web !--></a></td>
						</tr>
						<!-- endif !-->
						</tbody></table>
				</td>
				<td valign="top" class="about">
				</td>
			</tr>
			</tbody></table>
		<div class="clear"></div>
	</div>
</div>