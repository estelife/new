<div class="title">
	<h3>Ваш отзыв о данной клинике</h3>
	<p>Перед размещением мы просим Вас ознакомиться с <a href="#" target="_blank">правилами размещения отзывов</a></p>
</div>
<?php if (isset($arResult['error'])): ?>
	<div class="error"><?=$arResult['error']['message']?></div>
<?php endif; ?>
<form action="" name="add_review" method="post">
	<input type="hidden" name="clinic_id" value="<?=$arResult['clinic_id']?>" />
	<?php if (!empty($arResult['user_id'])): ?>
		<input type="hidden" name="user_id" value="<?=$arResult['user_id']?>" />
	<?php endif; ?>
	<div class="group personal">
		<h4>Личные данные</h4>
		<div class="field require<?=isset($arResult['errors']['user_name']) ? ' error' : ''?>">
			<label for="user_name"><i>*</i>Имя</label>
			<input type="text" name="user_name" id="user_name" class="text" value="<?=isset($arResult['user_name']) ? $arResult['user_name'] : ''?>" />
		</div>
		<div class="field">
			<label for="user_last_name">Фамилия</label>
			<input type="text" name="user_last_name" id="user_last_name" class="text" value="<?=isset($arResult['user_last_name']) ? $arResult['user_last_name'] : ''?>" />
		</div>
		<div class="field date require<?=isset($arResult['errors']['date_visit']) ? ' error' : ''?>">
			<label for="date_visit"><i>*</i>Дата посещения</label>
			<input type="text" name="date_visit" id="date_visit" class="text" value="<?=isset($arResult['date_visit']) ? $arResult['date_visit'] : ''?>" />
			<i class="calendar"></i>
		</div>
		<div class="field require<?=isset($arResult['errors']['user_email']) ? ' error' : ''?>">
			<label for="user_email"><i>*</i>E-mail</label>
			<input type="text" name="user_email" id="user_email" class="text" value="<?=isset($arResult['user_email']) ? $arResult['user_email'] : ''?>" />
		</div>
		<div class="field<?=isset($arResult['errors']['user_phone']) ? ' error' : ''?>">
			<label for="user_phone">Телефон</label>
			<input type="text" name="user_phone" id="user_phone" class="text" value="<?=isset($arResult['user_phone']) ? $arResult['user_phone'] : ''?>" />
		</div>
	</div>
	<div class="group problem<?=isset($arResult['errors']['problem_id']) ? ' error' : ''?>">
		<h4><i>*</i>Проблема или услуга обращения в клинику</h4>
		<div class="field">
			<select name="problem_id">
				<option value="0">--</option>
				<?php if (!empty($arResult['problems'])): ?>
					<?php foreach($arResult['problems'] as $arProblem): ?>
						<option value="<?=$arProblem['id']?>"<?=(isset($arResult['problem_id']) && $arResult['problem_id'] == $arProblem['id']) ? ' selected="true"' : ''?>><?=$arProblem['name']?></option>
					<?php endforeach; ?>
				<?php endif; ?>
			</select>
		</div>
		<span class="or">или</span>
		<div class="field">
			<input type="text" name="problem_name" id="problem_name" class="text" value="<?=isset($arResult['problem_name']) ? $arResult['problem_name'] : ''?>" />
		</div>
	</div>
	<div class="group prof<?=isset($arResult['errors']['specialist_id']) ? ' error' : ''?>">
		<h4><i>*</i>Специалист, с которым Вы общались</h4>
		<div class="field">
			<select name="specialist_id">
			<option value="0">--</option>
				<?php if (!empty($arResult['specialists'])): ?>
					<?php foreach($arResult['specialists'] as $key=>$arSpecialist): ?>
						<option value="<?=$key?>"<?=(isset($arResult['specialist_id']) && $arResult['specialist_id'] == $key) ? ' selected="true"' : ''?>><?=$arSpecialist['name']?></option>
					<?php endforeach; ?>
				<?php endif; ?>
			</select>
		</div>
		<span class="or">или</span>
		<div class="field">
			<input type="text" name="specialist_name" id="specialist_name" class="text" value="<?=isset($arResult['specialist_name']) ? $arResult['specialist_name'] : ''?>" />
		</div>
	</div>
	<div class="group ratings">
		<h4><i>*</i>Оцените клинику по следующим параметрам</h4>
		<div class="field<?=isset($arResult['errors']['rating_doctor']) ? ' error' : ''?>">
			<em>Работа врача</em>
			<div class="rating" id="rating_doctor">
				<?php for($i=1; $i<6; $i++): ?>
					<a href="#"<?=$arResult['rating_doctor']>=$i ? ' class="active"' : ''?>></a>
				<?php endfor; ?>
				<span><b><?=$arResult['rating_doctor']?></b> (<?=$arResult['rating_text'][$arResult['rating_doctor']]?>)</span>
			</div>
			<input type="hidden" name="rating_doctor" value="<?=$arResult['rating_doctor']?>" />
		</div>
		<div class="field<?=isset($arResult['errors']['rating_stuff']) ? ' error' : ''?>">
			<em>Работа персонала</em>
			<div class="rating" id="rating_stuff">
				<?php for($i=1; $i<6; $i++): ?>
					<a href="#"<?=$arResult['rating_stuff']>=$i ? ' class="active"' : ''?>></a>
				<?php endfor; ?>
				<span><b><?=$arResult['rating_stuff']?></b> (<?=$arResult['rating_text'][$arResult['rating_stuff']]?>)</span>
			</div>
			<input type="hidden" name="rating_stuff" value="<?=$arResult['rating_stuff']?>" />
		</div>
		<div class="field<?=isset($arResult['errors']['rating_service']) ? ' error' : ''?>">
			<em>Бытовые условия</em>
			<div class="rating" id="rating_service">
				<?php for($i=1; $i<6; $i++): ?>
					<a href="#"<?=$arResult['rating_service']>=$i ? ' class="active"' : ''?>></a>
				<?php endfor; ?>
				<span><b><?=$arResult['rating_service']?></b> (<?=$arResult['rating_text'][$arResult['rating_service']]?>)</span>
			</div>
			<input type="hidden" name="rating_service" value="<?=$arResult['rating_service']?>" />
		</div>
		<div class="field<?=isset($arResult['errors']['rating_quality']) ? ' error' : ''?>">
			<em>Цена / качество</em>
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
	<div class="group">
		<div class="<?=isset($arResult['errors']['read_term']) ? 'error' : ''?>">
			<input type="checkbox" name="read_term" value="1"<?=isset($arResult['read_term']) && $arResult['read_term']==1 ? ' checked="true"' : ''?> />
			Я ознакомлен(а) с <a href="#" target="_blank">правилами размещения отзывов</a>
		</div>
		<input type="submit" class="submit" value="Оставить отзыв">
	</div>
</form>