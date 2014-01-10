<div class="search_page">
	<form name="search" class="search" action="/search/" method="get">
		<input type="hidden" name="tags" value="<!--$SEARCH_PAGE.REQUEST.TAGS!-->" />
		<input type="text" class="text" name="q" value="<!--$SEARCH_PAGE.REQUEST.QUERY!-->" placeholder="Поиск по сайту">
		<input type="submit" class="submit" name="go" value="Найти">
		<input type="hidden" name="how" value="<!--if($SEARCH_PAGE.REQUEST.HOW==d)!-->d<!--else!-->r<!--endif!-->" />
	</form>

	<div class="search-founded">
		<!--if($SEARCH_PAGE.SEARCH_COUNT>0)!-->
			<h2>Результаты поиска</h2>
		<!--endif!-->
		<ul class="menu">
			<!--if($SEARCH_PAGE.REQUEST.HOW==d)!-->
			<li><a href="<!--$SEARCH_PAGE.URL!-->&amp;how=r">По релевантности</a></li>
			<li><a href="#" class="active">По дате</a></li>
			<!--else!-->
			<li><a href="#" class="active">По релевантности</a></li>
			<li><a href="<!--$SEARCH_PAGE.URL!-->&amp;how=d">По дате</a></li>
			<!--endif!-->
		</ul>
	</div>
	<!--if($SEARCH_PAGE.SEARCH_COUNT>0)!-->
	<div class="items">
		<!--foreach($SEARCH_PAGE.SEARCH as $key=>$arItem)!-->
		<div class="item search">
			<h3><!--$arItem.TITLE_FORMATED!--></h3>
			<p><!--$arItem.BODY_FORMATED!--></p>
			<span class="date">Изменен: <i><!--$arItem.DATE_CHANGE!--></i></span>
			<!--if($arItem.TAGS)!-->
				<!--foreach($arItem.TAGS as $tk=>$tags)!-->
					<!--if ($tk>0)!-->, <!--endif!-->
					<a href="<!--$tags.URL!-->"><!--$tags.TAG_NAME!--></a>
				<!--endforeach!-->
			<!--endif!-->
			<!--if($arItem.CHAIN_PATH)!-->
				<span class="date">Изменен&nbsp;<!--$arItem.CHAIN_PATH!--></span>
			<!--endif!-->
		</div>
		<!--endforeach!-->
	</div>
	<!--else!-->
	<div class="items">
		<br />
		<div class="not-found">К сожалению, на ваш поисковый запрос ничего не найдено.</div>
	</div>
	<!--endif!-->
</div>