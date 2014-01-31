<div class="search_page">
	<form name="search" class="search" action="/search/" method="get">
		<input type="hidden" name="tags" value="<!--$list.tags!-->" />
		<input type="text" class="text" name="q" value="<!--$list.query!-->" placeholder="Поиск по сайту">
		<input type="submit" class="submit" name="go" value="Найти">
		<input type="hidden" name="how" value="<!--if($list.how==d)!-->d<!--else!-->r<!--endif!-->" />
	</form>

	<div class="search-founded">
		<!--if($list.result)!-->
			<h2>Результаты поиска</h2>
		<!--endif!-->
		<ul class="menu">
			<!--if($list.how==d)!-->
			<li><a href="<!--$list.sort_url!-->&amp;how=r">По релевантности</a></li>
			<li><a href="#" class="active">По дате</a></li>
			<!--else!-->
			<li><a href="#" class="active">По релевантности</a></li>
			<li><a href="<!--$list.sort_url!-->&amp;how=d">По дате</a></li>
			<!--endif!-->
		</ul>
	</div>
	<!--if($list.result)!-->
		<div class="items">
			<!--foreach($list.result as $key=>$arItem)!-->
			<div class="item search">
				<h3><a href="<!--$arItem.src!-->"><!--$arItem.name!--></a></h3>
				<p><!--$arItem.description!--></p>
				<span class="date">Изменен: <i><!--$arItem.date_edit!--></i></span>
				<!--if($arItem.tags)!-->
					<!--foreach($arItem.tags as $k=>$v)!-->
						<!--if($k!=0)!-->, <!--endif!--><a href="<!--$list.tags_url!-->&amp;tags=<!--$v!-->"><!--$v!--></a>
					<!--endforeach!-->
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