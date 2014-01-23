<!--foreach($list as $key=>$arItem)!-->
<div class="item article">
	<img src="<!--$arItem.SRC!-->" alt="<!--$arItem.NAME!-->" title="<!--$arItem.NAME!-->"/>
	<h3><a href="<!--$arItem.DETAIL_PAGE_URL!-->"><!--$arItem.NAME!--></a></h3>
	<p><!--$arItem.PREVIEW_TEXT!--></p>
	<ul class="stat notlike">
		<!--if($arItem.ACTIVE_FROM)!-->
			<li class="date"><!--$arItem.ACTIVE_FROM!--></li>
			<li class="likes"><!--if($arItem.LIKES.countLike>0)!--><!--$arItem.LIKES.countLike!--><!--else!-->0<!--endif!--><i></i></li>
			<li class="unlikes"><!--if($arItem.LIKES.countDislike>0)!--><!--$arItem.LIKES.countDislike!--><!--else!-->0<!--endif!--><i></i></li>
		<!--endif!-->
	</ul>
</div>
<!--endforeach!-->