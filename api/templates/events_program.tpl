<!--if($detail)!-->
	<div class="activity">
		<h1>Программа мероприятия</h1>
		<h2><!--$detail.event.short_name!--></h2>

		<ul class="dates">
			<!--foreach($detail.dates as $nKey=>$arDate)!-->
			<li>
				<a href="/ev<!--$detail.event.id!-->/program/?date=<!--$arDate.format!-->"<!--if($arDate.date==$detail.current.date)!--> class="active"<!--endif!-->>
					<span><!--$arDate.day!--></span> <!--$arDate.month!-->
					<i></i>
				</a>
			</li>
			<!--endforeach!-->
		</ul>

		<div class="items">
			<!--foreach($detail.halls as $nKey=>$arHall)!-->
			<!--if($arHall.activities)!-->
			<div class="item">
				<div class="item-rel">
					<h3><a href="/ev<!--$detail.event.id!-->/<!--$arHall.translit!-->-<!--$detail.current.format!-->/"><!--$arHall.name!--></a></h3>
					<div class="item-in">
						<ul>
							<!--foreach($arHall.activities as $nKey=>$arActivity)!-->
							<li class="<!--if($arActivity.group==1)!-->group<!--else!-->one<!--endif!-->">
								<!--if($arActivity.time)!-->
									<span><!--$arActivity.time.from!--> : <!--$arActivity.time.to!--></span>
								<!--endif!-->
								<!--if($arActivity.theme)!-->
									<b><!--$arActivity.name!--></b>
									<p><!--$arActivity.theme!--></p>
								<!--else!-->
									<p><!--$arActivity.name!--></p>
								<!--endif!-->
							</li>
							<!--endforeach!-->
						</ul>
					</div>
				</div>
				<div class="border"></div>
			</div>
			<!--endif!-->
			<!--endforeach!-->
		</div>
	</div>
<!--else!-->
	<div class="not-found">Программа не найдена ...</div>
<!--endif!-->