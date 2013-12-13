<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>

<form name="sponsors" class="filter" method="get" action="/sponsors/" >
	<div class="title">
		<h4>Поиск организатора</h4>
		<!--		<span>Найдено 6 акций</span>-->
	</div>
	<div class="field">
		<label for="name">Наименование</label>
		<input name="name" type="text" value="<?=$_GET['name']?>" class="text"/>
		<span class="block"></span>
	</div>
	<div class="field country">
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

	<input type="submit" value="Найти организатора" class="submit">
	<a href="/sponsors/" class="clear">Сбросить фильтр</a>
</form>
