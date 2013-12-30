<form name="events" class="filter" method="get" action="/events/" >
	<div class="title">
		<h4>Поиск событий</h4>
		<!--<span>Найдено 6 акций</span>-->
	</div>
	<div class="field">
		<label for="name">Наименование</label>
		<input name="name" id="name" type="text" value="<!--$filter.name!-->" class="text"/>
		<span class="block"></span>
	</div>
	<div class="field country">
		<label for="country">Страна</label>
		<select name="country" id="country" data-rules="get_city:select[name=city]">
			<option value="">--</option>
			<!--if($countries)!-->
				<!--foreach ($countries as $key=>$val)!-->
					<option value="<!--$val.ID!-->"<!--if($filter.country==$val.ID)!--> selected="true"<!--endif!-->><!--$val.NAME!--></option>
				<!--endforeach!-->
			<!--endif!-->
		</select>
		<span class="block"></span>
	</div>
	<div class="field<!--if($cities)!--> disabled<!--endif!-->">
		<label for="city">Город</label>
		<select name="city" id="city">
			<option value="">--</option>
			<!--if ($cities)!-->
				<!--foreach ($cities as $key=>$val)!-->
					<option value="<!--$val.ID!-->"<!--if($filter.city==$val.ID)!--> selected="true"<!--endif!-->><!--$val.NAME!--></option>
				<!--endforeach!-->
			<!--endif!-->
		</select>
		<span class="block"></span>
	</div>

	<div class="field">
		<label for="direction">Направление</label>
		<select name="direction" id="direction">
			<option value="">--</option>
			<option value="1"<!--if($filter.direction==1)!--> selected="true"<!--endif!-->>Пластическая хирургия</option>
			<option value="2"<!--if($filter.direction==2)!--> selected="true"<!--endif!-->>Косметология</option>
			<option value="4"<!--if($filter.direction==4)!--> selected="true"<!--endif!-->>Дерматология</option>
			<option value="3"<!--if($filter.direction==3)!--> selected="true"<!--endif!-->>Косметика</option>
			<option value="11"<!--if($filter.direction==11)!--> selected="true"<!--endif!-->>Менеджмент</option>
		</select>
		<span class="block"></span>
	</div>

	<div class="field">
		<label for="type">Формат</label>
		<select name="type" id="type">
			<option value="">--</option>
			<option value="1"<!--if($filter.type==1)!--> selected="true"<!--endif!-->>Форум</option>
			<option value="2"<!--if($filter.type==2)!--> selected="true"<!--endif!-->>Выставка</option>
			<option value="4"<!--if($filter.type==4)!--> selected="true"<!--endif!-->>Тренинг</option>
		</select>
		<span class="block"></span>
	</div>

	<div class="field date">
		<label for="date">Период проведения</label>
		<div class="from text date">
			<input type="text" id="date" class="text" name="date_from" value="<!--$filter.date_from!-->" />
			<i></i>
		</div>
		<div class="to text date">
			<input type="text" class="text" name="date_to" value="<!--$filter.date_to!-->" />
			<i></i>
		</div>
		<span class="block"></span>
	</div>

	<input type="submit" value="Найти события" class="submit">
	<a href="/events/" class="clear">Сбросить фильтр</a>
</form>