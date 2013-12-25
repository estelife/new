<!--if($ARTICLES.iblock)!-->
<div class="articles ">
	<div class="title">
		<h2><!--$ARTICLES.TITLE!--></h2>
		<a href="<!--$ARTICLES.first_section!-->"><!--$ARTICLES.MORE_TITLE!--></a>
	</div>
	<ul class="menu">
		<!--if($ARTICLES.SECTIONS_NAME!--)!-->
			<!--$i=1!-->
			<!--foreach ($ARTICLES.SECTIONS_NAME as $key=>$val)!-->
				<li<!--if ($i==1)!--> class="active"<!--endif!-->><a href="#"><span><!--$val!--></span></a></li>
			<!--$i++!-->
			<!--endforeach!-->
		<!--endif!-->

	</ul>
	<!--foreach ($ARTICLES.iblock as $key=>$arArticle)!-->
	<div class="items<!--if ($ARTICLES.first!=$key)!--> none<!--endif!-->" rel="<!--$arArticle.section!-->">
		<!--foreach ($arArticle.articles as $key=>$val)!-->
		<div class="item article">
			<img src="<!--$val.IMG!-->" alt="<!--$val.NAME!-->" title="<!--$val.NAME!-->" width="227" />
			<h3><a href="<!--$val.DETAIL_URL!-->"><!--$val.NAME!--></a></h3>
			<p><!--$val.PREVIEW_TEXT!--></p>
			<ul class="stat">
				<li class="date"><!--$val.ACTIVE_FROM!--></li>
			</ul>
		</div>
		<!--endforeach!-->
	</div>
	<!--endforeach!-->
</div>
<!--endif!-->