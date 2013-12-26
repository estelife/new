<!--$i=0!-->
<!--foreach($result as $key=>$val)!-->
	<div class="item<!--if($val.IS_VIDEO)!--> video<!--endif!--> asd <!--if($i%6==0)!--> last<!--endif!-->" data-id="<!--$val.ID!-->">
		<!--if($val.IS_VIDEO)!--><span></span><!--endif!-->
		<img src="<!--$val.IMG!-->" alt="<!--$val.NAME!-->" title="<!--$val.NAME!-->" width="146px" height="100px" />
		<div class="border"></div>
	</div>
<!--$i++!-->
<!--endforeach!-->