<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>

<div class="top">
	<div class="head">
		<div class="left">
			<i>Рейтинг клиники:</i>
			<div class="rating">
				<?=$arResult['clinic_rating']['stars_full']?>
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
						<?=$arResult['clinic_rating']['stars_stuff']?>
						<span><?=$arResult['clinic_rating']['rating_stuff']?></span>
					</div>
				</div>
				<div class="row">
					<b>Бытовые услуги:</b>
					<div class="rating">
						<?=$arResult['clinic_rating']['stars_service']?>
						<span><?=$arResult['clinic_rating']['rating_service']?></span>
					</div>
				</div>
				<div class="row">
					<b>Работа врачей:</b>
					<div class="rating">
						<?=$arResult['clinic_rating']['stars_doctor']?>
						<span><?=$arResult['clinic_rating']['rating_doctor']?></span>
					</div>
				</div>
				<div class="row">
					<b>Цена/качество:</b>
					<div class="rating">
						<?=$arResult['clinic_rating']['stars_quality']?>
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
<?php if (!empty($arResult['reviews'])):?>
	<form action="" method="get" class="sort">
		<div class="field">
			<label for="">Проблема или услуга:</label>
			<div class="select"><div class="selected"><a href="javascript:void(0)" class="value"><span data-value="359">--</span></a><a href="javascript:void(0)" class="arrow"></a></div><div class="items" style="overflow: hidden; padding: 0px;"><em class="item"><a data-value="" href="javascript:void(0)">-- </a></em></div></div>
		</div>
		<div class="field">
			<label for="">Врач:</label>
			<div class="select"><div class="selected"><a href="javascript:void(0)" class="value"><span data-value="359">--</span></a><a href="javascript:void(0)" class="arrow"></a></div><div class="items" style="overflow: hidden; padding: 0px;"><em class="item"><a data-value="" href="javascript:void(0)">-- </a></em></div></div>
		</div>
		<a href="#" class="all">Все отзывы</a>
	</form>
	<div class="items">
		<?php foreach ($arResult['reviews'] as $val):?>
			<div class="item hl">
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
						<p>Давно выяснено, что при оценке дизайна и композиции читаемый текст мешает сосредоточиться. Lorem Ipsum используют потому, что тот обеспечивает более или менее стандартное заполнение шаблона.</p>
					</div>
				<?php endif?>
				<?php if (!empty($val['answer'])):?>
					<div class="row manager">
						<b>Комментарий администрации EsteLife</b>
						<p>Давно выяснено, что при оценке дизайна и композиции читаемый текст мешает сосредоточиться.</p>
					</div>
				<?php endif?>
			</div>
		<?php endforeach?>
	</div>
<?php endif?>

