<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<div class="top">
	<div class="head">
		<div class="left">
			<i>Рейтинг клиники:</i>
			<div class="rating">
				<?=$arResult['clinic_rating']['stars_rating_full']?>
				<span><?=$arResult['clinic_rating']['rating_full']?></span>
			</div>
		</div>
		<?php if (!empty($arResult['reviews'])):?>
			<div class="right">
				<span><i></i>Рекомендовали клинику:</span>
				<b><?=$arResult['count_good']?></b> <i>из</i> <?=$arResult['count_reviews']?>
			</div>
		<?php endif?>
	</div>
	<div class="info">
		<div class="top">
			<div class="left">
				<div class="row">
					<b>Персонал:</b>
					<div class="rating">
						<?=$arResult['clinic_rating']['stars_rating_stuff']?>
						<span><?=$arResult['clinic_rating']['rating_stuff']?></span>
					</div>
				</div>
				<div class="row">
					<b>Бытовые услуги:</b>
					<div class="rating">
						<?=$arResult['clinic_rating']['stars_rating_service']?>
						<span><?=$arResult['clinic_rating']['rating_service']?></span>
					</div>
				</div>
				<div class="row">
					<b>Работа врачей:</b>
					<div class="rating">
						<?=$arResult['clinic_rating']['stars_rating_doctor']?>
						<span><?=$arResult['clinic_rating']['rating_doctor']?></span>
					</div>
				</div>
				<div class="row">
					<b>Цена/качество:</b>
					<div class="rating">
						<?=$arResult['clinic_rating']['stars_rating_quality']?>
						<span><?=$arResult['clinic_rating']['rating_quality']?></span>
					</div>
				</div>
			</div>
			<?php if (!empty($arResult['specialist'])):?>
				<div class="right">
					<b>Лучший врач:</b>
					<div class="user">
						<a href="<?=$arResult['specialist']['professional_link']?>" class="img">
							<?=$arResult['specialist']['logo']?>
						</a>
						<a href="#" class="name">
							<?=$arResult['specialist']['name']?>
						</a>
						<div class="rating">
							<?=$arResult['specialist']['stars']?>
							<span><?=$arResult['specialist']['rating']?></span>
						</div>
					</div>
				</div>
			<?php endif?>
		</div>
		<a href="#" class="submit add_review">Оставить отзыв</a>
	</div>
</div>
<form action="" method="get" name="review_filter" class="sort">
	<input type="hidden" name="clinic_id" value="<?=$arResult['clinic_id']?>" />
	<div class="field">
		<label for="problem_id">Проблема или услуга:</label>
		<select name="problem_id" id="problem_id">
			<option value="0">--</option>
			<?php if(!empty($arResult['problems'])): ?>
				<?php foreach($arResult['problems'] as $arProblem): ?>
					<option value="<?=$arProblem['id']?>"<?=$arResult['filter']['problem_id']==$arProblem['id'] ? ' selected="true"' : ''?>><?=$arProblem['name']?></option>
				<?php endforeach; ?>
			<?php endif; ?>
		</select>
	</div>
	<div class="field">
		<label for="specialist_id">Врач:</label>
		<select name="specialist_id" id="specialist_id">
			<option value="0">--</option>
			<?php if (!empty($arResult['specialists'])): ?>
				<?php foreach($arResult['specialists'] as $arSpecialist): ?>
					<option value="<?=$arSpecialist['id']?>"<?=$arResult['filter']['specialist_id']==$arSpecialist['id'] ? ' selected="true"' : ''?>><?=$arSpecialist['name']?></option>
				<?php endforeach; ?>
			<?php endif; ?>
		</select>
	</div>
	<a href="#" class="all">Все отзывы</a>
</form>
<?php if (!empty($arResult['reviews'])):?>
	<div class="items">
		<?php foreach ($arResult['reviews'] as $val):?>
			<div class="item<?=$val['hl']?>">
				<h5>Отзыв №<?=$val['number']?> <b><?=$val['date_add']?></b></h5>
				<div class="top">
					<b>Оценка:</b>
					<div class="rating">
						<?=$val['stars']?>
						<span><?=$val['rating']?></span>
					</div>
					<?php if (!empty($val['moderate'])):?>
						<i>Отзыв проверяется</i>
					<?php endif?>
				</div>
				<ul class="scope">
					<li>
						<b>Проблема или услуга:</b>
						<?=$val['problem']?>
					</li>
					<li>
						<b>Врач:</b>
						<a href="<?=$val['professional_link']?>"><?=$val['professional_name']?></a>
					</li>
					<li>
						<b>Пациент:</b>
						<?=$val['user_name']?>
					</li>
					<li>
						<b>Дата посещения:</b>
						<?=$val['date_visit']?>
					</li>
				</ul>
				<div class="row message">
					<p class="positive"><span>+</span><?=$val['positive_description']?></p>
					<p class="negative"><span>-</span><?=$val['negative_description']?></p>
					<?php if ($val['is_recomended'] == 1):?>
						<b>Пациент рекомендовал клинику</b>
					<?php endif?>
				</div>
				<?php if (!empty($val['answer_clinic'])):?>
					<div class="row clinic">
						<b>Ответ клиники</b>
						<p><?=$val['answer_clinic']?></p>
					</div>
				<?php endif?>
				<?php if (!empty($val['answer'])):?>
					<div class="row manager">
						<b>Комментарий администрации EsteLife</b>
						<p><?=$val['answer']?></p>
					</div>
				<?php endif?>
			</div>
		<?php endforeach?>
	</div>
<?php endif?>

