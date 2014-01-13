<div class="item detail big-font">
	<h1><!--$detail.NAME!--></h1>
	<ul class="stat">
		<!--if ($detail.ACTIVE_FROM)!-->
		<li class="date"><!--$detail.ACTIVE_FROM!--></li>
		<!--endif!-->
	</ul>
	<div class="announce">
		<!--$detail.PREVIEW_TEXT!-->
	</div>
	<div class="article-img">
		<div class="article-img-in">
			<img src="<!--$detail.IMG.SRC!-->" alt="<!--$detail.NAME!-->" title="<!--$detail.NAME!-->">
		</div>
		<div class="article-img-desc">
			<!--$detail.IMG.DESCRIPTION!-->
		</div>
	</div>
	<!--$detail.DETAIL_TEXT!-->
	<div class="info">
		<div class="author cols">
			<!--if ($detail.SOURCE)!-->
			Автор статьи
			<b><!--$detail.SOURCE!--></b>
			<!--endif!-->
		</div>
	</div>
</div>
<!--if($same_data)!-->
<div class="articles">
	<div class="title">
		<h2>Материалы по теме</h2>
	</div>
	<div class="items ">
		<!--foreach($same_data as $key=>$arItem)!-->
		<div class="item article">
			<img src="<!--$arItem.SRC!-->" alt="<!--$arItem.NAME!-->" title="<!--$arItem.NAME!-->" width="229" height="160" />
			<h3><a href="<!--$arItem.DETAIL_PAGE_URL!-->"><!--$arItem.NAME!--></a></h3>
			<p><!--$arItem.PREVIEW_TEXT!--></p>
			<ul class="stat">
				<!--if ($arItem.ACTIVE_FROM)!-->
				<li class="date"><!--$arItem.ACTIVE_FROM!--></li>
				<!--endif!-->
			</ul>
		</div>
		<!--endforeach!-->
	</div>
</div>
<!--endif!-->