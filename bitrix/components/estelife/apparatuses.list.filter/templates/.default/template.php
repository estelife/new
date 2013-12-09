<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<form name="apparatuses" class="filter" method="get" action="/apparatuses/" >
	<div class="title">
		<h4>Поиск аппарата</h4>
		<!--		<span>Найдено 6 акций</span>-->
	</div>
	<div class="field">
		<label for="name">Наименование</label>
		<input name="name" type="text" value="<?=$_GET['name']?>" class="text"/>
		<span class="block"></span>
	</div>
	<div class="field">
		<label for="type">Тип</label>
		<select name="type" >
			<option value="">--</option>
			<option value="1" <?if($_GET['type'] === 1) echo " selected";?>>Anti-Age терапия</option>
			<option value="7" <?if($_GET['type'] === 7) echo " selected";?>>Диагностика</option>
			<option value="2" <?if($_GET['type'] === 2) echo " selected";?>>Коррекция фигуры</option>
			<option value="9" <?if($_GET['type'] === 9) echo " selected";?>>Микропигментация</option>
			<option value="5" <?if($_GET['type'] === 5) echo " selected";?>>Микротоки</option>
			<option value="4" <?if($_GET['type'] === 4) echo " selected";?>>Миостимуляция</option>
			<option value="6" <?if($_GET['type'] === 6) echo " selected";?>>Лазеры</option>
			<option value="8" <?if($_GET['type'] === 8) echo " selected";?>>Реабилитация</option>
			<option value="3" <?if($_GET['type'] === 3) echo " selected";?>>Эпиляция</option>
		</select>

		<span class="block"></span>
	</div>
	<div class="field">
		<label for="country">Страна</label>
		<select name="country" >
			<option value="">--</option>
			<?php if (!empty($arResult['countries'])):?>
				<?php foreach ($arResult['countries'] as $val):?>
					<option value="<?=$val['ID']?>" <?if($_GET['country'] === $val['ID']) echo " selected";?>><?=$val['NAME']?></option>
				<?php endforeach?>
			<?php endif?>
		</select>
		<span class="block"></span>
	</div>

	<input type="submit" value="Найти аппарат" class="submit">
	<a href="/apparatuses/" class="clear">Сбросить фильтр</a>
</form>