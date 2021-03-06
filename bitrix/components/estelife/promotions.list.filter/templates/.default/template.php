<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>

<form name="promotions" class="filter" method="get" action="/promotions/" >
	<div class="title">
		<h4>Поиск акций</h4>
		<span class="count-result"><?=$arResult['count']?></span>
	</div>
	<div class="field">
		<label for="name">Наименование</label>
		<input name="name" type="text" value="<?=$arResult['filter']['name']?>" class="text" />
		<span class="block"></span>
	</div>
	<div class="field">
		<label for="cities">Город</label>
		<select name="city" data-rules="get_metro:select[name=metro]">
			<option value="all">--</option>
			<?php if (!empty($arResult['cities'])):?>
				<?php foreach ($arResult['cities'] as $val):?>
					<option value="<?=$val['id']?>"<?if($arResult['filter']['city'] == $val['id']) echo ' selected="true"';?>><?=$val['name']?></option>
				<?php endforeach?>
			<?php endif?>
		</select>

		<span class="block"></span>
	</div>
	<div class="field<?=(empty($arResult['metro']) ? ' disabled' : '')?>">
		<label for="metros">Станция метро</label>
		<select name="metro">
			<option value="">--</option>
			<?php if (!empty($arResult['metro'])):?>
				<?php foreach ($arResult['metro'] as $val):?>
					<option value="<?=$val['ID']?>" <?if($arResult['filter']['metro'] == $val['ID']) echo ' selected="true"';?>><?=$val['NAME']?></option>
				<?php endforeach?>
			<?php endif?>
		</select>
		<span class="block"></span>
	</div>
	<div class="field">
		<label for="specs">Специализация</label>
		<select name="spec" data-rules="get_service:select[name=service];get_method:select[name=method];get_concreate:select[name=concreate];">
			<option value=''>--</option>
			<?php if (!empty($arResult['specializations'])):?>
				<?php foreach ($arResult['specializations'] as $val):?>
					<option value="<?=$val['id']?>" <?if($arResult['filter']['spec'] == $val['id']) echo ' selected="true"';?>><?=$val['name']?></option>
				<?php endforeach?>
			<?php endif?>
		</select>
		<span class="block"></span>
	</div>
	<div class="field<?=(empty($arResult['service']) ? ' disabled' : '')?>">
		<label for="service">Вид услуги</label>
		<select name="service" data-rules="get_concreate:select[name=concreate];get_method:select[name=method]">
			<option value=''>--</option>
			<?php if (!empty($arResult['service'])):?>
				<?php foreach ($arResult['service'] as $val):?>
					<option value="<?=$val['id']?>" <?if($arResult['filter']['service'] == $val['id']) echo ' selected="true"';?>><?=$val['name']?></option>
				<?php endforeach?>
			<?php endif?>
		</select>
		<span class="block"></span>
	</div>
	<div class="field<?=(empty($arResult['methods']) ? ' disabled' : '')?>">
		<label for="method">Методика</label>
		<select name="method" data-rules="get_concreate:select[name=concreate]">
			<option value=''>--</option>
			<?php if(!empty($arResult['methods'])):?>
				<?php foreach ($arResult['methods'] as $val):?>
					<option value="<?=$val['id']?>" <?if($arResult['filter']['method'] == $val['id']) echo ' selected="true"';?>><?=$val['name']?></option>
				<?php endforeach?>
			<?php endif?>
		</select>
		<span class="block"></span>
	</div>
	<div class="field<?=(empty($arResult['concreate']) ? ' disabled' : '')?>">
		<label for="concreate">Тип услуги</label>
		<select name="concreate">
			<option value=''>--</option>
			<?php if (!empty($arResult['concreate'])):?>
				<?php foreach ($arResult['concreate'] as $val):?>
					<option value="<?=$val['id']?>" <?if($arResult['filter']['concreate'] == $val['id']) echo ' selected="true"';?>><?=$val['name']?></option>
				<?php endforeach?>
			<?php endif?>
		</select><span class="block"></span>
	</div>
	<input type="submit" value="Найти акции" class="submit">
	<?php if ($arResult['empty']):?>
		<a href="/promotions/?city=all" class="clear">Сбросить фильтр</a>
	<?php endif?>
</form>