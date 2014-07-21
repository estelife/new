<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="inner">
	<ul class="crumb">
		<li><a href="/">Главная</a></li>
		<li><a href="/apparatuses/">Аппараты</a></li>
		<li><b><?=$arResult['app']['name']?></b></li>
	</ul>
	<div class="wrap_item">
		<div class="item detail product">
			<h1><?=$arResult['app']['name']?></h1>
			<div class="current">
				<div class="img">
					<div class="img-in">
						<?php if(!empty($arResult['app']['img'])):?>
							<?=$arResult['app']['img']?>
						<?php else: ?>
							<div class="default">Изображение отсутствует</div>
						<?php endif; ?>
					</div>
				</div>
				<ul>
					<?php if (!empty($arResult['app']['country_name'])):?>
						<li class="country c<?=$arResult['app']['country_id']?>"><?=$arResult['app']['country_name']?></li>
					<?php endif?>
					<?php if (!empty($arResult['app']['company_name'])):?>
						<li>Производитель: <a href="<?=$arResult['app']['company_link']?>"><?=$arResult['app']['company_name']?></a></li>
					<?php endif?>
				</ul>
			</div>
			<p><?=$arResult['app']['detail_text']?></p>

			<div class="properties">
				<?php if (!empty($arResult['app']['action'])):?>
					<div class="el-tab">
						<h3><a href="#">Действие</a></h3>
						<div class="text">
							<div class="desc"><?=$arResult['app']['action']?></div>
							<div class="pr_space"></div>
						</div>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['app']['evidence'])):?>
					<div class="el-tab">
						<h3><a href="#">Показания</a></h3>
						<div class="text">
							<div class="desc"><?=$arResult['app']['evidence']?></div>
							<div class="pr_space"></div>
						</div>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['app']['contra'])):?>
					<div class="el-tab">
						<h3><a href="#">Противопоказания</a></h3>
						<div class="text">
							<div class="desc"><?=$arResult['app']['contra']?></div>
							<div class="pr_space"></div>
						</div>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['app']['area'])):?>
					<div class="el-tab">
						<h3><a href="#">Зоны применения</a></h3>
						<div class="text">
							<div class="desc"><?=$arResult['app']['area']?></div>
							<div class="pr_space"></div>
						</div>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['app']['procedure'])):?>
					<div class="el-tab">
						<h3><a href="#">Курс процедур</a></h3>
						<div class="text">
							<div class="desc"><?=$arResult['app']['procedure']?></div>
							<div class="pr_space"></div>
						</div>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['app']['registration']) || !empty($arResult['app']['registration_photo'])):?>
					<div class="el-tab">
						<h3><a href="#">Регистрация</a></h3>
						<div class="text">
							<div class="desc"><?=$arResult['app']['registration']?></div>
							<div class="reg_images">
								<?php foreach ($arResult['app']['registration_photo'] as $val):?>
									<div class="item">
										<div class="item-rel">
											<div class="img">
												<div class="img-in">
													<a href="#" class="reg_photo">
														<?=$val['file'];?>
													</a>
												</div>
											</div>
										</div>
										<div class="border"></div>
									</div>
								<?php endforeach?>
							</div>
							<div class="pr_space"></div>
						</div>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['app']['security'])):?>
					<div class="el-tab">
						<h3><a href="#">Безопасность</a></h3>
						<div class="text">
							<div class="desc"><?=$arResult['app']['security']?></div>
							<div class="pr_space"></div>
						</div>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['app']['effect'])):?>
					<div class="el-tab">
						<h3><a href="#">Достигаемые эффекты</a></h3>
						<div class="text">
							<div class="desc"><?=$arResult['app']['effect']?></div>
							<div class="pr_space"></div>
						</div>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['app']['undesired'])):?>
					<div class="el-tab">
						<h3><a href="#">Побочные эффекты</a></h3>
						<div class="text">
							<div class="desc"><?=$arResult['app']['undesired']?></div>
							<div class="pr_space"></div>
						</div>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['app']['func'])):?>
					<div class="el-tab">
						<h3><a href="#">Функции</a></h3>
						<div class="text">
							<div class="desc"><?=$arResult['app']['func']?></div>
							<div class="pr_space"></div>
						</div>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['app']['advantages'])):?>
					<div class="el-tab">
						<h3><a href="#">Преимущества</a></h3>
						<div class="text">
							<div class="desc"><?=$arResult['app']['advantages']?></div>
							<div class="pr_space"></div>
						</div>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['app']['mix'])):?>
					<div class="el-tab">
						<h3><a href="#">Сочетание</a></h3>
						<div class="text">
							<div class="desc"><?=$arResult['app']['mix']?></div>
							<div class="pr_space"></div>
						</div>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['app']['patient'])):?>
					<div class="el-tab">
						<h3><a href="#">Рекомендации пациенту</a></h3>
						<div class="text">
							<div class="desc"><?=$arResult['app']['patient']?></div>
							<div class="pr_space"></div>
						</div>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['app']['specialist'])):?>
					<div class="el-tab">
						<h3><a href="#">Рекомендации специалисту</a></h3>
						<div class="text">
							<div class="desc"><?=$arResult['app']['specialist']?></div>
							<div class="pr_space"></div>
						</div>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['app']['protocol'])):?>
					<div class="el-tab">
						<h3><a href="#">Протокол процедуры</a></h3>
						<div class="text">
							<div class="desc"><?=$arResult['app']['protocol']?></div>
							<div class="pr_space"></div>
						</div>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['app']['specs'])):?>
					<div class="el-tab">
						<h3><a href="#">Технические характеристики</a></h3>
						<div class="text">
							<div class="desc"><?=$arResult['app']['specs']?></div>
							<div class="pr_space"></div>
						</div>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['app']['rules'])):?>
					<div class="el-tab">
						<h3><a href="#">Правила эксплуатации</a></h3>
						<div class="text">
							<div class="desc"><?=$arResult['app']['rules']?></div>
							<div class="pr_space"></div>
						</div>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['app']['equipment'])):?>
					<div class="el-tab">
						<h3><a href="#">Комплектация</a></h3>
						<div class="text">
							<div class="desc"><?=$arResult['app']['equipment']?></div>
							<div class="pr_space"></div>
						</div>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['app']['acs'])):?>
					<div class="el-tab">
						<h3><a href="#">Аксессуары</a></h3>
						<div class="text">
							<div class="desc"><?=$arResult['app']['acs']?></div>
							<div class="pr_space"></div>
						</div>
					</div>
				<?php endif?>



				<?php if (!empty($arResult['app']['gallery'])):?>
					<div class="el-tab">
						<h3><a href="#">Фотографии результатов</a></h3>
						<div class="items none">
							<div class="gallery">
								<div class="gallery-in">
									<?php foreach ($arResult['app']['gallery'] as $val):?>
										<div class="item">
											<b>До</b>
											<b class="r">После</b>
											<div class="img">
												<img src="<?=$val?>" alt="До После" title="До После" />
											</div>

										</div>
									<?php endforeach?>
								</div>
								<a href="#" class="arrow left">Вперед<i></i></a>
								<a href="#" class="arrow right">Назад<i></i></a>

							</div>
						</div>
					</div>
				<?php endif?>
				<div class="pr_space"></div>
			</div>
			<div class="attention">
				Данная информация не является рекламой, представляет собой справочный материал и предназначена исключительно для специалистов с целью ознакомления пациентов с аналогами, имеющимися в обращении в РФ в соответствии с пп.4 ст.74 ФЗ «Об охране здоровья граждан»
			</div>
		</div>
		<?$APPLICATION->IncludeComponent(
			"estelife:apparatuses.list",
			"similar_list",
			array(
				"MAKER"=>$arResult['app']['company_id'],
				"MAKER_LINK"=> $arResult['app']['company_link'],
				"COMPONENT"=> 'similar_list',
				"PREP_ID" => $arResult['app']['id'],
			)
		)?>
	</div>
</div>