<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<form action="<?=$arResult["FORM_ACTION"]?>" name="search">
	<input type="text" class="text" data-action="get_search_history" name="q" placeholder="Поиск по сайту" />
	<input type="hidden" name="serach_id" value="0" />
	<input type="submit" class="submit set_search_history" name="s" value="Найти" />
</form>
