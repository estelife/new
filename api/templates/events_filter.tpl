<div class="small-block el-filter">
<div class="block-header blue">
	<span>Фильтр</span>
	<a href="/events/" class="el-cl-filter" data-filter="events" title="Сбросить параметры фильтра">Сбросить</a>
	<div class="clear"></div>
</div>
<div class="shadow"></div>
<form method="get" action="/events/" name="events">
	<table class='clinic-table'>
		<tr>
			<td valign="top">
				<div class="field-block"></div>
				<label>Страна</label>
				<select name="country" data-rules="get_city:select[name=city]">
					<option value="">-- Не важно --</option>
					<!--foreach ($countries as $key=>$val)!-->
					<option value="<!--$val.ID!-->"><!--$val.NAME!--></option>
					<!--endforeach!-->
				</select>
			</td>
		</tr>
		<tr>
			<td valign="top" class="disabled">
				<div class="dsbld">
					<div class="field-block"></div>
					<label>Город</label>
					<select name="city">
						<option value="">-- Не важно --</option>
					</select>
				</div>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<div class="field-block"></div>
				<label>Направление</label>
				<select name="direction">
					<option value="">-- Не важно --</option>
					<option value="1">Пластическая хирургия</option>
					<option value="2">Косметология</option>
					<option value="3">Косметика</option>
					<option value="4">Дерматология</option>
				</select>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<div class="field-block"></div>
				<label>Тип</label>
				<select name="type">
					<option value="">-- Не важно --</option>
					<option value="1">Форум</option>
					<option value="2">Выставка</option>
					<option value="4">Тренинг</option>
				</select>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<div class="field-block"></div>
				<label>Дата проведения</label>
				<div class="text date from">
					<input type="text" name="date_from" value="<!--$date_from!-->" />
					<img src="/img/icon/f_calendar.png" />
				</div>
				<div class="text date to">
					<input type="text" name="date_to" value="" />
					<img src="/img/icon/f_calendar.png" />
				</div>
			</td>
		</tr>
	</table>
</form>
</div>