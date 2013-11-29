<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>

<div class="block-header blue">
	<span><?=GetMessage("ESTELIFE_FILTER")?></span>
	<a href="/events/" class="el-cl-filter" data-filter="events" title="Сбросить параметры фильтра">Сбросить</a>
	<div class="clear"></div>
</div>
<div class="shadow"></div>
<form method="get" action="/events/" name="events">
	<table class='clinic-table'>
		<tr>
			<td valign="top">
				<div class="field-block"></div>
				<label><?=GetMessage("ESTELIFE_COUNTRY")?></label>
				<select name="country" data-rules="get_city:select[name=city]">
					<option value="">-- Не важно --</option>
					<?php foreach ($arResult['countries'] as $val):?>
						<option value="<?=$val['ID']?>"<?if($arResult['filter']['country'] === $val['ID']) echo " selected";?>><?=$val['NAME']?></option>
					<?php endforeach?>
				</select>
			</td>
		</tr>
		<tr>
			<td valign="top" class="disabled">
				<div class="dsbld">
					<div class="field-block"></div>
					<label><?=GetMessage("ESTELIFE_CITY")?></label>
					<select name="city">
						<option value="">-- Не важно --</option>
						<?php if (!empty($arResult['cities'])):?>
							<?php foreach ($arResult['cities'] as $val):?>
								<option value="<?=$val['ID']?>"<?if($arResult['filter']['city'] === $val['ID']) echo " selected";?>><?=$val['NAME']?></option>
							<?php endforeach?>
						<?php endif?>
					</select>
				</div>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<div class="field-block"></div>
				<label><?=GetMessage("ESTELIFE_DIRECTIONS")?></label>
				<select name="direction">
					<option value="">-- Не важно --</option>
					<option value="1" <?if($arResult['filter']['direction'] == 1) echo " selected";?>>Пластическая хирургия</option>
					<option value="2" <?if($arResult['filter']['direction'] == 2) echo " selected";?>>Косметология</option>
					<option value="3" <?if($arResult['filter']['direction'] == 3) echo " selected";?>>Косметика</option>
					<option value="4" <?if($arResult['filter']['direction'] == 4) echo " selected";?>>Дерматология</option>
				</select>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<div class="field-block"></div>
				<label><?=GetMessage("ESTELIFE_FORM")?></label>
				<select name="type">
					<option value="">-- Не важно --</option>
					<option value="1" <?if($arResult['filter']['type'] == 1) echo " selected";?>>Форум</option>
					<option value="2" <?if($arResult['filter']['type'] == 2) echo " selected";?>>Выставка</option>
					<option value="4" <?if($arResult['filter']['type'] == 4) echo " selected";?>>Тренинг</option>
				</select>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<div class="field-block"></div>
				<label><?=GetMessage("ESTELIFE_DATE")?></label>
				<div class="text date from">
					<input type="text" name="date_from" value="<?=$arResult['filter']['date_from']?>" />
					<img src="/img/icon/f_calendar.png" />
				</div>
				<div class="text date to">
					<input type="text" name="date_to" value="<?=$arResult['filter']['date_to']?>" />
					<img src="/img/icon/f_calendar.png" />
				</div>
			</td>
		</tr>
	</table>
</form>

