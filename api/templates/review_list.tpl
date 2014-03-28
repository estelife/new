<!--if($reviews)!-->
	<div class="top">
		<div class="head">
			<div class="left">
				<i>Рейтинг клиники:</i>
				<div class="rating">
					<!--$reviews.clinic_rating.stars_rating_full!-->
					<span><!--$reviews.clinic_rating.rating_full!--></span>
				</div>
			</div>
			<!--if($reviews.reviews)!-->
				<div class="right">
					<span><i></i>Рекомендовали клинику:</span>
					<b><!--$reviews.count_good!--></b> <i>из</i> <!--$reviews.count_reviews!-->
				</div>
			<!--endif!-->
		</div>
		<div class="info">
			<div class="top">
				<div class="left">
					<div class="row">
						<b>Персонал:</b>
						<div class="rating">
							<!--$reviews.clinic_rating.stars_rating_stuff!-->
							<span><!--$reviews.clinic_rating.rating_stuff!--></span>
						</div>
					</div>
					<div class="row">
						<b>Бытовые услуги:</b>
						<div class="rating">
							<!--$reviews.clinic_rating.stars_rating_service!-->
							<span><!--$reviews.clinic_rating.rating_service!--></span>
						</div>
					</div>
					<div class="row">
						<b>Работа врачей:</b>
						<div class="rating">
							<!--$reviews.clinic_rating.stars_rating_doctor!-->
							<span><!--$reviews.clinic_rating.rating_doctor!--></span>
						</div>
					</div>
					<div class="row">
						<b>Цена/качество:</b>
						<div class="rating">
							<!--$reviews.clinic_rating.stars_rating_quality!-->
							<span><!--$reviews.clinic_rating.rating_quality!--></span>
						</div>
					</div>
				</div>
				<!--if ($reviews.specialist)!-->
					<div class="right">
						<b>Лучший врач:</b>
						<div class="user">
							<a href="<!--$reviews.specialist.professional_link!-->" class="img">
								<!--$reviews.specialist.logo!-->
							</a>
							<a href="#" class="name">
								<!--$reviews.specialist.name!-->
							</a>
							<div class="rating">
								<!--$reviews.specialist.stars!-->
								<span><!--$reviews.specialist.rating!--></span>
							</div>
						</div>
					</div>
				<!--endif!-->
			</div>
			<a href="#" class="submit add_review">Оставить отзыв</a>
		</div>
	</div>
	<form action="" method="get" name="review_filter" class="sort">
		<input type="hidden" name="clinic_id" value="<!--$reviews.clinic_id!-->" />
		<div class="field">
			<label for="problem_id">Проблема или услуга:</label>
			<select name="problem_id" id="problem_id">
				<option value="0">--</option>
				<!--if($reviews.problems)!-->
					<!--foreach($reviews.problems as $key=>$arProblem)!-->
						<option value="<!--$arProblem.id!-->"<!--if($reviews.filter.problem_id==$arProblem.id)!--> selected="true"<!--endif!-->><!--$arProblem.name!--></option>
					<!--endforeach!-->
				<!--endif!-->
			</select>
		</div>
		<div class="field">
			<label for="specialist_id">Врач:</label>
			<select name="specialist_id" id="specialist_id">
				<option value="0">--</option>
				<!--if ($reviews.specialists)!-->
					<!--foreach($reviews.specialists as $val=>$arSpecialist)!-->
						<option value="<!--$arSpecialist.id!-->"<!--if($reviews.filter.specialist_id==$arSpecialist.id)!--> selected="true"<!--endif!-->><!--$arSpecialist.name!--></option>
					<!--endforeach!-->
				<!--endif!-->
			</select>
		</div>
		<!--if($reviews.filter.not_empty)!-->
			<a href="/cl<!--$reviews.clinic_id!-->/" class="all">Все отзывы</a>
		<!--endif!-->
	</form>
	<!--if ($reviews.reviews)!-->
		<div class="items">
			<!--foreach ($reviews.reviews as $key=>$val)!-->
				<div class="item<!--$val.hl!-->">
					<h5>Отзыв №<!--$val.number!--> <b><!--$val.date_add!--></b></h5>
					<div class="top">
						<b>Оценка:</b>
						<div class="rating">
							<!--$val.stars!-->
							<span><!--$val.rating!--></span>
						</div>
						<!--if ($val.moderate)!-->
							<i>Отзыв проверяется</i>
						<!--endif!-->
					</div>
					<ul class="scope">
						<li>
							<b>Проблема или услуга:</b>
							<!--$val.problem!-->
						</li>
						<li>
							<b>Врач:</b>
							<!--if($val.professional_link)!-->
								<a href="<!--$val.professional_link!-->"><!--$val.professional_name!--></a>
							<!--else!-->
								<!--$val.professional_name!-->
							<!--endif!-->
						</li>
						<li>
							<b>Пациент:</b>
							<!--$val.user_name!-->
						</li>
						<li>
							<b>Дата посещения:</b>
							<!--$val.date_visit!-->
						</li>
					</ul>
					<div class="row message">
						<p class="positive"><span>+</span><!--$val.positive_description!--></p>
						<p class="negative"><span>-</span><!--$val.negative_description!--></p>
						<!--if ($val.is_recomended == 1)!-->
							<b>Пациент рекомендовал клинику</b>
						<!--endif!-->
					</div>
					<!--if (($val.answer_clinic)!-->
						<div class="row clinic">
							<b>Ответ клиники</b>
							<p></p>
						</div>
					<!--endif!-->
					<!--if ($val.answer)!-->
						<div class="row manager">
							<b>Комментарий администрации EsteLife</b>
							<p></p>
						</div>
					<!--endif!-->
				</div>
			<!--endforeach!-->
		</div>
	<!--endif!-->
<!--endif!-->