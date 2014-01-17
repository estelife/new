<form name="promotions" class="filter" method="get" action="/promotions/" >
	<div class="title">
		<h4>Поиск акции</h4>
		<!--if($count)!-->
			<span class="count-result"><!--$count!--></span>
		<!--endif!-->
	</div>
	<div class="field">
		<label for="name">Наименование</label>
		<input name="name" type="text" value="<!--$filter.name!-->" class="text" />
		<span class="block"></span>
	</div>
	<div class="field">
		<label for="cities">Город</label>
		<select name="city" data-rules="get_metro:select[name=metro]">
			<option value="all">--</option>
			<option value="359"<!--if($filter.city===359)!--> selected="true"<!--endif!-->>Москва</option>
			<option value="358"<!--if($filter.city===358)!--> selected="true"<!--endif!-->>Санкт-Петербург</option>
		</select>
		<span class="block"></span>
	</div>
	<div class="field<!--if($filter.metro)!--> <!--else!--> disabled<!--endif!-->">
		<label for="metros">Станция метро</label>
		<select name="metro" id="metros">
			<option value="">--</option>
			<!--if($metro)!-->
			<!--foreach ($metro as $key=>$val)!-->
			<option value="<!--$val.ID!-->"<!--if($filter.metro==$val.ID)!--> selected="true"<!--endif!-->><!--$val.NAME!--></option>
			<!--endforeach!-->
			<!--endif!-->
		</select>
		<span class="block"></span>
	</div>
	<div class="field">
		<label for="specs">Специализация</label>
		<select name="spec" id="specs" data-rules="get_service:select[name=service];get_method:select[name=method];get_concreate:select[name=concreate];">
			<option value="">--</option>
			<!--if($specializations)!-->
			<!--foreach($specializations as $key=>$val)!-->
			<option value="<!--$val.id!-->"<!--if($filter.spec==$val.id)!--> selected="true"<!--endif!-->><!--$val.name!--></option>
			<!--endforeach!-->
			<!--endif!-->
		</select>
		<span class="block"></span>
	</div>
	<div class="field disabled">
		<label for="service">Вид услуги</label>
		<select name="service" id="service" data-rules="get_concreate:select[name=concreate];get_method:select[name=method]">
			<option value="">--</option>
			<!--if($service)!-->
			<!--foreach ($service as $key=>$val)!-->
			<option value="<!--$val.id!-->"<!--if($filter.service==$val.id)!--> selected="true"<!--endif!-->><!--$val.name!--></option>
			<!--endforeach!-->
			<!--endif!-->
		</select>
		<span class="block"></span>
	</div>
	<div class="field disabled">
		<label for="method">Методика</label>
		<select name="method" data-rules="get_concreate:select[name=concreate]">
			<option value="">--</option>
			<!--if($methods)!-->
			<!--foreach ($methods as $key=>$val)!-->
			<option value="<!--$val.id!-->"<!--if($filter.method==$val.id)!--> selected="true"<!--endif!-->><!--$val.name!--></option>
			<!--endforeach!-->
			<!--endif!-->
		</select>
		<span class="block"></span>
	</div>
	<div class="field disabled">
		<label for="concreate">Тип услуги</label>
		<select name="concreate">
			<option value="">--</option>
			<!--if ($concreate)!-->
			<!--foreach ($concreate as $key=>$val)!-->
			<option value="<!--$val.id!-->" <!--if($filter.concreate==$val.id)!--> selected="true"<!--endif!-->><!--$val.name!--></option>
			<!--endforeach!-->
			<!--endif!-->
		</select>
		<span class="block"></span>
	</div>
	<input type="submit" value="Найти акцию" class="submit">
	<!--if($empty)!-->
		<a href="/clinics/?city=all" class="clear">Сбросить фильтр</a>
	<!--endif!-->
</form>