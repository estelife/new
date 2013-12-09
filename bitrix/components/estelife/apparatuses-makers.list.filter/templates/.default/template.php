<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>

<form name="preparations-makers" class="filter" method="get" action="/apparatuses-makers/" >
	<div class="title">
		<h4>Поиск производителя</h4>
		<!--		<span>Найдено 6 акций</span>-->
	</div>
	<div class="field">
		<label for="name">Наименование</label>
		<input name="name" type="text" value="<?=$_GET['name']?>" class="text"/>
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

	<input type="submit" value="Найти производителя" class="submit">
	<a href="/apparatuses-makers/" class="clear">Сбросить фильтр</a>
</form>

