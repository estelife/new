<form name="preparations" class="filter" method="get" action="<!--$link!-->" >
	<div class="title">
		<h4><!--$find_title!--></h4>
		<!--if($count)!-->
			<span class="count-result"><!--$count!--></span>
		<!--endif!-->
	</div>
	<!--if($filter_access.name)!-->
		<div class="field">
			<label for="name">Наименование</label>
			<input name="name" type="text" value="<!--$filter.name!-->" class="text"/>
			<span class="block"></span>
		</div>
	<!--endif!-->
	<!--if($filter_access.company_name)!-->
		<div class="field">
			<label for="company_name">Производитель</label>
			<input name="company_name" type="text" value="<!--$filter.company_name!-->" class="text"/>
			<span class="block"></span>
		</div>
	<!--endif!-->
	<!--if($filter_access.type)!-->
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
	<!--endif!-->
	<!--if($filter_access.countries)!-->
		<div class="field country">
			<label for="country">Страна</label>
			<select name="country" >
				<option value="all">--</option>
				<!--if($countries)!-->
					<!--foreach($countries as $key=>$val)!-->
						<option value="<!--$val.ID!-->" <!--if($filter.country===$val.ID)!--> selected="true"<!--endif!-->><!--$val.NAME!--></option>
					<!--endforeach!-->
				<!--endif!-->
			</select>
			<span class="block"></span>
		</div>
	<!--endif!-->
	<input type="submit" value="<!--$find!-->" class="submit">
	<!--if($empty)!-->
		<a href="<!--$link!-->?country=all" class="clear">Сбросить фильтр</a>
	<!--endif!-->
</form>