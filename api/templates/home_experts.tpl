<!--if ($EXPERTS.iblock)!-->
	<div class="experts">
		<div class="item-rel">
			<h2><!--$EXPERTS.TITLE!--></h2>
			<!--foreach ($EXPERTS.iblock as $key=>$val)!-->
			<div class="item<!--if($key>0)!--> none<!--endif!-->">
				<div class="user">
					<img src="<!--$val.IMG!-->" alt="<!--$val.NAME!-->" title="<!--$val.NAME!-->" width="190" />
					<b><!--$val.AUTHOR!--></b>
					<i><!--$val.PROFESSION!--></i>
				</div>
				<div class="quote">
					<h3><a href="/ex<!--$val.ID!-->/"><!--$val.NAME!--></a></h3>
					<p><!--$val.PREVIEW_TEXT!--></p>
				</div>
			</div>
			<!--endforeach!-->
			<ul class="menu">
                <!--foreach ($EXPERTS.iblock as $key=>$val)!-->
				    <li class="<!--if($key==0)!--> active<!--endif!-->"><a href="#"><i></i></a></li>
                <!--endforeach!-->
			</ul>
		</div>
		<div class="border"></div>
	</div>
<!--endif!-->