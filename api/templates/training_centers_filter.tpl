<div class="small-block el-filter">
	<div class="block-header red">
		<span>Фильтр</span>
		<a href="/trainings-center/" class="el-cl-filter" data-filter="trainings_center" title="Сбросить параметры фильтра">Сбросить</a>
		<div class="clear"></div>
	</div>
	<div class="shadow"></div>
	<form method="get" action="/training-centers/" name="training_centers">
		<table class='clinic-table'>
			<tr>
				<td valign="top">
					<div class="field-block"></div>
					<label>Название</label>
					<div class="text inp">
						<input name="name" type="text" value="" data-action="get_uch"  />
					</div>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<div class="field-block"></div>
					<label>Город</label>
					<select name="city">
						<option value="">-- Не важно --</option>
						<!--if ($cities)!-->
							<!--foreach ($cities as $key=>$val)!-->
								<option value="<!--$val.ID!-->"><!--$val.NAME!--></option>
							<!--endforeach!-->
						<!--endif!-->
					</select>
				</td>
			</tr>
		</table>
	</form>
</div>