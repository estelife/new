<div class="small-block el-filter">
	<div class="block-header red">
		<span>Фильтр</span>
		<div class="clear"></div>
	</div>
	<div class="shadow"></div>
	<form method="get" action="/apparatuses/" name="apparatuses">
		<table class='clinic-table'>
			<tr>
				<td valign="top">
					<div class="field-block"></div>
					<label>Название</label>
					<div class="text inp">
						<input name="name" type="text" value="" data-action="get_apparatus" />
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
						<option value="1">Anti-Age терапия</option>
						<option value="7">Диагностика</option>
						<option value="2">Коррекция фигуры</option>
						<option value="9">Микропигментация</option>
						<option value="5">Микротоки</option>
						<option value="4">Миостимуляция</option>
						<option value="6">Лазеры</option>
						<option value="8">Реабилитация</option>
						<option value="3">Эпиляция</option>
					</select>
				</td>
			</tr>
		</table>
	</form>
</div>