<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<form name="events" class="filter" method="get" action="/events/" >
	<div class="title">
		<h4>Поиск событий</h4>
		<span class="count-result"><?=$arResult['count']?></span>
	</div>
	<div class="field">
		<label for="name">Наименование</label>
		<input name="name" type="text" value="<?=$arResult['filter']['name']?>" class="text"/>
		<span class="block"></span>
	</div>
	<div class="field country">
		<label for="country">Страна</label>
		<select name="country" data-rules="get_city:select[name=city]">
			<option value="all">--</option>
			<?php if (!empty($arResult['countries'])):?>
				<?php foreach ($arResult['countries'] as $val):?>
					<option value="<?=$val['ID']?>"<?=($arResult['filter']['country']==$val['ID'])? ' selected="true"': '' ?>><?=$val['NAME']?></option>
				<?php endforeach?>
			<?php endif?>
		</select>
		<span class="block"></span>
	</div>
	<div class="field<?=(empty($arResult['cities']) ? ' disabled' : '')?>">
		<label for="city">Город</label>
		<select name="city" >
			<option value="all">--</option>
			<?php if (!empty($arResult['cities'])):?>
				<?php foreach ($arResult['cities'] as $val):?>
					<option value="<?=$val['ID']?>"<?=($arResult['filter']['city']==$val['ID'] ? ' selected="true"' : '')?>><?=$val['NAME']?></option>
				<?php endforeach?>
			<?php endif?>
		</select>
		<span class="block"></span>
	</div>

	<div class="field">
		<label for="direction" class="checkbox-label">Направление</label>
		<input type="checkbox" name="direction[]" value="1" title="Пластическая хирургия"<?=(in_array(1,$arResult['filter']['direction']) ? ' checked="true"' : '')?> />
		<input type="checkbox" name="direction[]" value="2" title="Косметология"<?=(in_array(2,$arResult['filter']['direction']) ? ' checked="true"' : '')?> />
		<input type="checkbox" name="direction[]" value="4" title="Дерматология"<?=(in_array(4,$arResult['filter']['direction']) ? ' checked="true"' : '')?> />
		<input type="checkbox" name="direction[]" value="3" title="Косметика"<?=(in_array(3,$arResult['filter']['direction']) ? ' checked="true"' : '')?> />
		<input type="checkbox" name="direction[]" value="11" title="Менеджмент"<?=(in_array(11,$arResult['filter']['direction']) ? ' checked="true"' : '')?> />
		<span class="block"></span>
	</div>

	<div class="field">
		<label for="type" class="checkbox-label">Формат</label>
		<input type="checkbox" name="type[]" value="1" title="Форум"<?=(in_array(1,$arResult['filter']['type']) ? ' checked="true"' : '')?> />
		<span class="help-icon">Помощь <p>Мероприятия дискуссионного характера: форумы, конгрессы, саммиты, симпозиумы, съезды, конференции, собрания, семинары, мастер-классы.</p></span>
		<input type="checkbox" name="type[]" value="2" title="Выставка"<?=(in_array(2,$arResult['filter']['type']) ? ' checked="true"' : '')?> />
		<span class="help-icon">Помощь <p>Мероприятия демонстрационного характера: выставки, салоны, фестивали, ярмарки.</p></span>
		<input type="checkbox" name="type[]" value="4" title="Тренинг"<?=(in_array(4,$arResult['filter']['type']) ? ' checked="true"' : '')?> />
		<span class="help-icon">Помощь <p>Мероприятия практического характера: тренинги, семинары, мастер-классы, видеодемонстрации.</p></span>
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
	<?php if ($arResult['empty']):?>
		<a href="/events/?country=all&city=all" class="clear">Сбросить фильтр</a>
	<?php endif?>
</form>