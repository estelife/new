<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>

<form name="trainings" class="filter" method="get" action="/trainings/" >
	<div class="title">
		<h4>Поиск семинаров</h4>
		<span class="count-result"><?=$arResult['count']?></span>
	</div>
	<div class="field">
		<label for="city">Город</label>
		<select name="city" >
			<option value="all">--</option>
			<?php if (!empty($arResult['cities'])):?>
				<?php foreach ($arResult['cities'] as $val):?>
					<option value="<?=$val['ID']?>" <?if($_GET['city'] === $val['ID']) echo " selected";?>><?=$val['NAME']?></option>
				<?php endforeach?>
			<?php endif?>
		</select>
		<span class="block"></span>
	</div>

	<div class="field">
	<label for="direction">Направление</label>
	<select name="direction" >
		<option value="">--</option>
		<option value="5" <?if($arResult['filter']['direction'] == 5) echo " selected";?>>Ботулинотерапия</option>
		<option value="6" <?if($arResult['filter']['direction'] == 6) echo " selected";?>>Контурная пластика</option>
		<option value="7" <?if($arResult['filter']['direction'] == 7) echo " selected";?>>Мезотерапия</option>
		<option value="8" <?if($arResult['filter']['direction'] == 8) echo " selected";?>>Биоревитализация</option>
		<option value="9" <?if($arResult['filter']['direction'] == 9) echo " selected";?>>Объемное моделирование</option>
		<option value="10" <?if($arResult['filter']['direction'] == 10) echo " selected";?>>Безоперационный лифтинг</option>
		<option value="12" <?if($arResult['filter']['direction'] == 12) echo " selected";?>>Пилинги</option>
		<option value="13" <?if($arResult['filter']['direction'] == 13) echo " selected";?>>Космецевтика</option>
		<option value="14" <?if($arResult['filter']['direction'] == 14) echo " selected";?>>Аппаратная косметология</option>
	</select>
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

	<input type="submit" value="Найти семинар" class="submit">
	<?php if ($arResult['empty']):?>
		<a href="/trainings/?city=all" class="clear">Сбросить фильтр</a>
	<?php endif?>
</form>