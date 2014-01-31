<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<form name="preparations" class="filter" method="get" action="/preparations/" >
	<div class="title">
		<h4>Поиск препарата</h4>
		<span class="count-result"><?=$arResult['count']?></span>
	</div>
	<div class="field">
		<label for="name">Наименование</label>
		<input name="name" type="text" value="<?=$arResult['filter']['name']?>" class="text"/>
		<span class="block"></span>
	</div>
	<div class="field">
		<label for="type">Назначение</label>
		<select name="type" >
			<option value="">--</option>
			<option value="1" <?if($arResult['filter']['type'] === 1) echo " selected";?>>Мезотерапия</option>
			<option value="3" <?if($arResult['filter']['type'] === 3) echo " selected";?>>Биоревитализация</option>
			<option value="2" <?if($arResult['filter']['type'] === 2) echo " selected";?>>Ботулинотерапия</option>
			<option value="4" <?if($arResult['filter']['type'] === 4) echo " selected";?>>Контурная пластика</option>
			<option value="5" <?if($arResult['filter']['type'] === 5) echo " selected";?>>Имплантаты</option>
			<option value="6" <?if($arResult['filter']['type'] === 6) echo " selected";?>>Нити</option>
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

	<input type="submit" value="Найти препарат" class="submit">
	<?php if ($arResult['empty']):?>
		<a href="/preparations/?country=all" class="clear">Сбросить фильтр</a>
	<?php endif?>
</form>