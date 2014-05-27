<!--if($detail)!-->
	<div class="item detail product">
		<h1><!--$detail.name!--></h1>
		<div class="current">
			<div class="img">
				<div class="img-in">
					<!--if($detail.img)!-->
						<!--$detail.img!-->
					<!--else!-->
						<div class="default">Изображение отсутствует</div>
					<!--endif!-->
				</div>
			</div>
			<ul>
				<!--if($detail.country_name)!-->
					<li class="country c<!--$detail.country_id!-->"><!--$detail.country_name!--></li>
				<!--endif!-->
				<!--if($detail.company_name)!-->
					<li>Производитель: <a href="<!--$detail.company_link!-->"><!--$detail.company_name!--></a></li>
				<!--endif!-->
			</ul>
		</div>
		<p><!--$detail.detail_text!--></p>

		<div class="properties">
			<!--if($detail.action)!-->
				<div class="el-tab">
					<h3><a href="#">Действие</a></h3>
					<div class="none"><!--$detail.action!--></div>
				</div>
			<!--endif!-->
			<!--if($detail.func)!-->
				<div class="el-tab">
					<h3><a href="#">Функции</a></h3>
					<div class="none"><!--$detail.func!--></div>
				</div>
			<!--endif!-->
			<!--if($detail.evidence)!-->
				<div class="el-tab">
					<h3><a href="#">Показания</a></h3>
					<div class="none"><!--$detail.evidence!--></div>
				</div>
			<!--endif!-->
			<!--if($detail.contra)!-->
				<div class="el-tab">
					<h3><a href="#">Противопоказания</a></h3>
					<div class="none"><!--$detail.contra!--></div>
				</div>
			<!--endif!-->
			<!--if($detail.registration)!-->
				<div class="el-tab">
					<h3><a href="#">Регистрация</a></h3>
					<div class="none"><!--$detail.registration!--></div>
				</div>
			<!--endif!-->
			<!--if($detail.advantages)!-->
				<div class="el-tab">
					<h3><a href="#">Преимущества</a></h3>
					<div class="none"><!--$detail.advantages!--></div>
				</div>
			<!--endif!-->
			<!--if($detail.procedure)!-->
				<div class="el-tab">
					<h3><a href="#">Курс процедур</a></h3>
					<div class="none"><!--$detail.procedure!--></div>
				</div>
			<!--endif!-->
			<!--if($detail.undesired)!-->
				<div class="el-tab">
					<h3><a href="#">Побочные эффекты</a></h3>
					<div class="none"><!--$detail.undesired!--></div>
				</div>
			<!--endif!-->
			<!--if($detail.specs)!-->
				<div class="el-tab">
					<h3><a href="#">Технические характеристики</a></h3>
					<div class="none"><!--$detail.specs!--></div>
				</div>
			<!--endif!-->
			<!--if($detail.security)!-->
				<div class="el-tab">
					<h3><a href="#">Безопасность</a></h3>
					<div class="none"><!--$detail.security!--></div>
				</div>
			<!--endif!-->
			<!--if($detail.protocol)!-->
				<div class="el-tab">
					<h3><a href="#">Протокол процедуры</a></h3>
					<div class="none"><!--$detail.protocol!--></div>
				</div>
			<!--endif!-->
			<!--if($detail.equipment)!-->
				<div class="el-tab">
					<h3><a href="#">Комплектация</a></h3>
					<div class="none"><!--$detail.equipment!--></div>
				</div>
			<!--endif!-->
			<!--if ($detail.effect)!-->
				<div class="el-tab">
					<h3><a href="#">Достигаемый эффект</a></h3>
					<div class="none"><!--$detail.effect!--></div>
				</div>
			<!--endif!-->
			<!--if ($detail.specialist)!-->
				<div class="el-tab">
					<h3><a href="#">Рекомендации специалисту</a></h3>
					<div class="none"><!--$detail.specialist!--></div>
				</div>
			<!--endif!-->
			<!--if ($detail.patient)!-->
				<div class="el-tab">
					<h3><a href="#">Рекомендации пациенту</a></h3>
					<div class="none"><!--$detail.patient!--></div>
				</div>
			<!--endif!-->
			<!--if($detail.gallery)!-->
				<div class="el-tab">
					<h3><a href="#">Фотографии результатов</a></h3>
					<div class="items none">
						<div class="gallery">
							<div class="gallery-in">
								<!--foreach($detail.gallery as $key=>$val)!-->
									<div class="item">
										<b>До</b>
										<b class="r">После</b>
										<div class="img">
											<img src="<!--$val!-->" alt="До После" title="До После" />
										</div>

									</div>
								<!--endforeach!-->
							</div>
							<a href="#" class="arrow left">Вперед<i></i></a>
							<a href="#" class="arrow right">Назад<i></i></a>

						</div>
					</div>
				</div>
			<!--endif!-->
		</div>
	</div>
	<!--if($detail.similar.production)!-->
		<div class="similars products">
			<div class="title">
				<h2>Другие аппараты производителя</h2>
				<a href="<!--$detail.similar.company_link!-->">Смотреть все</a>
			</div>
			<div class="items products">
				<!--foreach($detail.similar.production as $key=>$val)!-->
					<div class="item product">
						<div class="item-rel">
							<div class="img">
								<div class="img-in">
									<a href="<!--$val.link!-->">
										<!--if($val.img)!-->
											<!--$val.img!-->
										<!--else!-->
											<div class="default">Изображение отсутствует</div>
										<!--endif!-->
									</a>
								</div>
							</div>
							<div class="cols">
								<h4><!--$val.name!--></h4>
								<p><!--$val.preview_text!--></p>
							</div>
						</div>
						<div class="border"></div>
					</div>
				<!--endforeach!-->
			</div>
		</div>
	<!--endif!-->
<!--else!-->
	<div class="not-found">Аппарат не найден ...</div>
<!--endif!-->