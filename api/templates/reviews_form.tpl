<!--if(form)!-->
	<div class="title">
		<h3>Ваш отзыв о данной клинике</h3>
		<p>Перед размещением мы просим Вас ознакомиться с <a href="#" target="_blank">правилами размещения отзывов</a></p>
	</div>
	<!--if ($form.error)!-->
		<div class="error"><!--$form.error.message!--></div>
	<!--endif!-->
	<form action="" name="add_review" method="post">
		<input type="hidden" name="clinic_id" value="<!--$form.clinic_id!-->" />
		<!--if ($form.user_id)!-->
			<input type="hidden" name="user_id" value="<!--$form.user_id!-->" />
		<!--endif!-->
		<div class="group personal">
			<h4>Личные данные</h4>
			<div class="field require<!--if($form.errors.user_name)!--> error<!--endif!-->">
				<label for="user_name"><i>*</i>Имя</label>
				<input type="text" name="user_name" id="user_name" class="text" value="<!--form.errors.user_name!-->" />
			</div>
			<div class="field">
				<label for="user_last_name">Фамилия</label>
				<input type="text" name="user_last_name" id="user_last_name" class="text" value="<!--form.errors.user_last_name!-->" />
			</div>
			<div class="field date require<!--if($form.errors.date_visit)!--> error<!--endif!-->">
				<label for="date_visit"><i>*</i>Дата посещения</label>
				<input type="text" name="date_visit" id="date_visit" class="text" value="<!--form.errors.date_visit!-->" />
				<i class="calendar"></i>
			</div>
			<div class="field require<!--if($form.errors.user_email)!--> error<!--endif!-->">
				<label for="user_email"><i>*</i>E-mail</label>
				<input type="text" name="user_email" id="user_email" class="text" value="<!--form.errors.user_email!-->" />
			</div>
			<div class="field<!--if($form.errors.user_phpne)!--> error<!--endif!-->">
				<label for="user_phone">Телефон</label>
				<input type="text" name="user_phone" id="user_phone" class="text" value="<!--form.errors.user_phone!-->" />
			</div>
		</div>
		<div class="group problem<!--if($form.errors.problem_id)!--> error<!--endif!-->">
			<h4><i>*</i>Проблема или услуга обращения в клинику</h4>
			<div class="field">
				<select name="problem_id">
					<option value="0">--</option>
					<!--if ($form.problems)!-->
						<!--foreach($form.problems as $key=>$arProblem)!-->
							<option value="<!--$arProblem.id!-->"<!--if($form.problem_id == $arProblem.id) selected="true"<!--endif!-->><!--$arProblem.name!--></option>
						<!--endforeach!-->
					<!--endif!-->
				</select>
			</div>
			<span class="or">или</span>
			<div class="field">
				<input type="text" name="problem_name" id="problem_name" class="text" value="<!--$form.problem_name!-->" />
			</div>
		</div>
		<div class="group prof<!--if($form.errors.specialist_id)!--> error<!--endif!-->">
			<h4><i>*</i>Специалист, с которым Вы общались</h4>
			<div class="field">
				<select name="specialist_id">
					<option value="0">--</option>
						<!--if ($form.specialists)!-->
							<!--foreach($form.specialists as $key=>$arSpecialist)!-->
								<option value="<!--$key!-->"<!--if($form.specialist_id == $key) selected="true"<!--endif!-->><!--$arSpecialist!--></option>
							<?php endforeach; ?>
						<!--endif!-->
				</select>
			</div>
			<span class="or">или</span>
			<div class="field">
				<input type="text" name="specialist_name" id="specialist_name" class="text" value="<!--$form.specialist_name!-->" />
			</div>
		</div>
		<div class="group ratings<?=isset($arResult['errors']['ratings']) ? ' error' : ''?>">
			<h4><i>*</i>Оцените клинику по следующим параметрам</h4>
			<div class="field">
				<i>Работа врача</i>
				<div class="rating" id="rating_doctor">
					<?php for($i=1; $i<6; $i++): ?>
					<a href="#"<?=$arResult['rating_doctor']>=$i ? ' class="active"' : ''?>></a>
					<?php endfor; ?>
					<span><b><?=$arResult['rating_doctor']?></b> (<?=$arResult['rating_text'][$arResult['rating_doctor']]?>)</span>
				</div>
				<input type="hidden" name="rating_doctor" value="<?=$arResult['rating_doctor']?>" />
			</div>
			<div class="field">
				<i>Работа персонала</i>
				<div class="rating" id="rating_stuff">
					<?php for($i=1; $i<6; $i++): ?>
					<a href="#"<?=$arResult['rating_stuff']>=$i ? ' class="active"' : ''?>></a>
					<?php endfor; ?>
					<span><b><?=$arResult['rating_stuff']?></b> (<?=$arResult['rating_text'][$arResult['rating_stuff']]?>)</span>
				</div>
				<input type="hidden" name="rating_stuff" value="<?=$arResult['rating_stuff']?>" />
			</div>
			<div class="field">
				<i>Бытовые условия</i>
				<div class="rating" id="rating_service">
					<?php for($i=1; $i<6; $i++): ?>
					<a href="#"<?=$arResult['rating_service']>=$i ? ' class="active"' : ''?>></a>
					<?php endfor; ?>
					<span><b><?=$arResult['rating_service']?></b> (<?=$arResult['rating_text'][$arResult['rating_service']]?>)</span>
				</div>
				<input type="hidden" name="rating_service" value="<?=$arResult['rating_service']?>" />
			</div>
			<div class="field">
				<i>Цена / качество</i>
				<div class="rating" id="rating_quality">
					<?php for($i=1; $i<6; $i++): ?>
					<a href="#"<?=$arResult['rating_quality']>=$i ? ' class="active"' : ''?>></a>
					<?php endfor; ?>
					<span><b><?=$arResult['rating_quality']?></b> (<?=$arResult['rating_text'][$arResult['rating_quality']]?>)</span>
				</div>
				<input type="hidden" name="rating_quality" value="<?=$arResult['rating_quality']?>" />
			</div>
		</div>
		<div class="group areas">
			<div class="field require<?=isset($arResult['errors']['positive']) ? ' error' : ''?>">
				<span>+</span>
				<label for="positive"><i>*</i>Понравилось</label>
				<textarea name="positive" id="positive"><?=isset($arResult['positive']) ? $arResult['positive'] : ''?></textarea>
			</div>
			<div class="field require<?=isset($arResult['errors']['negative']) ? ' error' : ''?>">
				<span>-</span>
				<label for="negative"><i>*</i>Не понравилось</label>
				<textarea name="negative" id="negative"><?=isset($arResult['negative']) ? $arResult['negative'] : ''?></textarea>
			</div>
		</div>
		<div class="group require radios<?=isset($arResult['errors']['recommend']) ? ' error' : ''?>">
			<h4><i>*</i>Порекомендуете ли Вы клинику своим близким и друзьям?</h4>
			<input type="radio" name="recommend" value="1" title="Да"<?=isset($arResult['recommend']) && $arResult['recommend']==1 ? ' checked="true"' : ''?> />
			<input type="radio" name="recommend" value="2" title="Нет"<?=isset($arResult['recommend']) && $arResult['recommend']==2 ? ' checked="true"' : ''?> />
			<input type="radio" name="recommend" value="3" title="Затрудняюсь ответить"<?=isset($arResult['recommend']) && $arResult['recommend']==3 ? ' checked="true"' : ''?> />
		</div>
		<div class="group<?=isset($arResult['errors']['read_term']) ? ' error' : ''?>">
			<input type="checkbox" name="read_term" value="1"<?=isset($arResult['read_term']) && $arResult['read_term']==1 ? ' checked="true"' : ''?> />
			Я ознакомлен(а) с <a href="#" target="_blank">правилами размещения отзывов</a>
			<input type="submit" class="submit" value="Оставить отзыв">
		</div>
	</form>
<!--endif!-->