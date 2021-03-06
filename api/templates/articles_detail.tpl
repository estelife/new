<div itemscope itemtype="http://schema.org/ScholarlyArticle">
<div class="item detail article">
	<meta itemprop="genre" content="<!--$detail.NAME!-->">
	<h1 itemprop="headline"><!--$detail.NAME!--></h1>
	<ul class="stat notlike" data-elid="<!--$detail.LIKES.element_id!-->" data-type="<!--$detail.LIKES.type!-->">
		<!--if ($detail.ACTIVE_FROM)!-->
		<span itemprop="datePublished" hidden="hidden"><!--$detail.DATE!--></span>
			<li class="date"><!--$detail.ACTIVE_FROM!--></li>
		<!--endif!-->
		<li class="likes islike"><!--$detail.LIKES.countLike!--><!--if($detail.LIKES.typeLike==1)!--> и Ваш<!--endif!--><i></i></li>
		<li class="unlikes islike"><!--$detail.LIKES.countDislike!--><!--if($detail.LIKES.typeLike==2)!--> и Ваш<!--endif!--><i></i></li>
	</ul>
	<div class="announce">
		<span itemprop="description" style="display:none;"><!--$detail.DESCRIPTION!--></span>
		<span itemprop="description"><!--$detail.PREVIEW_TEXT!--></span>
	</div>
	<div class="article-img">
		<div class="article-img-in">
			<span itemprop="image" hidden="hidden"><!--$detail.IMG.SRC!--></span>
			<img src="<!--$detail.IMG.SRC!-->" alt="<!--$detail.NAME!-->" title="<!--$detail.NAME!-->">
		</div>
		<!--if($detail.IMG.DESCRIPTION)!-->
			<div class="article-img-desc">
				<!--$detail.IMG.DESCRIPTION!-->
			</div>
		<!--endif!-->
	</div>
	<div itemprop="articleBody"><!--$detail.DETAIL_TEXT!--></div>
	<!--if ($detail.SOURCE)!-->
			<span itemprop="author" itemscope itemtype="http://schema.org/Person">
    					<span itemprop="name" hidden="hidden"><!--$detail.SOURCE!--></span>
					</span>
	<div class="author"><!--$detail.SOURCE!--></div>
	<!--else!-->
		<span itemprop="author" itemscope itemtype="http://schema.org/Person">
    					<span itemprop="name" hidden="hidden">Estelife.Ru</span>
					</span>
		<div class="author">Estelife.Ru</div>
	<!--endif!-->
	<div class="info">
		<ul class="stat" data-elid="<!--$detail.LIKES.element_id!-->" data-type="<!--$detail.LIKES.type!-->">
			<li><a href="#" class="likes islike<!--if($detail.LIKES.typeLike==1)!--> active<!--endif!-->" data-help="Нравится"><!--$detail.LIKES.countLike!--><!--if($detail.LIKES.typeLike==1)!--> и Ваш<!--endif!--><i></i></a></li>
			<li><a href="#" class="unlikes islike<!--if($detail.LIKES.typeLike==2)!--> active<!--endif!-->" data-help="Не нравится"><!--$detail.LIKES.countDislike!--><!--if($detail.LIKES.typeLike==2)!--> и Ваш<!--endif!--><i></i></a></li>
		</ul>
		<div class="social cols repost">
			<span>Поделиться: </span>
			<a href="http://vkontakte.ru/share.php?url=http://estelife.ru/<!--$detail.TYPE!--><!--$detail.ID!-->/" target="_blank" class="vk">ВКонтакте</a>
			<a href="https://www.facebook.com/sharer.php?u=http://estelife.ru/<!--$detail.TYPE!--><!--$detail.ID!-->/" target="_blank" class="fb">Facebook</a>
		</div>
		<div class="author cols">

		</div>
	</div>
</div>

<div class="comments-ajax"></div>

<!--if($same_data)!-->
	<div class="articles">
		<div class="title">
			<h2>Материалы по теме</h2>
		</div>
		<div class="items ">
			<!--foreach($same_data as $key=>$arItem)!-->
			<div class="item article">
				<div class="item-in">
					<img src="<!--$arItem.SRC!-->" alt="<!--$arItem.NAME!-->" title="<!--$arItem.NAME!-->" width="229" height="160" />
					<h3><a href="<!--$arItem.DETAIL_PAGE_URL!-->"><!--$arItem.NAME!--></a></h3>
					<p><!--$arItem.PREVIEW_TEXT!--></p>
				</div>
				<ul class="stat notlike">
					<!--if ($arItem.ACTIVE_FROM)!-->
					<li class="date"><!--$arItem.ACTIVE_FROM!--></li>
					<li class="likes"><!--if($arItem.LIKES.countLike>0)!--><!--$arItem.LIKES.countLike!--><!--else!-->0<!--endif!--><i></i></li>
					<li class="unlikes"><!--if($arItem.LIKES.countDislike>0)!--><!--$arItem.LIKES.countDislike!--><!--else!-->0<!--endif!--><i></i></li>
					<!--endif!-->
				</ul>
			</div>
			<!--endforeach!-->
		</div>
	</div>
<!--endif!-->