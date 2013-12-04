<div class="content">
	<div class="title">
		<h2>Медиа</h2>
		<!--<a href="#">Смотреть больше</a>-->
	</div>
	<ul class="menu">
		<li class="first"><a href="#" class="get_photos_and_videos" rel="ALL">x</a></li>
		<li><a href="#" class="get_only_photos" rel="ONLY_PHOTO">Только фото</a></li>
		<li><a href="#" class="get_only_videos" rel="ONLY_VIDEO">Только видео</a></li>
	</ul>
	<!--$i=1!-->
	<div class="items">

		<!--foreach($result as $key=>$val)!-->
			<div class="item <!--if($val.IS_VIDEO)!-->video<!--endif!--> <!--if($i%6==0)!--> last <!--endif!-->">
				<!--if($val.IS_VIDEO)!--><span></span><!--endif!-->
				<a href="<!--$val.LINK!-->"><img src="<!--$val.IMG!-->" alt="<!--$val.NAME!-->" title="<!--$val.NAME!-->" width="146px" height="100px" /></a>
				<div class="border"></div>
			</div>
		<!--$i++!-->
		<!--endforeach!-->
	</div>
</div>
