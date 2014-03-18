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
					<p class="none"><!--$detail.action!--></p>
				</div>
			<!--endif!-->
			<!--if($detail.evidence)!-->
				<div class="el-tab">
					<h3><a href="#">Показания</a></h3>
					<p class="none"><!--$detail.evidence!--></p>
				</div>
			<!--endif!-->
			<!--if($detail.contra)!-->
				<div class="el-tab">
					<h3><a href="#">Противопоказания</a></h3>
					<p class="none"><!--$detail.contra!--></p>
				</div>
			<!--endif!-->
			<!--if($detail.structure)!-->
				<div class="el-tab">
					<h3><a href="#">Состав</a></h3>
					<p class="none"><!--$detail.structure!--></p>
				</div>
			<!--endif!-->
			<!--if($detail.registration)!-->
				<div class="el-tab">
					<h3><a href="#">Регистрация</a></h3>
					<p class="none"><!--$detail.registration!--></p>
				</div>
			<!--endif!-->
			<!--if($detail.advantages)!-->
				<div class="el-tab">
					<h3><a href="#">Преимущества</a></h3>
					<p class="none"><!--$detail.advantages!--></p>
				</div>
			<!--endif!-->
			<!--if($detail.usage)!-->
				<div class="el-tab">
					<h3><a href="#">Курс лечения</a></h3>
					<p class="none"><!--$detail.usage!--></p>
				</div>
			<!--endif!-->
			<!--if($detail.area)!-->
				<div class="el-tab">
					<h3><a href="#">Зоны применения</a></h3>
					<p class="none"><!--$detail.area!--></p>
				</div>
			<!--endif!-->
			<!--if($detail.effect)!-->
				<div class="el-tab">
					<h3><a href="#">Достигаемый эффект</a></h3>
					<p class="none"><!--$detail.effect!--></p>
				</div>
			<!--endif!-->
			<!--if($detail.undesired)!-->
				<div class="el-tab">
					<h3><a href="#">Побочные эффекты</a></h3>
					<p class="none"><!--$detail.undesired!--></p>
				</div>
			<!--endif!-->
			<!--if($detail.security)!-->
				<div class="el-tab">
					<h3><a href="#">Безопасность</a></h3>
					<p class="none"><!--$detail.security!--></p>
				</div>
			<!--endif!-->
			<!--if($detail.mix)!-->
				<div class="el-tab">
					<h3><a href="#">Сочетание</a></h3>
					<p class="none"><!--$detail.mix!--></p>
				</div>
			<!--endif!-->
			<!--if($detail.specs)!-->
				<div class="el-tab">
					<h3><a href="#">Технические характеристики</a></h3>
					<p class="none"><!--$detail.specs!--></p>
				</div>
			<!--endif!-->
			<!--if($detail.protocol)!-->
				<div class="el-tab">
					<h3><a href="#">Протокол процедуры</a></h3>
					<p class="none"><!--$detail.protocol!--></p>
				</div>
			<!--endif!-->
			<!--if($detail.form)!-->
				<div class="el-tab">
					<h3><a href="#">Форма выпуска</a></h3>
					<p class="none"><!--$detail.form!--></p>
				</div>
			<!--endif!-->
			<!--if($detail.storage)!-->
				<div class="el-tab">
					<h3><a href="#">Условия хранения</a></h3>
					<p class="none"><!--$detail.storage!--></p>
				</div>
			<!--endif!-->
			<!--if($detail.gallery)!-->
				<div class="el-tab">
					<h3><a href="#">Фотографии результатов</a></h3>
					<div class="items none">
						<div class="gallery">
							<div class="gallery-in">
								<!--foreach ($detail.gallery as $key=>$val)!-->
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
	<!--if($detail.similar)!-->
		<div class="similars products">
			<div class="title">
				<h2>Другие препараты производителя</h2>
				<a href="<!--$detail.similar.company_link!-->">Смотреть все</a>
			</div>
			<div class="items products">
				<!--foreach ($detail.similar.production as $key=>$val)!-->
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
	<div class="not-found">Препарат не найден ...</div>
<!--endif!-->