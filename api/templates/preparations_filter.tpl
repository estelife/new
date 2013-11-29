<div class="small-block el-filter">
	<div class="block-header blue">
		<span>Фильтр</span>
		<div class="clear"></div>
	</div>
	<div class="shadow"></div>
	<form method="get" action="/preparations/" name="preparations">
		<table class='clinic-table'>
			<tr>
				<td valign="top">
					<div class="field-block"></div>
					<label>Название</label>
					<div class="text inp">
						<input name="name" type="text" value="" data-action="get_pills" />
					</div>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<div class="field-block"></div>
					<label>Страна</label>
					<select name="country">
						<option value="">-- Не важно --</option>
						<!--if ($countries)!-->
							<!--foreach ($countries as $key=>$val)!-->
								<option value="<!--$val.ID!-->"><!--$val.NAME!--></option>
							<!--endforeach!-->
						<!--endif!-->
					</select>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<div class="field-block"></div>
					<label>Тип</label>
					<select name="type">
						<option value="">-- Не важно --</option>
						<option value="3">Биоревитализация</option>
						<option value="2">Ботулинотерапия</option>
						<option value="5">Имплантаты</option>
						<option value="1">Мезотерапия</option>
						<option value="6">Нити</option>
						<option value="4">Филлеры</option>
					</select>
				</td>
			</tr>
		</table>
	</form>
</div>