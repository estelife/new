<!--foreach($list as $key=>$arItem)!-->
<div class="item article">
	<img src="<!--$arItem.SRC!-->" alt="<!--$arItem.NAME!-->" title="<!--$arItem.NAME!-->" width="229" height="160" />
	<h3><a href="<!--$arItem.DETAIL_PAGE_URL!-->"><!--$arItem.NAME!--></a></h3>
	<p><!--$arItem.PREVIEW_TEXT!--></p>
	<ul class="stat">
		<!--if($arItem.ACTIVE_FROM)!-->
			<li class="date"><!--$arItem.ACTIVE_FROM!--></li>
		<!--endif!-->
	</ul>
</div>
<!--endforeach!-->