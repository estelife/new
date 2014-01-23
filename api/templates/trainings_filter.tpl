<form name="trainings" class="filter" method="get" action="/trainings/" >
	<div class="title">
		<h4>Поиск обучения</h4>
		<!--if($count)!-->
			<span class="count-result"><!--$count!--></span>
		<!--endif!-->
	</div>
	<div class="field">
		<label for="city">Город</label>
		<select name="city" id="city">
			<option value="all">--</option>
			<!--if($cities)!-->
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
			<option value="5"<!--if($filter.direction==5)!--> selected="true"<!--endif!-->>Ботулинотерапия</option>
			<option value="6"<!--if($filter.direction==6)!--> selected="true"<!--endif!-->>Контурная пластика</option>
			<option value="7"<!--if($filter.direction==7)!--> selected="true"<!--endif!-->>Мезотерапия</option>
			<option value="8"<!--if($filter.direction==8)!--> selected="true"<!--endif!-->>Биоревитализация</option>
			<option value="9"<!--if($filter.direction==9)!--> selected="true"<!--endif!-->>Объемное моделирование</option>
			<option value="10"<!--if($filter.direction==10)!--> selected="true"<!--endif!-->>Безоперационный лифтинг</option>
			<option value="12"<!--if($filter.direction==12)!--> selected="true"<!--endif!-->>Пилинги</option>
			<option value="13"<!--if($filter.direction==13)!--> selected="true"<!--endif!-->>Космецевтика</option>
			<option value="14"<!--if($filter.direction==14)!--> selected="true"<!--endif!-->>Аппаратная косметология</option>
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

	<input type="submit" value="Найти обучение" class="submit">
	<!--if($empty)!-->
		<a href="/trainings/?city=all" class="clear">Сбросить фильтр</a>
	<!--endif!-->
</form>