<form name="preparations" class="filter" method="get" action="/preparations/" >
	<div class="title">
		<h4>Поиск препарата</h4>
		<!--		<span>Найдено 6 акций</span>-->
	</div>
	<div class="field">
		<label for="name">Наименование</label>
		<input name="name" type="text" value="<!--$filter.name!-->" class="text"/>
		<span class="block"></span>
	</div>
	<div class="field">
		<label for="type">Назначение</label>
		<select name="type" >
			<option value="">--</option>
			<option value="1" <!--if($filter.type===1)!--> selected="true"<!--endif!-->>Мезотерапия</option>
			<option value="3" <!--if($filter.type===3)!--> selected="true"<!--endif!-->>Биоревитализация</option>
			<option value="2" <!--if($filter.type=== 2)!--> selected="true"<!--endif!-->>Ботулинотерапия</option>
			<option value="4" <!--if($filter.type===4)!--> selected="true"<!--endif!-->>Контурная пластика</option>
		</select>

		<span class="block"></span>
	</div>
	<div class="field country">
		<label for="country">Страна</label>
		<select name="country" >
			<option value="">--</option>
			<!--if($countries)!-->
				<!--foreach($countries as $key=>$val)!-->
					<option value="<!--$val.ID!-->" <!--if($filter.country===$val.ID)!--> selected="true"<!--endif!-->><!--$val.NAME!--></option>
				<!--endforeach!-->
			<!--endif!-->
		</select>
		<span class="block"></span>
	</div>

	<input type="submit" value="Найти препарат" class="submit">
	<a href="/preparations/" class="clear">Сбросить фильтр</a>
</form>