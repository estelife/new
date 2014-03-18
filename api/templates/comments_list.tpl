<!--if($comments)!-->
	<div class="comments" data-id="<!--$comments.element_id!-->" data-type="<!--$comments.type!-->">
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
			<div class="success">Ваш комментарий успешно добавлен.</div>
		<!--endif!-->
		<!--if($comments.auth)!-->
			<form name="comments" method="post" action="#comment">
				<div class="form-in quality-in">
					<input type="hidden" name="id" value="<!--$comments.element_id!-->">
					<input type="hidden" name="type" value="<!--$comments.type!-->">
					<div class="col1">
						<div class="field <!--if($comments.error.comment)!-->error<!--endif!-->">
							<label for="comment">Ваш комментарий<span>Осталось <s>1000 символов</s></span></label>
							<textarea name="comment" id="comment"></textarea>
						</div>
					</div>
				</div>
				<input type="submit" class="submit" value="Комментировать" name="send_comment">
			</form>
		<!--else!-->
		<div class="not-auth">
			<p>Комментарии могут оставлять только зарегистрированные пользователи. <a href="/personal/register/?backurl=/<!--$comments.type!--><!--$comments.element_id!-->/">Зарегистрироваться</a>.</p>
		</div>
		<!--endif!-->
	</div>
<!--endif!-->