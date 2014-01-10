<!--if ($PODCASTS.ELEMENTS)!-->
<div class="general-news">
	<div class="title">
		<h1><!--$PODCASTS.SECTION_NAME!--></h1>
		<h2>Точка зрения</h2>
	</div>
	<div class="cols col1">
		<!--if ($PODCASTS.FIRST)!-->
			<div class="img">
				<a href="<!--$PODCASTS.FIRST.DETAIL_URL!-->">
					<img src="<!--$PODCASTS.FIRST.IMG_B!-->" width="393px" height="218px"" alt="<!--$PODCASTS.FIRST.NAME!-->" title="<!--$PODCASTS.FIRST.NAME!-->" />
				</a>
				<div>
					<h3><!--$PODCASTS.FIRST.NAME!--></h3>
				</div>
				<span>1</span>
			</div>
			<!--if($PODCASTS.FIRST.PREVIEW_TEXT_B)!-->
				<a href="<!--$PODCASTS.FIRST.DETAIL_URL!-->" class="text"><!--$PODCASTS.FIRST.PREVIEW_TEXT_B!--></a>
			<!--endif!-->
		<!--endif!-->
	</div>
	<div class="cols col2">
		<!--$i=2!-->
		<!--foreach ($PODCASTS.ELEMENTS as $key=>$val)!-->
		<div class="img">
			<a href="<!--$val.DETAIL_URL!-->">
				<img src="<!--$val.IMG_S!-->" width="143px" height="98px"" alt="<!--$val.NAME!-->" title="<!--$val.NAME!-->" />
			</a>
			<!--if($val.NAME)!-->
				<div><p><!--$val.NAME!--></p></div>
			<!--endif!-->
			<span><!--$i!--></span>
		</div>
		<!--$i++!-->
		<!--endforeach!-->
	</div>
</div>
<!--endif!-->