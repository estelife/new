<!--if($detail)!-->
	<div class="hall">
		<h1><!--$detail.event!--></h1>
		<h2>Программа мероприятия</h2>
		<div class="title">
			<div class="date"><!--$detail.date!-->. <!--$detail.hall!--><i></i></div>
			<a href="/events/ev<!--$detail.event_id!-->/program/">Полная программа конгресса</a>
		</div>
		<!--if($detail.sections)!-->
			<!--foreach($detail.sections as $key=>$val)!-->
				<div class="items">
					<!--if($key>0)!-->
						<div class="h">
							<b><!--$val.section_name!--></b>
							<!--if($val.time)!-->
								<span><!--$val.time.from!--> - <!--$val.time.to!--></span>
							<!--endif!-->
							<h3><!--$val.section_theme!--></h3>
						</div>
					<!--endif!-->
					<!--if ($val.activities)!-->
						<div class="item">
							<h4><!--$val.activities.activity_name!--></h4>
							<!--if($val.activities.events)!-->
								<!--foreach($val.activities.events as $k=>$v)!-->
									<div class="user">
										<div class="img">
											<div class="img-in">
												<!--if($v.logo)!-->
													<!--$v.logo!-->
												<!--else!-->
													<div class="default">Изображение отсутствует</div>
												<!--endif!-->
											</div>
										</div>
										<div class="about">
											<div class="about-in">
												<a href="<!--$v.link!-->"><!--$v.name!--></a>
												<span class="country c<!--$v.country_id!-->"><!--$v.country_name!--></span>
												<p><!--$v.description!--></p>
											</div>
										</div>
									</div>
								<!--endforeach!-->
							<!--endif!-->
						</div>
					<!--endif!-->
				</div>
			<!--endforeach!-->
		<!--endif!-->
	</div>
<!--else!-->
	<div class="not-found">Зал не найден ...</div>
<!--endif!-->