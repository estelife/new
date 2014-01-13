<form name="apparatuses" class="filter" method="get" action="/apparatuses/" >
	<div class="title">
		<h4>Поиск аппарата</h4>
		<!--		<span>Найдено 6 акций</span>-->
	</div>
	<div class="field">
		<label for="name">Наименование</label>
		<input name="name" type="text" value="<!--$filter.name!-->" class="text" />
		<span class="block"></span>
	</div>
	<div class="field">
		<label for="type">Тип</label>
		<select name="type" >
			<option value="">--</option>
			<option value="1" <!--if($filter.type === 1)!--> selected="true"<!--endif!-->>Anti-Age терапия</option>
			<option value="7" <!--if($filter.type === 7)!--> selected="true"<!--endif!-->>Диагностика</option>
			<option value="2" <!--if($filter.type === 2)!--> selected="true"<!--endif!-->>Коррекция фигуры</option>
			<option value="9" <!--if($filter.type === 9)!--> selected="true"<!--endif!-->>Микропигментация</option>
			<option value="5" <!--if($filter.type === 5)!--> selected="true"<!--endif!-->>Микротоки</option>
			<option value="4" <!--if($filter.type === 4)!--> selected="true"<!--endif!-->>Миостимуляция</option>
			<option value="6" <!--if($filter.type === 6)!--> selected="true"<!--endif!-->>Лазеры</option>
			<option value="8" <!--if($filter.type === 8)!--> selected="true"<!--endif!-->>Реабилитация</option>
			<option value="3" <!--if($filter.type === 3)!--> selected="true"<!--endif!-->>Эпиляция</option>
		</select>

		<span class="block"></span>
	</div>
	<div class="field country">
		<label for="country">Страна</label>
		<select name="country" >
			<option value="all">--</option>
			<!--if($countries)!-->
				<!--foreach($countries as $key=>$val)!-->
					<option value="<!--$val.ID!-->" <!--if($filter.country === $val.ID)!--> selected="true"<!--endif!-->><!--$val.NAME!--></option>
				<!--endforeach!-->
			<!--endif!-->
		</select>
		<span class="block"></span>
	</div>

	<input type="submit" value="Найти аппарат" class="submit">
	<!--if($empty)!-->
		<a href="/apparatuses/?country=all" class="clear">Сбросить фильтр</a>
	<!--endif!-->
</form>