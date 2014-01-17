<form name="sponsors" class="filter" method="get" action="/sponsors/" >
	<div class="title">
		<h4>Поиск организатора</h4>
		<!--if($count)!-->
			<span class="count-result"><!--$count!--></span>
		<!--endif!-->
	</div>
	<div class="field">
		<label for="name">Наименование</label>
		<input name="name" type="text" value="<!--$filter.name!-->" class="text"/>
		<span class="block"></span>
	</div>
	<div class="field country">
		<label for="country">Страна</label>
		<select name="country" data-rules="get_city:select[name=city]">
			<option value="all">--</option>
			<!--if($countries)!-->
				<!--foreach($countries as $key=>$val)!-->
					<option value="<!--$val.ID!-->" <!--if($filter.country===$val.ID)!--> selected="true"<!--endif!-->><!--$val.NAME!--></option>
				<!--endforeach!-->
			<!--endif!-->
		</select>
		<span class="block"></span>
	</div>
	<div class="field <!--if($cities)!--> <!--else!--> disabled<!--endif!-->">
		<label for="city">Город</label>
		<select name="city" >
			<option value="all">--</option>
			<!--if($cities)!-->
				<!--foreach($cities as $key=>$val)!-->
					<option value="<!--$val.ID!-->" <!--if($filter.city===$val.ID)!--> selected="true"<!--endif!-->><!--$val.NAME!--></option>
				<!--endforeach!-->
			<!--endif!-->
		</select>

		<span class="block"></span>
	</div>

	<input type="submit" value="Найти организатора" class="submit">
	<!--if($empty)!-->
		<a href="/sponsors/?country=all&city=all" class="clear">Сбросить фильтр</a>
	<!--endif!-->
</form>