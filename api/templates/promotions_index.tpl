<!--foreach($list as $key=>$val)!-->
	<div class="item promotion">
		<span class="perc"><!--$val.sale!-->%</span>
		<a href="<!--$val.link!-->">
			<img src="<!--$val.src!-->" width="227px" height="158px" alt="<!--$val.name!-->" title="<!--$val.name!-->">
		</a>
		<h3><!--$val.name!--></h3>
		<div class="cols prices">
			<b><!--$val.new_price!--> <i></i></b>
			<s><!--$val.old_price!--> <i></i></s>
		</div>
		<div class="cols time">
			<!--$val.time!--> <!--$val.day!-->
			<i></i>
		</div>
	</div>
<!--endforeach!-->
