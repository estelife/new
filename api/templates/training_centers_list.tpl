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
						<!--$i=0!-->
						<!--if($address)!-->
						<tr>
							<td><i class='icon address'></i></td>
							<td><!--$address!--></td>
						</tr>
						<!--endif!-->
						<!--if($metro)!-->
						<tr>
							<td><i class='icon metro<!--$city_id!-->'></i></td>
							<td><!--$metro!--></td>
						</tr>
						<!--endif!-->
						<!--if($web)!-->
						<tr>
							<td><i class='icon link'></i></td>
							<td><a target='_blank' href="<!--$web!-->"><!--$short_web!--></a></td>
						</tr>
						<!--endif!-->
						<!--if($phone)!--><tr>
							<td><i class='icon phone'></i></td>
							<td><b><!--$phone!--></b></td>
						</tr>
						<!--endif!-->
					</table>
				</td>
				<td valign="top" >
				</td>
			</tr>
		</table>
		<div class='clear'></div>
	</div>
</div>