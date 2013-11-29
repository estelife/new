<div class="small-block el-filter">
	<div class="block-header red">
		<span>Фильтр</span>
		<div class="clear"></div>
	</div>

	<div class="shadow"></div>

	<form method="get" action="/clinic/" name="clinics">
		<table class='clinic-table'>
			<tr>
				<td valign="top">
					<label>Город</label>
					<select name="city" data-rules="get_metro:select[name=metro]">
						<option value="">-- Не важно --</option>
						<option value="359"<!--if($filter.city==359)!--> selected="true"<!--endif!-->>Москва</option>
						<option value="358"<!--if($filter.city==358)!--> selected="true"<!--endif!-->>Санкт-Петербург</option>
					</select>
					<div class="field-block"></div>
				</td>
			</tr>
			<tr>
				<td valign="top" class="disabled">
					<div class="dsbld">
						<label>Станция метро</label>
						<select name="metro">
							<option value="">-- Не важно --</option>
						</select>
						<div class="field-block"></div>
					</div>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<label>Специализация</label>
					<select name="spec" data-rules="get_service:select[name=service];get_method:select[name=method]">
						<option value=''>-- Не важно --</option>
						<!--if ($specializations)!-->
						<!--foreach($specializations as $key=>$val)!-->
						<option value="<!--$val.id!-->"><!--$val.name!--></option>
						<!--endforeach!-->
						<!--endif!-->
					</select>
					<div class="field-block"></div>
				</td>
			</tr>
			<tr>
				<td valign="top" class="disabled">
					<div class="dsbld">
						<label>Вид услуги</label>
						<select name="service" data-rules="get_concreate:select[name=concreate];get_method:select[name=method]">
							<option value=''>-- Не важно --</option>
						</select>
						<div class="field-block"></div>
					</div>
				</td>
			</tr>
			<tr>
				<td valign="top" class="disabled">
					<div class="dsbld">
						<label>Методика</label>
						<select name="method" data-rules="get_concreate:select[name=concreate]">
							<option value=''>-- Не важно --</option>
						</select>
						<div class="field-block"></div>
					</div>
				</td>
			</tr>
			<tr>
				<td valign="top" class="disabled">
					<div class="dsbld">
						<label>Тип услуги</label>
						<select name="concreate">
							<option value=''>-- Не важно --</option>
						</select>
						<div class="field-block"></div>
					</div>
				</td>
			</tr>
		</table>
	</form>
</div>