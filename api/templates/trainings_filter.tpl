<div class="small-block el-filter">
	<div class="block-header blue">
		<span>Фильтр</span>
		<a href="/trainings/" class="el-cl-filter" data-filter="trainings" title="Сбросить параметры фильтра">Сбросить</a>
		<div class="clear"></div>
	</div>
	<div class="shadow"></div>
	<form method="get" action="/trainings/" name="trainings">
		<table class='clinic-table'>
			<tr>
				<td valign="top">
					<div class="field-block"></div>
					<label>Город</label>
					<select name="city">
						<option value="">-- Не важно --</option>
						<!--if($cities)!-->
							<!--foreach ($cities as $key=>$val)!-->
								<option value="<!--$val.ID!-->"><!--$val.NAME!--></option>
							<!--endforeach!-->
						<!--endif!-->
					</select>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<div class="field-block"></div>
					<label>Направление</label>
					<select name="direction">
						<option value="">-- Не важно --</option>
						<option value="5">Ботулинотерапия</option>
						<option value="6">Контурная пластика</option>
						<option value="7">Мезотерапия</option>
						<option value="8">Биоревитализация</option>
						<option value="9">Объемное моделирование</option>
						<option value="10">Безоперационный лифтинг</option>
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
						<input type="text" name="date_to" value="<!--$date_to!-->" />
						<img src="/img/icon/f_calendar.png" />
					</div>
				</td>
			</tr>
		</table>
	</form>
</div>