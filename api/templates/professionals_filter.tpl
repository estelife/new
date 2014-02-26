<form name="professionals" class="filter" method="get" action="/professionals/" >
	<div class="title">
		<h4>Поиск специалиста</h4>
		<!--if($count)!-->
			<span class="count-result"><!--$count!--></span>
		<!--endif!-->
	</div>
	<div class="field">
		<label for="name">Наименование</label>
		<input name="name" type="text" value="<!--$filter.name!-->" class="text" />
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

	<input type="submit" value="Найти специалиста" class="submit">
	<!--if($empty)!-->
		<a href="/professionals/?country=all" class="clear">Сбросить фильтр</a>
	<!--endif!-->
</form>