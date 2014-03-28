<!--if($reviews)!-->
	<div class="title">
		<h3>Ваш отзыв о данной клинике</h3>
		<p>Перед размещением мы просим Вас ознакомиться с <a href="#" class="show_terms">правилами размещения отзывов</a></p>
	</div>
	<form action="" name="add_review" method="post">
		<input type="hidden" name="clinic_id" value="<!--$reviews.clinic_id!-->" />
		<!--if ($reviews.user_id)!-->
			<input type="hidden" name="user_id" value="<!--$reviews.user_id!-->" />
		<!--endif!-->
		<div class="group personal">
			<h4>Личные данные</h4>
			<div class="field require" data-handler="user_name">
				<label for="user_name"><i>*</i>Имя</label>
				<input type="text" name="user_name" id="user_name" class="text" value="<!--$reviews.user_name!-->" />
			</div>
			<div class="field">
				<label for="user_last_name">Фамилия</label>
				<input type="text" name="user_last_name" id="user_last_name" class="text" value="<!--$reviews.user_last_name!-->" />
			</div>
			<div class="field date require" data-handler="date_visit">
				<label for="date_visit"><i>*</i>Дата посещения</label>
				<input type="text" name="date_visit" id="date_visit" class="text" value="" />
				<i class="calendar"></i>
			</div>
			<div class="field require" data-handler="user_email">
				<label for="user_email"><i>*</i>E-mail</label>
				<input type="text" name="user_email" id="user_email" class="text" value="<!--$reviews.user_email!-->" />
			</div>
			<div class="field">
				<label for="user_phone">Телефон</label>
				<input type="text" name="user_phone" id="user_phone" class="text" value="<!--$reviews.user_phone!-->" />
			</div>
		</div>
		<div class="group problem" data-handler="problem_id">
			<h4><i>*</i>Проблема или услуга обращения в клинику</h4>
			<div class="field">
				<select name="problem_id">
					<option value="0">--</option>
					<!--if ($reviews.problems)!-->
						<!--foreach($reviews.problems as $key=>$arProblem)!-->
							<option value="<!--$arProblem.id!-->"<!--if($reviews.problem_id == $arProblem.id)!--> selected="true"<!--endif!-->><!--$arProblem.name!--></option>
						<!--endforeach!-->
					<!--endif!-->
				</select>
			</div>
			<span class="or">или</span>
			<div class="field">
				<input type="text" name="problem_name" id="problem_name" class="text" value="" />
			</div>
		</div>
		<div class="group prof" data-handler="specialist_id">
			<h4><i>*</i>Специалист, с которым Вы общались</h4>
			<div class="field">
				<select name="specialist_id">
					<option value="0">--</option>
						<!--if ($reviews.specialists)!-->
							<!--foreach($reviews.specialists as $key=>$arSpecialist)!-->
								<option value="<!--$arSpecialist.id!-->"<!--if($reviews.specialist_id == $arSpecialist.id)!--> selected="true"<!--endif!-->><!--$arSpecialist.name!--></option>
							<!--endforeach!-->
						<!--endif!-->
				</select>
			</div>
			<span class="or">или</span>
			<div class="field">
				<input type="text" name="specialist_name" id="specialist_name" class="text" value="" />
			</div>
		</div>
		<div class="group ratings">
			<h4><i>*</i>Оцените клинику по следующим параметрам</h4>
			<div class="field" data-handler="rating_doctor">
				<em>Работа врача</em>
				<div class="rating" id="rating_doctor">
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<span><b>0</b> (никак)</span>
				</div>
				<input type="hidden" name="rating_doctor" value="0" />
			</div>
			<div class="field" data-handler="rating_stuff">
				<em>Работа персонала</em>
				<div class="rating" id="rating_stuff">
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<span><b>0</b> (никак)</span>
				</div>
				<input type="hidden" name="rating_stuff" value="0" />
			</div>
			<div class="field" data-handler="rating_service">
				<em>Бытовые условия</em>
				<div class="rating" id="rating_service">
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<span><b>0</b> (никак)</span>
				</div>
				<input type="hidden" name="rating_service" value="0" />
			</div>
			<div class="field" data-handler="rating_quality">
				<em>Цена / качество</em>
				<div class="rating" id="rating_quality">
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<span><b>0</b> (никак)</span>
				</div>
				<input type="hidden" name="rating_quality" value="0" />
			</div>
		</div>
		<div class="group areas">
			<div class="field require" data-handler="positive">
				<span>+</span>
				<label for="positive"><i>*</i>Понравилось</label>
				<textarea name="positive" id="positive"></textarea>
			</div>
			<div class="field require" data-handler="negative">
				<span>-</span>
				<label for="negative"><i>*</i>Не понравилось</label>
				<textarea name="negative" id="negative"></textarea>
			</div>
		</div>
		<div class="group require radios" data-handler="recommend">
			<h4><i>*</i>Порекомендуете ли Вы клинику своим близким и друзьям?</h4>
			<input type="radio" name="recommend" value="1" title="Да" />
			<input type="radio" name="recommend" value="2" title="Нет" />
			<input type="radio" name="recommend" value="3" title="Затрудняюсь ответить" />
		</div>
		<div class="group">
			<div data-handler="read_term">
				<input type="checkbox" name="read_term" value="1" />
				Я ознакомлен(а) с <a href="#" class="show_terms">правилами размещения отзывов</a>
			</div>
			<input type="submit" class="submit" value="Оставить отзыв">
		</div>
	</form>
<!--endif!-->