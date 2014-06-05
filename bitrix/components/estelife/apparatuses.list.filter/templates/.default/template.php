<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<form name="apparatuses" class="filter" method="get" action="/apparatuses/" >
	<div class="title">
		<h4>Поиск аппарата</h4>
		<span class="count-result"><?=$arResult['count']?></span>
	</div>
	<div class="field">
		<label for="name">Наименование</label>
		<input name="name" type="text" value="<?=$arResult['filter']['name']?>" class="text" />
		<span class="block"></span>
	</div>
	<div class="field">
		<label for="company_name">Производитель</label>
		<input name="company_name" type="text" value="<?=$arResult['filter']['company_name']?>" class="text"/>
		<span class="block"></span>
	</div>
	<?php if (!empty($arResult['types'])):?>
		<div class="field">
			<label for="type">Назначение</label>
			<select name="type" >
				<option value="">--</option>
				<?php foreach ($arResult['types'] as $val):?>
					<option value="<?=$val['id']?>" <?if($arResult['filter']['type'] === $val['id']) echo " selected";?>><?=$val['name']?></option>
				<?php endforeach?>
			</select>
			<span class="block"></span>
		</div>
	<?php endif?>
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