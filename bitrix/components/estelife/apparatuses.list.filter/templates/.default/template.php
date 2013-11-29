<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>

<div class="block-header red">
	<span><?=GetMessage("ESTELIFE_CLINIC_FILTER")?></span>
	<div class="clear"></div>
</div>
<div class="shadow"></div>
<form method="get" action="/apparatuses/" name="apparatuses">
	<table class='clinic-table'>
		<tr>
			<td valign="top">
				<div class="field-block"></div>
				<label><?=GetMessage("ESTELIFE_NAME")?></label>
				<div class="text inp">
					<input name="name" type="text" value="<?=$_GET['name']?>" data-action="get_apparatus" />
				</div>
				<div class="field-block"></div>
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
				<div class="field-block"></div>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<div class="field-block"></div>
				<label><?=GetMessage("ESTELIFE_TYPE")?></label>
				<select name="type">
					<option value="">-- Не важно --</option>
					<option value="1" <?if($_GET['type'] === 1) echo " selected";?>>Anti-Age терапия</option>
					<option value="7" <?if($_GET['type'] === 7) echo " selected";?>>Диагностика</option>
					<option value="2" <?if($_GET['type'] === 2) echo " selected";?>>Коррекция фигуры</option>
					<option value="9" <?if($_GET['type'] === 9) echo " selected";?>>Микропигментация</option>
					<option value="5" <?if($_GET['type'] === 5) echo " selected";?>>Микротоки</option>
					<option value="4" <?if($_GET['type'] === 4) echo " selected";?>>Миостимуляция</option>
					<option value="6" <?if($_GET['type'] === 6) echo " selected";?>>Лазеры</option>
					<option value="8" <?if($_GET['type'] === 8) echo " selected";?>>Реабилитация</option>
					<option value="3" <?if($_GET['type'] === 3) echo " selected";?>>Эпиляция</option>
				</select>
				<div class="field-block"></div>
			</td>
		</tr>
	</table>
</form>