<div class="content">
	<div class="cities-in">
		<!--foreach($active as $key=>$val)!-->
			<div class="cols col1">
				<ul>
				<!--foreach($val as $k=>$v)!-->
					<li <!--if($v.id==$city)!-->class="active"<!--endif!-->><a href="#" class="<!--$v.id!-->"><!--$v.name!--></a></li>
				<!--endforeach!-->
				</ul>
			</div>
		<!--endforeach!-->
	</div>
</div>