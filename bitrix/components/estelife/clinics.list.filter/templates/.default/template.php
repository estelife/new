<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>

<div class="block-header red">
	<span><?=GetMessage("ESTELIFE_CLINIC_FILTER")?></span>
	<a href="/clinic/" class="el-cl-filter" data-filter="clinics" title="Сбросить параметры фильтра">Сбросить</a>
	<div class="clear"></div>
</div>
<div class="shadow"></div>
<form method="get" action="/clinic/" name="clinics">
	<table class='clinic-table'>
		<tr>
			<td valign="top">
				<label><?=GetMessage("ESTELIFE_CLINIC_CITY")?></label>
				<select name="city" data-rules="get_metro:select[name=metro]">
					<option value="">-- Не важно --</option>
					<option value="359"<?if($arResult['filter']['city'] === "359") echo " selected";?>><?=GetMessage("ESTELIFE_CLINIC_MOSCOW")?></option>
					<option value="358"<?if($arResult['filter']['city'] === "358") echo " selected";?>><?=GetMessage("ESTELIFE_CLINIC_SPB")?></option>
				</select>
				<div class="field-block"></div>
			</td>
		</tr>
		<tr>
			<td valign="top"<?=(empty($arResult['metro']) ? ' class="disabled"' : '')?>>
				<div class="dsbld">
					<label><?=GetMessage("ESTELIFE_CLINIC_METRO")?></label>
					<select name="metro">
						<option value="">-- Не важно --</option>
						<?php if (!empty($arResult['metro'])):?>
							<?php foreach ($arResult['metro'] as $val):?>
								<option value="<?=$val['ID']?>" <?if($arResult['filter']['metro'] === $val['ID']) echo " selected";?>><?=$val['NAME']?></option>
							<?php endforeach?>
						<?php endif?>
					</select>
					<div class="field-block"></div>
				</div>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<label><?=GetMessage("ESTELIFE_CLINIC_SPEC")?></label>
				<select name="spec" data-rules="get_service:select[name=service];get_method:select[name=method]">
					<option value=''>-- Не важно --</option>
					<?php if (!empty($arResult['specializations'])):?>
						<?php foreach ($arResult['specializations'] as $val):?>
							<option value="<?=$val['id']?>" <?if($arResult['filter']['spec'] === $val['id']) echo " selected";?>><?=$val['name']?></option>
						<?php endforeach?>
					<?php endif?>
				</select>
				<div class="field-block"></div>
			</td>
		</tr>
		<tr>
			<td valign="top"<?=(empty($arResult['service']) ? ' class="disabled"' : '')?>>
				<div class="dsbld">
					<label><?=GetMessage("ESTELIFE_CLINIC_SERVICE")?></label>
					<select name="service" data-rules="get_concreate:select[name=concreate];get_method:select[name=method]">
						<option value=''>-- Не важно --</option>
						<?php if (!empty($arResult['service'])):?>
							<?php foreach ($arResult['service'] as $val):?>
								<option value="<?=$val['id']?>" <?if($arResult['filter']['service'] === $val['id']) echo " selected";?>><?=$val['name']?></option>
							<?php endforeach?>
						<?php endif?>
					</select>
					<div class="field-block"></div>
				</div>
			</td>
		</tr>
		<tr>
			<td valign="top"<?=(empty($arResult['methods']) ? ' class="disabled"' : '')?>>
				<div class="dsbld">
					<label><?=GetMessage("ESTELIFE_CLINIC_METHOD")?></label>
					<select name="method" data-rules="get_concreate:select[name=concreate]">
						<option value=''>-- Не важно --</option>
						<?php if(!empty($arResult['methods'])):?>
							<?php foreach ($arResult['methods'] as $val):?>
								<option value="<?=$val['id']?>" <?if($arResult['filter']['method'] === $val['id']) echo " selected";?>><?=$val['name']?></option>
							<?php endforeach?>
						<?php endif?>
					</select>
					<div class="field-block"></div>
				</div>
			</td>
		</tr>
		<tr>
			<td valign="top"<?=(empty($arResult['concreate']) ? ' class="disabled"' : '')?>>
				<div class="dsbld">
					<label><?=GetMessage("ESTELIFE_CLINIC_CONCREATE")?></label>
					<select name="concreate">
						<option value=''>-- Не важно --</option>
						<?php if (!empty($arResult['concreate'])):?>
							<?php foreach ($arResult['concreate'] as $val):?>
								<option value="<?=$val['id']?>" <?if($arResult['filter']['concreate'] === $val['id']) echo " selected";?>><?=$val['name']?></option>
							<?php endforeach?>
						<?php endif?>
					</select>
					<div class="field-block"></div>
				</div>
			</td>
		</tr>
	</table>
</form>