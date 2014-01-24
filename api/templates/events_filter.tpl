<form name="events" class="filter" method="get" action="/events/" >
	<div class="title">
		<h4>Поиск событий</h4>
		<!--if($count)!-->
			<span class="count-result"><!--$count!--></span>
		<!--endif!-->
	</div>
	<div class="field">
		<label for="name">Наименование</label>
		<input name="name" id="name" type="text" value="<!--$filter.name!-->" class="text"/>
		<span class="block"></span>
	</div>
	<div class="field country">
		<label for="country">Страна</label>
		<select name="country" data-rules="get_city:select[name=city]">
			<option value="all">--</option>
			<!--if($countries)!-->
				<!--foreach ($countries as $key=>$val)!-->
					<option value="<!--$val.ID!-->"<!--if($filter.country==$val.ID)!--> selected="true"<!--endif!-->><!--$val.NAME!--></option>
				<!--endforeach!-->
			<!--endif!-->
		</select>
		<span class="block"></span>
	</div>
	<div class="field<!--if(!$cities)!--> disabled<!--endif!-->">
		<label for="city">Город</label>
		<select name="city" id="city">
			<option value="all">--</option>
			<!--if ($cities)!-->
				<!--foreach ($cities as $key=>$val)!-->
					<option value="<!--$val.ID!-->"<!--if($filter.city==$val.ID)!--> selected="true"<!--endif!-->><!--$val.NAME!--></option>
				<!--endforeach!-->
			<!--endif!-->
		</select>
		<span class="block"></span>
	</div>

	<div class="field">
		<label for="direction" class="checkbox-label">Направление</label>
		<input type="checkbox" name="direction[]" value="1" title="Пластическая хирургия"<!--if(1 in $filter.direction)!--> checked="true"<!--endif!--> />
		<input type="checkbox" name="direction[]" value="2" title="Косметология"<!--if(2 in $filter.direction)!--> checked="true"<!--endif!--> />
		<input type="checkbox" name="direction[]" value="4" title="Дерматология"<!--if(4 in $filter.direction)!--> checked="true"<!--endif!--> />
		<input type="checkbox" name="direction[]" value="3" title="Косметика"<!--if(3 in $filter.direction)!--> checked="true"<!--endif!--> />
		<input type="checkbox" name="direction[]" value="11" title="Менеджмент"<!--if(11 in $filter.direction)!--> checked="true"<!--endif!--> />
		<span class="block"></span>
	</div>

	<div class="field">
		<label for="type" class="checkbox-label">Формат</label>
		<input type="checkbox" name="type[]" value="1" title="Форум"<!--if(1 in $filter.type)!--> checked="true"<!--endif!--> />
		<input type="checkbox" name="type[]" value="2" title="Выставка"<!--if(2 in $filter.type)!--> checked="true"<!--endif!--> />
		<input type="checkbox" name="type[]" value="4" title="Тренинг"<!--if(4 in $filter.type)!--> checked="true"<!--endif!--> />
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
	<!--if($empty)!-->
		<a href="/events/?country=all&city=all" class="clear">Сбросить фильтр</a>
	<!--endif!-->
</form>