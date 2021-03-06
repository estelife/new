<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<form name="training-centers" class="filter" method="get" action="/training-centers/" >
	<div class="title">
		<h4>Поиск учебного центра</h4>
		<span class="count-result"><?=$arResult['count']?></span>
	</div>
	<div class="field">
		<label for="name">Наименование</label>
		<input name="name" type="text" value="<?=$arResult['filter']['name']?>" class="text"/>
		<span class="block"></span>
	</div>
	<div class="field">
		<label for="city">Город</label>
		<select name="city" >
			<option value="all">--</option>
			<?php if (!empty($arResult['cities'])):?>
				<?php foreach ($arResult['cities'] as $val):?>
					<option value="<?=$val['ID']?>" <?if($arResult['filter']['city'] === $val['ID']) echo " selected";?>><?=$val['NAME']?></option>
				<?php endforeach?>
			<?php endif?>
		</select>

		<span class="block"></span>
	</div>

	<input type="submit" value="Найти учебный центр" class="submit">
	<?php if ($arResult['empty']):?>
		<a href="/training-centers/?city=all" class="clear">Сбросить фильтр</a>
	<?php endif?>
</form>