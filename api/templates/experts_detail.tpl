<div class="item detail expert">
	<h1><!--$detail.NAME!--></h1>
	<ul class="stat notlike" data-elid="<!--$detail.LIKES.element_id!-->" data-type="<!--$detail.LIKES.type!-->">
		<li class="likes islike"><!--$detail.LIKES.countLike!--><!--if($detail.LIKES.typeLike==1)!--> и Ваш<!--endif!--><i></i></li>
		<li class="unlikes islike"><!--$detail.LIKES.countDislike!--><!--if($detail.LIKES.typeLike==2)!--> и Ваш<!--endif!--><i></i></li>
	</ul>
	<div class="announce">
		<p><!--$detail.PREVIEW_TEXT!--></p>
	</div>
	<div class="theme">
		<!--$detail.ABOUT!-->
	</div>
	<div class="user">
		<div class="img">
			<img src="<!--$detail.IMG.SRC!-->" alt="<!--$detail.AUTOR!-->" title="<!--$detail.AUTOR!-->"/>
		</div>
		<div class="about">
			<p>
				<b><!--$detail.AUTOR!--></b>
				<i><!--$detail.PROFESSION!--></i>
			</p>
		</div>
	</div>
	<!--$detail.DETAIL_TEXT!-->
	<div class="cor">
		<!--if($detail.FIO)!-->
		Корреспондент: <b><!--$detail.FIO!--></b>
		<!--endif!-->
	</div>
	<div class="info">
		<ul class="stat" data-elid="<!--$detail.LIKES.element_id!-->" data-type="<!--$detail.LIKES.type!-->">
			<li><a href="#" class="likes islike<!--if($detail.LIKES.typeLike==1)!--> active<!--endif!-->" data-help="Нравится"><!--$detail.LIKES.countLike!--><!--if($detail.LIKES.typeLike==1)!--> и Ваш<!--endif!--><i></i></a></li>
			<li><a href="#" class="unlikes islike<!--if($detail.LIKES.typeLike==2)!--> active<!--endif!-->" data-help="Не нравится"><!--$detail.LIKES.countDislike!--><!--if($detail.LIKES.typeLike==2)!--> и Ваш<!--endif!--><i></i></a></li>
		</ul>
		<div class="social cols repost">
			<span>Поделиться: </span>
			<a href="http://vkontakte.ru/share.php?url=http://estelife.ru/ex<!--$detail.ID!-->/" target="_blank" class="vk">ВКонтакте</a>
			<a href="https://www.facebook.com/sharer.php?u=http://estelife.ru/ex<!--$detail.ID!-->/" target="_blank" class="fb">Facebook</a>
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