<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<form name="training-centers" class="filter" method="get" action="/training-centers/" >
	<div class="title">
		<h4>Поиск учебного центра</h4>
		<!--		<span>Найдено 6 акций</span>-->
	</div>
	<div class="field">
		<label for="name">Наименование</label>
		<input name="name" type="text" value="<?=$_GET['name']?>" class="text"/>
		<span class="block"></span>
	</div>
	<div class="field">
		<label for="city">Город</label>
		<select name="city" >
			<option value="">--</option>
			<?php if (!empty($arResult['cities'])):?>
				<?php foreach ($arResult['cities'] as $val):?>
					<option value="<?=$val['ID']?>" <?if($_GET['country'] === $val['ID']) echo " selected";?>><?=$val['NAME']?></option>
				<?php endforeach?>
			<?php endif?>
		</select>

		<span class="block"></span>
	</div>

	<input type="submit" value="Найти учебный центр" class="submit">
	<a href="/training-centers/" class="clear">Сбросить фильтр</a>
</form>