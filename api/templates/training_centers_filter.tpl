<form name="training-centers" class="filter" method="get" action="/training-centers/" >
	<div class="title">
		<h4>Поиск учебного центра</h4>
		<!--if($count)!-->
			<span class="count-result"><!--$count!--></span>
		<!--endif!-->
	</div>
	<div class="field">
		<label for="name">Наименование</label>
		<input name="name" id="name" type="text" value="<!--$filter.name!-->" class="text"/>
		<span class="block"></span>
	</div>
	<div class="field">
		<label for="city">Город</label>
		<select name="city" id="city">
			<option value="all">--</option>
			<!--if($cities)!-->
				<!--foreach($cities as $key=>$val)!-->
					<option value="<!--$val.ID!-->"<!--if($filter.city==$val.ID)!--> selected="true"<!--endif!-->><!--$val.NAME!--></option>
				<!--endforeach!-->
			<!--endif!-->
		</select>

		<span class="block"></span>
	</div>

	<input type="submit" value="Найти учебный центр" class="submit">
	<!--if($empty)!-->
		<a href="/training-centers/?city=all" class="clear">Сбросить фильтр</a>
	<!--endif!-->
</form>