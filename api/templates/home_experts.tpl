<!--if ($EXPERTS.iblock)!-->
	<div class="experts">
		<h2><!--$EXPERTS.TITLE!--></h2>
		<!--foreach ($EXPERTS.iblock as $key=>$val)!-->
		<div class="item<!--if($key>0)!--> none<!--endif!-->">
			<div class="user">
				<img src="<!--$val.IMG!-->" alt="<!--$val.NAME!-->" title="<!--$val.NAME!-->" width="146px" />
				<b><!--$val.AUTHOR!--></b>
				<i><!--$val.PROFESSION!--></i>
			</div>
			<div class="quote">
				<h3><!--$val.NAME!--></h3>
				<p><!--$val.PREVIEW_TEXT!--></p>
			</div>
		</div>
		<!--endforeach!-->
		<ul class="menu">
			<li class="active"><a href="#"><i></i></a></li>
			<li><a href="#"><i></i></a></li>
		</ul>
	</div>
<!--endif!-->