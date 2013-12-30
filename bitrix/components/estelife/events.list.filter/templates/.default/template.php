<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<form name="events" class="filter" method="get" action="/events/" >
	<div class="title">
		<h4>Поиск событий</h4>
		<!--		<span>Найдено 6 акций</span>-->
	</div>
	<div class="field">
		<label for="name">Наименование</label>
		<input name="name" type="text" value="<?=$_GET['name']?>" class="text"/>
		<span class="block"></span>
	</div>
	<div class="field country">
		<label for="country">Страна</label>
		<select name="country" data-rules="get_city:select[name=city]">
			<option value="">--</option>
			<?php if (!empty($arResult['countries'])):?>
				<?php foreach ($arResult['countries'] as $val):?>
					<option value="<?=$val['ID']?>"<?=($_GET['country']==$val['ID'])? ' selected="true"': '' ?>><?=$val['NAME']?></option>
				<?php endforeach?>
			<?php endif?>
		</select>
		<span class="block"></span>
	</div>
	<div class="field<?=(empty($arResult['cities']) ? ' disabled' : '')?>">
		<label for="city">Город</label>
		<select name="city" >
			<option value="">--</option>
			<?php if (!empty($arResult['cities'])):?>
				<?php foreach ($arResult['cities'] as $val):?>
					<option value="<?=$val['ID']?>"<?=($_GET['city']==$val['ID'] ? ' selected="true"' : '')?>><?=$val['NAME']?></option>
				<?php endforeach?>
			<?php endif?>
		</select>
		<span class="block"></span>
	</div>

	<div class="field">
		<label for="direction">Направление</label>
		<select name="direction" >
			<option value="">--</option>
			<option value="1"<?=($arResult['filter']['direction'] == 1 ? ' selected="true"' : '')?>>Пластическая хирургия</option>
			<option value="2"<?=($arResult['filter']['direction'] == 2 ? ' selected="true"' : '')?>>Косметология</option>
			<option value="4"<?=($arResult['filter']['direction'] == 4 ? ' selected="true"' : '')?>>Дерматология</option>
			<option value="3"<?=($arResult['filter']['direction'] == 3 ? ' selected="true"' : '')?>>Косметика</option>
			<option value="11"<?=($arResult['filter']['direction'] == 11 ? ' selected="true"' : '')?>>Менеджмент</option>
		</select>
		<span class="block"></span>
	</div>

	<div class="field">
		<label for="type">Формат</label>
		<select name="type" >
			<option value="">--</option>
			<option value="1"<?=($arResult['filter']['type'] == 1 ? ' selected="true"' : '')?>>Форум</option>
			<option value="2"<?=($arResult['filter']['type'] == 2 ? ' selected="true"' : '')?>>Выставка</option>
			<option value="4"<?=($arResult['filter']['type'] == 4 ? ' selected="true"' : '')?>>Тренинг</option>
		</select>
		<span class="block"></span>
	</div>

	<div class="field date">
		<label for="date">Период проведения</label>
		<div class="from text date">
			<input type="text" class="text" name="date_from" value="<?=$arResult['filter']['date_from']?>" />
			<i></i>
		</div>
		<div class="to text date">
			<input type="text" class="text" name="date_to" value="<?=$arResult['filter']['date_to']?>" />
			<i></i>
		</div>
		<span class="block"></span>
	</div>

	<input type="submit" value="Найти события" class="submit">
	<a href="/events/" class="clear">Сбросить фильтр</a>
</form>