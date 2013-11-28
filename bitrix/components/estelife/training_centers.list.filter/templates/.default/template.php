<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>

<div class="block-header red">
	<span><?=GetMessage("ESTELIFE_CLINIC_FILTER")?></span>
	<a href="/training-centers/" class="el-cl-filter" data-filter="training_centers" title="Сбросить параметры фильтра">Сбросить</a>
	<div class="clear"></div>
</div>
<div class="shadow"></div>
<form method="get" action="/trainings-center/" name="trainings_center">
	<table class='clinic-table'>
		<tr>
			<td valign="top">
				<div class="field-block"></div>
				<label><?=GetMessage("ESTELIFE_NAME")?></label>
				<div class="text inp">
					<input name="name" type="text" value="<?=$_GET['name']?>" data-action="get_uch"  />
				</div>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<div class="field-block"></div>
				<label><?=GetMessage("ESTELIFE_CITY")?></label>
				<select name="city">
					<option value="">-- Не важно --</option>
					<?php if (!empty($arResult['cities'])):?>
						<?php foreach ($arResult['cities'] as $val):?>
							<option value="<?=$val['ID']?>" <?if($_GET['city'] === $val['ID']) echo " selected";?>><?=$val['NAME']?></option>
						<?php endforeach?>
					<?php endif?>
				</select>
			</td>
		</tr>
	</table>
</form>