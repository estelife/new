<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>

<div class="block-header blue">
	<span><?=GetMessage("ESTELIFE_CLINIC_FILTER")?></span>
	<div class="clear"></div>
</div>
<div class="shadow"></div>
<form method="get" action="/preparations/" name="preparations">
	<table class='clinic-table'>
		<tr>
			<td valign="top">
				<div class="field-block"></div>
				<label><?=GetMessage("ESTELIFE_NAME")?></label>
				<div class="text inp">
					<input name="name" type="text" value="<?=$_GET['name']?>" data-action="get_pills" />
				</div>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<div class="field-block"></div>
				<label><?=GetMessage("ESTELIFE_COUNTRY")?></label>
				<select name="country">
					<option value="">-- Не важно --</option>
					<?php if (!empty($arResult['countries'])):?>
						<?php foreach ($arResult['countries'] as $val):?>
							<option value="<?=$val['ID']?>" <?if($_GET['country'] === $val['ID']) echo " selected";?>><?=$val['NAME']?></option>
						<?php endforeach?>
					<?php endif?>
				</select>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<div class="field-block"></div>
				<label><?=GetMessage("ESTELIFE_TYPE")?></label>
				<select name="type">
					<option value="">-- Не важно --</option>
					<option value="3" <?if($_GET['type'] === 3) echo " selected";?>>Биоревитализация</option>
					<option value="2" <?if($_GET['type'] === 2) echo " selected";?>>Ботулинотерапия</option>
					<option value="5" <?if($_GET['type'] === 5) echo " selected";?>>Имплантаты</option>
					<option value="1" <?if($_GET['type'] === 1) echo " selected";?>>Мезотерапия</option>
					<option value="6" <?if($_GET['type'] === 6) echo " selected";?>>Нити</option>
					<option value="4" <?if($_GET['type'] === 4) echo " selected";?>>Филлеры</option>
				</select>
			</td>
		</tr>
	</table>
</form>