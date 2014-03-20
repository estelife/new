<!--if($comments)!-->
	<div class="comments" data-id="<!--$comments.element_id!-->" data-type="<!--$comments.type!-->">
		<h2>Обсуждение</h2>
		<!--if($comments.comments)!-->
			<b class="stat"><!--$comments.count!--></b>
			<div class="items">
				<!--foreach ($comments.comments as $key=>$val)!-->
					<div class="item" id="comment_<!--$val.id!-->">
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
			<!--$comments.form.create_token!-->
			<form name="<!--$comments.form.name!-->" method="<!--$comments.form.method!-->" action="<!--$comments.form.action!-->" id="<!--$comments.form.id!-->">
				<div class="form-in quality-in">
					<!--$comments.form.fields.id!-->
					<!--$comments.form.fields.type!-->
					<div class="col1">
						<div class="field <!--if($comments.errors.comment)!-->error<!--endif!-->">
							<label for="comment">Ваш комментарий<span>Осталось <s>1000 символов</s></span></label>
							<!--$comments.form.fields.comment!-->
						</div>
					</div>
				</div>
				<!--$comments.form.fields.send_comment!-->
			</form>
		<!--else!-->
		<div class="not-auth">
			<p>Комментарии могут оставлять только зарегистрированные пользователи. <a href="/personal/register/?backurl=/<!--$comments.type_string!--><!--$comments.element_id!-->/">Зарегистрироваться</a> или <a href="/personal/auth/?backurl=/<!--$comments.type_string!--><!--$comments.element_id!-->/">авторизоваться</a>.</p>
		</div>
		<!--endif!-->
	</div>
<!--endif!-->