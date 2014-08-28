<!--if($PHOTOGALLERY)!-->
<div class="media">
	<div class="content">
		<div class="title">
			<h2>Медиа</h2>
		</div>
		<ul class="menu">
			<li class="first"><a href="#" class="get_photos_and_videos none" rel="ALL">x</a></li>
			<li><a href="#" class="get_only_photos" rel="ONLY_PHOTO">Только фото</a></li>
			<li><a href="#" class="get_only_videos" rel="ONLY_VIDEO">Только видео</a></li>
		</ul>
		<div class="items">
			<!--$i=0!-->
			<!--foreach ($PHOTOGALLERY as $key=>$val)!-->
			<div class="item<!--if($val.IS_VIDEO)!--> video<!--endif!--> asd <!--if($i%6==0)!--> last<!--endif!-->" data-id="<!--$val.ID!-->">
				<!--if($val.IS_VIDEO)!--><span></span><!--endif!-->
				<img src="<!--$val.IMG!-->" alt="<!--$val.NAME!-->" title="<!--$val.NAME!-->" width="146px" height="100px" />
				<div class="border"></div>
			</div>
			<!--$i++!-->
			<!--endforeach!-->
		</div>
	</div>
</div>
<!--endif!-->