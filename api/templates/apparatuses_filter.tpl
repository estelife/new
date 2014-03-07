<form name="apparatuses" class="filter" method="get" action="/apparatuses/" >
	<div class="title">
		<h4>Поиск аппарата</h4>
		<!--if($count)!-->
			<span class="count-result"><!--$count!--></span>
		<!--endif!-->
	</div>
	<div class="field">
		<label for="name">Наименование</label>
		<input name="name" type="text" value="<!--$filter.name!-->" class="text" />
		<span class="block"></span>
	</div>
	<!--if($types)!-->
		<div class="field">
			<label for="type">Назначение</label>
			<select name="type" >
				<option value="">--</option>
				<!--foreach($types as $key=>$val)!-->
					<option value="<!--$val.id!-->" <!--if($filter.type === $val.id)!--> selected="true"<!--endif!-->><!--$val.name!--></option>
				<!--endforeach!-->
			</select>
			<span class="block"></span>
		</div>
	<!--endif!-->
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