<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>

<div class="block-header blue">
	<span><?=GetMessage("ESTELIFE_CLINIC_FILTER")?></span>
	<a href="/events/" class="el-cl-filter" data-filter="events" title="Сбросить параметры фильтра">Сбросить</a>
	<div class="clear"></div>
</div>
<div class="shadow"></div>
<form method="get" action="/trainings/" name="trainings">
	<table class='clinic-table'>

		<tr>
			<td valign="top">
				<div class="field-block"></div>
				<label><?=GetMessage("ESTELIFE_CLINIC_CITY")?></label>
				<select name="city">
					<option value="">-- Не важно --</option>
					<?php if (!empty($arResult['cities'])):?>
						<?php foreach ($arResult['cities'] as $val):?>
							<option value="<?=$val['ID']?>" <?if($_GET['filter']['city'] === $val['ID']) echo " selected";?>><?=$val['NAME']?></option>
						<?php endforeach?>
					<?php endif?>
				</select>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<div class="field-block"></div>
				<label><?=GetMessage("ESTELIFE_CLINIC_DIRECTIONS")?></label>
				<select name="direction">
					<option value="">-- Не важно --</option>
					<option value="5" <?if($arResult['filter']['direction'] == 5) echo " selected";?>>Ботулинотерапия</option>
					<option value="6" <?if($arResult['filter']['direction'] == 6) echo " selected";?>>Контурная пластика</option>
					<option value="7" <?if($arResult['filter']['direction'] == 7) echo " selected";?>>Мезотерапия</option>
					<option value="8" <?if($arResult['filter']['direction'] == 8) echo " selected";?>>Биоревитализация</option>
					<option value="9" <?if($arResult['filter']['direction'] == 9) echo " selected";?>>Объемное моделирование</option>
					<option value="10" <?if($arResult['filter']['direction'] == 10) echo " selected";?>>Безоперационный лифтинг</option>
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