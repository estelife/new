<form name="apparatuses-makers" class="filter" method="get" action="/apparatuses-makers/" >
	<div class="title">
		<h4>Поиск производителя</h4>
		<!--		<span>Найдено 6 акций</span>-->
	</div>
	<div class="field">
		<label for="name">Наименование</label>
		<input name="name" type="text" value="<!--$filter.name!-->" class="text"/>
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

	<input type="submit" value="Найти производителя" class="submit">
	<a href="/apparatuses-makers/" class="clear">Сбросить фильтр</a>
</form>