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
					<option value="<?=$val['ID']?>" <?if($_GET['country'] === $val['ID']) echo " selected";?>><?=$val['NAME']?></option>
				<?php endforeach?>
			<?php endif?>
		</select>
		<span class="block"></span>
	</div>
	<div class="field <?=(empty($arResult['cities']) ? ' disabled' : '')?>">
		<label for="city">Город</label>
		<select name="city" >
			<option value="">--</option>
			<?php if (!empty($arResult['cities'])):?>
				<?php foreach ($arResult['cities'] as $val):?>
					<option value="<?=$val['ID']?>" <?if($_GET['city'] === $val['ID']) echo " selected";?>><?=$val['NAME']?></option>
				<?php endforeach?>
			<?php endif?>
		</select>
		<span class="block"></span>
	</div>

	<div class="field">
		<label for="direction">Направление</label>
		<select name="direction" >
			<option value="">--</option>
			<option value="1" <?if($arResult['filter']['direction'] == 1) echo " selected";?>>Пластическая хирургия</option>
			<option value="2" <?if($arResult['filter']['direction'] == 2) echo " selected";?>>Косметология</option>
			<option value="4" <?if($arResult['filter']['direction'] == 4) echo " selected";?>>Дерматология</option>
			<option value="3" <?if($arResult['filter']['direction'] == 3) echo " selected";?>>Косметика</option>
			<option value="11" <?if($arResult['filter']['direction'] == 11) echo " selected";?>>Менеджмент</option>
		</select>
		<span class="block"></span>
	</div>

	<div class="field">
		<label for="type">Формат</label>
		<select name="type" >
			<option value="">--</option>
			<option value="1" <?if($arResult['filter']['type'] == 1) echo " selected";?>>Форум</option>
			<option value="2" <?if($arResult['filter']['type'] == 2) echo " selected";?>>Выставка</option>
			<option value="4" <?if($arResult['filter']['type'] == 4) echo " selected";?>>Тренинг</option>
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