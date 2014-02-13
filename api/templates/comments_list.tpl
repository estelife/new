<!--if($comments)!-->
	<div class="comments">
		<h2>Обсуждение</h2>
		<!--if($comments.comments)!-->
			<b class="stat"><!--$comments.count!--></b>
			<div class="items">
				<!--foreach ($comments.comments as $key=>$val)!-->
					<div class="item">
						<b><!--$val.name!--></b>
						<i><!--$val.date_create!--></i>
						<p><!--$val.text!--></p>
					</div>
				<!--endforeach!-->
				<div class="more">
					<!--if ($comments.all)!-->
						<a href="#"><span class="hide">Скрыть комментарии</span></a>
					<!--else!-->
						<a href="#"><span>Все комментарии</span></a>
					<!--endif!-->
				</div>
			</div>
		<!--endif!-->
		<!--if($comments.success)!-->
			<div class="success">Ваш комментарий успешно добавлен. Комментарий появится после модерации.</div>
		<!--endif!-->
		<form name="comments" method="post" action="#comment">
			<div class="form-in quality-in">
				<input type="hidden" name="id" value="<!--$comments.element_id!-->">
				<input type="hidden" name="type" value="<!--$comments.type!-->">
				<div class="col1">
					<div class="field <!--if($comments.error.first_name)!-->error<!--endif!-->">
						<label for="first_name">Имя</label>
						<input type="text" name="first_name" id="first_name" class="text" value="">
					</div>
					<div class="field <!--if($comments.error.last_name)!-->error<!--endif!-->">
						<label for="last_name">Фамилия</label>
						<input type="text" name="last_name" id="last_name" class="text" value="">
					</div>
				</div>
				<div class="col2">
					<div class="field <!--if($comments.error.comment)!-->error<!--endif!-->">
						<label for="comment">Ваш комментарий<span>Осталось <s>1000 символов</s></span></label>
						<textarea name="comment" id="comment"></textarea>
					</div>
				</div>
			</div>
			<input type="submit" class="submit" value="Комментировать" name="send_comment">
			<p class="total_error <!--if($comments.error)!-->error<!--endif!-->">! Все поля обязательны к заполнению</p>
		</form>
	</div>
<!--endif!-->