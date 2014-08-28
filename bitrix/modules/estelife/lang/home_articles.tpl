<!--if($ARTICLES.iblock)!-->
<div class="articles ">
	<div class="title">
		<h2><!--$ARTICLES.TITLE!--></h2>
		<ul class="tabs-menu">
			<!--if($ARTICLES.SECTIONS_NAME!--)!-->
			<!--foreach ($ARTICLES.SECTIONS_NAME as $key=>$val)!-->
			<li<!--if ($ARTICLES.first==$val.key)!--> class="active"<!--endif!-->><a href="#"><span><!--$val.value!--></span><i></i></a></li>
			<!--endforeach!-->
			<!--endif!-->

		</ul>
	</div>
	<!--foreach ($ARTICLES.iblock as $key=>$arArticle)!-->
	<div class="items<!--if ($ARTICLES.first!=$arArticle.section_id)!--> none<!--endif!-->" rel="<!--$arArticle.section!-->">
		<!--foreach ($arArticle.articles as $key=>$val)!-->
		<div class="item article">
			<div class="item-in">
				<img src="<!--$val.IMG!-->" alt="<!--$val.NAME!-->" title="<!--$val.NAME!-->" />
				<h3><a href="<!--$val.DETAIL_URL!-->"><!--$val.NAME!--></a></h3>
				<p><!--$val.PREVIEW_TEXT!--></p>
			</div>
			<ul class="stat notlike">
				<li class="date"><!--$val.ACTIVE_FROM!--></li>
				<li class="likes"><!--if($val.LIKES.countLike>0)!--><!--$val.LIKES.countLike!--><!--else!-->0<!--endif!--><i></i></li>
				<li class="unlikes"><!--if($val.LIKES.countDislike>0)!--><!--$val.LIKES.countDislike!--><!--else!-->0<!--endif!--><i></i></li>
			</ul>
		</div>
		<!--endforeach!-->
	</div>
	<!--endforeach!-->
</div>
<!--endif!-->