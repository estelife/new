<!--if($detail)!-->
	<div class="item detail specialist">
		<div class="current">
			<div class="img">
				<div class="img-in">
					<!--if(detail.img)!-->
						<!--$detail.img!-->
					<!--else!-->
						<div class="default">Изображение отсутствует</div>
					<!--endif!-->
				</div>
			</div>
			<!--if($detail.country_name)!-->
			<span class="country c<!--$detail.country_id!-->">Страна: <!--$detail.country_name!--></span>
			<!--endif!-->
			<!--if($detail.clinics)!-->
				<div class="work">
					<h2>Место работы:</h2>
					<ul>
						<!--foreach($detail.clinics as $key=>$val)!-->
							<li><a href="<!--$val.link!-->"><!--$val.name!--><i></i></a></li>
						<!--endforeach!-->
					</ul>
				</div>
			<!--endif!-->
		</div>
		<div class="right">
			<h1><!--$detail.name!--></h1>
			<p><!--$detail.full_description!--></p>
		</div>
		<div class="cl"></div>
		<!--if($detail.activities)!-->
			<h2>Участие в общественных мероприятиях</h2>
			<table>
				<col width="101">
				<col width="409">
				<tr>
					<th>Дата</th>
					<th>Тема доклада</th>
					<th>Место</th>
				</tr>
				<!--foreach ($detail.activities as $key=>$val)!-->
					<tr>
						<td><!--$val.date!--></td>
						<td>
							<!--$val.description!-->
						</td>
						<td>
							<a href="<!--$val.link_event!-->"><!--$val.event_name!--></a>
						</td>
					</tr>
				<!--endforeach!-->
			</table>
		<!--endif!-->
	</div>
<!--else!-->
	<div class="not-found">Специалист не найден ...</div>
<!--endif!-->