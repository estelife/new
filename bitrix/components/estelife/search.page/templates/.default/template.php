<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<div class="inner">
	<ul class="crumb">
		<li><a href="/">Главная</a></li>
		<li><b>Поиск</b></li>
	</ul>
	<div class="title">
		<h1>Поиск по сайту</h1>
	</div>
	<div class="search_page">
		<form name="search" class="search" action="/search/" method="get">
			<input type="hidden" name="tags" value="<?echo $arResult['search']["tags"]?>" />
			<input type="text" class="text" data-action="get_search_history" name="q" value="<?=$arResult['search']["query"]?>" placeholder="Поиск по сайту">
			<input type="submit" class="submit set_search_history" name="go" value="Найти">
			<input type="hidden" name="how" value="<?echo $arResult['search']["how"]=="d"? "d": "r"?>" />
		</form>

		<div class="search-founded">
			<?if(!empty($arResult['search']["result"])):?>
				<h2>Результаты поиска</h2>
			<?endif;?>
			<ul class="menu">
				<?if($arResult['search']["how"]=="d"):?>
					<li><a href="<?=$arResult['search']["sort_url"]?>&amp;how=r" >По релевантности</a></li>
					<li><a href="#" class="active">По дате</a></li>
				<?else:?>
					<li><a href="#" class="active">По релевантности</a></li>
					<li><a href="<?=$arResult['search']["sort_url"]?>&amp;how=d" >По дате</a></li>
				<?endif;?>
			</ul>
		</div>
		<?if(!empty($arResult['search']["result"])):?>
			<div class="items">
				<?foreach($arResult['search']["result"] as $val):?>
					<div class="item search">
						<h3><a href="<?=$val["src"]?>"><?=$val["name"]?></a></h3>
						<p><?=$val["description"]?></p>
						<span class="date">Изменен: <i><?=$val["date_edit"]?></i></span>
						<?=$val['tags']?>
					</div>
				<?endforeach;?>
			</div>
		<?else:?>
			<div class="items">
				<br />
				<div class="not-found">К сожалению, на ваш поисковый запрос ничего не найдено.</div>
			</div>
		<?endif;?>
	</div>
	<?=$arResult["nav"]?>
</div>