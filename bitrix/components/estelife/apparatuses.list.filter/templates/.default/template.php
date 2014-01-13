<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<form name="apparatuses" class="filter" method="get" action="/apparatuses/" >
	<div class="title">
		<h4>Поиск аппарата</h4>
		<!--		<span>Найдено 6 акций</span>-->
	</div>
	<div class="field">
		<label for="name">Наименование</label>
		<input name="name" type="text" value="<?=$arResult['filter']['name']?>" class="text" />
		<span class="block"></span>
	</div>
	<div class="field">
		<label for="type">Тип</label>
		<select name="type" >
			<option value="">--</option>
			<option value="1" <?if($arResult['filter']['type'] === 1) echo " selected";?>>Anti-Age терапия</option>
			<option value="7" <?if($arResult['filter']['type'] === 7) echo " selected";?>>Диагностика</option>
			<option value="2" <?if($arResult['filter']['type'] === 2) echo " selected";?>>Коррекция фигуры</option>
			<option value="9" <?if($arResult['filter']['type'] === 9) echo " selected";?>>Микропигментация</option>
			<option value="5" <?if($arResult['filter']['type'] === 5) echo " selected";?>>Микротоки</option>
			<option value="4" <?if($arResult['filter']['type'] === 4) echo " selected";?>>Миостимуляция</option>
			<option value="6" <?if($arResult['filter']['type'] === 6) echo " selected";?>>Лазеры</option>
			<option value="8" <?if($arResult['filter']['type'] === 8) echo " selected";?>>Реабилитация</option>
			<option value="3" <?if($arResult['filter']['type'] === 3) echo " selected";?>>Эпиляция</option>
		</select>

		<span class="block"></span>
	</div>
	<div class="field country">
		<label for="country">Страна</label>
		<select name="country" >
			<option value="all">--</option>
			<?php if (!empty($arResult['countries'])):?>
				<?php foreach ($arResult['countries'] as $val):?>
					<option value="<?=$val['ID']?>" <?if($arResult['filter']['country'] === $val['ID']) echo " selected";?>><?=$val['NAME']?></option>
				<?php endforeach?>
			<?php endif?>
		</select>
		<span class="block"></span>
	</div>

	<input type="submit" value="Найти аппарат" class="submit">
	<?php if ($arResult['empty']):?>
		<a href="/apparatuses/?country=all" class="clear">Сбросить фильтр</a>
	<?php endif?>
</form>