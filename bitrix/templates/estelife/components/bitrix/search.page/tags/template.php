<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<div class="inner">
	<ul class="crumb">
		<li><a href="/">Главная</a></li>
		<li><b>Поиск</b></li>
	</ul>
	<div class="title">
		<h1>Поиск по сайту</h1>
	</div>
	<form name="search" class="search" action="" method="get">
		<input type="hidden" name="tags" value="<?echo $arResult["REQUEST"]["TAGS"]?>" />
		<input type="text" class="text" name="q" value="<?=$arResult["REQUEST"]["QUERY"]?>" placeholder="Поиск">
		<input type="submit" class="submit" name="go" value="Найти">
		<input type="hidden" name="how" value="<?echo $arResult["REQUEST"]["HOW"]=="d"? "d": "r"?>" />
	</form>

	<div class="search-founded">
		<?if(count($arResult["SEARCH"])>0):?>
			<h2>Результаты поиска</h2>
		<?endif;?>
		<ul class="menu">
			<?if($arResult["REQUEST"]["HOW"]=="d"):?>
				<li><a href="<?=$arResult["URL"]?>&amp;how=r" >По релевантности</a></li>
				<li><a href="#" class="active">По дате</a></li>
			<?else:?>
				<li><a href="#" class="active">По релевантности</a></li>
				<li><a href="<?=$arResult["URL"]?>&amp;how=d" >По дате</a></li>
			<?endif;?>
		</ul>
	</div>
	<?if(count($arResult["SEARCH"])>0):?>
		<div class="items">
			<?foreach($arResult["SEARCH"] as $arItem):?>
				<div class="item search">
					<h3><?echo $arItem["TITLE_FORMATED"]?></h3>
					<p><?echo $arItem["BODY_FORMATED"]?></p>
					<span class="date">Изменен: <i><?=$arItem["DATE_CHANGE"]?></i></span>
					<?if (!empty($arItem["TAGS"]))
					{
						$first = true;
						foreach ($arItem["TAGS"] as $tags):
							if (!$first)
							{
								?>, <?
							}
							?><a href="<?=$tags["URL"]?>"><?=$tags["TAG_NAME"]?></a> <?
							$first = false;
						endforeach;
					}
					?>
					<?if($arItem["CHAIN_PATH"]):?>
					<span class="date"><?=GetMessage("SEARCH_PATH")?>&nbsp;<?=$arItem["CHAIN_PATH"]?></span>
					<?endif;?>
				</div>
			<?endforeach;?>
		</div>
		<?=$arResult["NAV_STRING"]?>
	<?else:?>
		<div class="items">
			<br />
			<div class="not-found"><?=GetMessage("SEARCH_NOTHING_TO_FOUND");?></div>
		</div>
	<?endif;?>

</div>