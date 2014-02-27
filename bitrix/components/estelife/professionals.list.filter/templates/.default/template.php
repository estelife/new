<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<form name="professionals" class="filter" method="get" action="/professionals/" >
	<div class="title">
		<h4>Поиск специалиста</h4>
		<span class="count-result"><?=$arResult['count']?></span>
	</div>
	<div class="field">
		<label for="name">ФИО</label>
		<input name="name" type="text" value="<?=$arResult['filter']['name']?>" class="text" />
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

	<input type="submit" value="Найти специалиста" class="submit">
	<?php if ($arResult['empty']):?>
		<a href="/professionals/?country=all" class="clear">Сбросить фильтр</a>
	<?php endif?>
</form>