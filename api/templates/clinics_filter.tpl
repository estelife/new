<form name="promotions" class="filter" method="get" action="/clinics/" >
	<div class="title">
		<h4>Поиск клиники</h4>
		<!--<span>Найдено 6 акций</span>-->
	</div>
	<div class="field">
		<label for="name">Наименование</label>
		<input name="name" type="text" value="<!--$filter.name!-->" class="text" />
		<span class="block"></span>
	</div>
	<div class="field">
		<label for="cities">Город</label>
		<select name="city" data-rules="get_metro:select[name=metro]">
			<option value="">--</option>
			<option value="359"<!--if($filter.city===359)!--> selected="true"<!--endif!-->>Москва</option>
			<option value="358"<!--if($filter.city===358)!--> selected="true"<!--endif!-->>Санкт-Петербург</option>
		</select>
		<span class="block"></span>
	</div>
	<div class="field<!--if($filter.metro)!--> <!--else!--> disabled<!--endif!-->">
		<label for="metros">Станция метро</label>
		<select name="metro">
			<option value="">--</option>
		</select>
		<span class="block"></span>
	</div>
	<div class="field">
		<label for="specs">Специализация</label>
		<select name="spec" data-rules="get_service:select[name=service];get_method:select[name=method];get_concreate:select[name=concreate];">
			<option value="">--</option>
			<!--if($specializations)!-->
				<!--foreach($specializations as $key=>$val)!-->
					<option value="<!--$val.id!-->"><!--$val.name!--></option>
				<!--endforeach!-->
			<!--endif!-->
		</select>
		<span class="block"></span>
	</div>
	<div class="field disabled">
		<label for="service">Вид услуги</label>
		<select name="service" data-rules="get_concreate:select[name=concreate];get_method:select[name=method]">
			<option value="">--</option>
		</select>
		<span class="block"></span>
	</div>
	<div class="field disabled>">
		<label for="method">Методика</label>
		<select name="method" data-rules="get_concreate:select[name=concreate]">
			<option value="">--</option>
		</select><span class="block"></span>
	</div>
	<div class="field disabled">
		<label for="concreate">Тип услуги</label>
		<select name="concreate">
			<option value="">--</option>
		</select><span class="block"></span>
	</div>
	<input type="submit" value="Найти клинику" class="submit">
	<a href="/clinics/" class="clear">Сбросить фильтр</a>
</form>