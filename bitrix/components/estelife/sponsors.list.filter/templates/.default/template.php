<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>

<div class="block-header red">
	<span><?=GetMessage("ESTELIFE_CLINIC_FILTER")?></span>
	<div class="clear"></div>
</div>
<div class="shadow"></div>
<form method="get" action="/sponsors/" name="clinic_filter">
	<table class='clinic-table'>
		<tr>
			<td valign="top">
				<label><?=GetMessage("ESTELIFE_CLINIC_COUNTRY")?></label>
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
			<td valign="bottom">
				<input type="submit" class="button" name="filter" value="Отфильтровать" />
			</td>
		</tr>
	</table>
</form>