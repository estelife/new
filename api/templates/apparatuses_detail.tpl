<!--if($detail)!-->
	<div class="item detail product">
		<h1><!--$detail.name!--></h1>
		<div class="current">
			<div class="img">
				<div class="img-in">
					<!--$detail.img!-->
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
			<!--if($detail.registration)!-->
				<div class="el-tab">
					<h3><a href="#">Регистрация</a></h3>
					<p class="none"><!--$detail.registration!--></p>
				</div>
			<!--endif!-->
			<!--if($detail.action)!-->
				<div class="el-tab">
					<h3><a href="#">Действие</a></h3>
					<p class="none"><!--$detail.action!--></p>
				</div>
			<!--endif!-->
			<!--if($detail.func)!-->
				<div class="el-tab">
					<h3><a href="#">Функции</a></h3>
					<p class="none"><!--$detail.func!--></p>
				</div>
			<!--endif!-->
			<!--if($detail.undesired)!-->
				<div class="el-tab">
					<h3><a href="#">Побочные действие</a></h3>
					<p class="none"><!--$detail.undesired!--></p>
				</div>
			<!--endif!-->
			<!--if($detail.evidence)!-->
				<div class="el-tab">
					<h3><a href="#">Показания</a></h3>
					<p class="none"><!--$detail.evidence!--></p>
				</div>
			<!--endif!-->
			<!--if($detail.procedure)!-->
				<div class="el-tab">
					<h3><a href="#">Курс процедур</a></h3>
					<p class="none"><!--$detail.procedure!--></p>
				</div>
			<!--endif!-->
			<!--if($detail.contra)!-->
				<div class="el-tab">
					<h3><a href="#">Противопоказания</a></h3>
					<p class="none"><!--$detail.contra!--></p>
				</div>
			<!--endif!-->
			<!--if($detail.advantages)!-->
				<div class="el-tab">
					<h3><a href="#">Преимущества</a></h3>
					<p class="none"><!--$detail.advantages!--></p>
				</div>
			<!--endif!-->
			<!--if($detail.security)!-->
				<div class="el-tab">
					<h3><a href="#">Безопасность</a></h3>
					<p class="none"><!--$detail.security!--></p>
				</div>
			<!--endif!-->
			<!--if($detail.protocol)!-->
				<div class="el-tab">
					<h3><a href="#">Протокол процедуры</a></h3>
					<p class="none"><!--$detail.protocol!--></p>
				</div>
			<!--endif!-->
			<!--if($detail.specs)!-->
				<div class="el-tab">
					<h3><a href="#">Технические характеристики</a></h3>
					<p class="none"><!--$detail.specs!--></p>
				</div>
			<!--endif!-->
			<!--if($detail.equipment)!-->
				<div class="el-tab">
					<h3><a href="#">Комплектация</a></h3>
					<p class="none"><!--$detail.equipment!--></p>
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
	<!--if($detail.production)!-->
		<div class="similars products">
			<div class="title">
				<h2>Другие аппараты</h2>
			</div>
			<div class="items">
				<!--foreach($detail.production as $key=>$val)!-->
					<div class="item product">
						<div class="item-rel">
							<div class="img">
								<div class="img-in">
									<!--if($val.logo_id)!-->
										<!--$val.img!-->
									<!--endif!-->
								</div>
							</div>
							<div class="cols">
								<h2><a href="<!--$val.link!-->"><!--$val.name!--></a></h2>
								<ul>
									<li class="country c<!--$detail.country_id!-->"><!--$detail.country_name!--></li>
									<li>Производитель: <a href="<!--$detail.company_link!-->"><!--$detail.company_name!--></a></li>
								</ul>
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