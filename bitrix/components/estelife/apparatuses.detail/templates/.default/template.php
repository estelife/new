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
						<?=$arResult['app']['img']?>
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
				<?php if (!empty($arResult['app']['registration'])):?>
					<div class="el-tab">
						<h3><a href="#">Регистрация</a></h3>
						<p class="none"><?=$arResult['app']['registration']?></p>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['app']['action'])):?>
					<div class="el-tab">
						<h3><a href="#">Действие</a></h3>
						<p class="none"><?=$arResult['app']['action']?></p>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['app']['func'])):?>
					<div class="el-tab">
						<h3><a href="#">Функции</a></h3>
						<p class="none"><?=$arResult['app']['func']?></p>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['app']['undesired'])):?>
					<div class="el-tab">
						<h3><a href="#">Побочные действие</a></h3>
						<p class="none"><?=$arResult['app']['undesired']?></p>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['app']['evidence'])):?>
					<div class="el-tab">
						<h3><a href="#">Показания</a></h3>
						<p class="none"><?=$arResult['app']['evidence']?></p>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['app']['procedure'])):?>
					<div class="el-tab">
						<h3><a href="#">Курс процедур</a></h3>
						<p class="none"><?=$arResult['app']['procedure']?></p>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['app']['contra'])):?>
					<div class="el-tab">
						<h3><a href="#">Противопоказания</a></h3>
						<p class="none"><?=$arResult['app']['contra']?></p>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['app']['advantages'])):?>
					<div class="el-tab">
						<h3><a href="#">Преимущества</a></h3>
						<p class="none"><?=$arResult['app']['advantages']?></p>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['app']['security'])):?>
					<div class="el-tab">
						<h3><a href="#">Безопасность</a></h3>
						<p class="none"><?=$arResult['app']['security']?></p>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['app']['protocol'])):?>
					<div class="el-tab">
						<h3><a href="#">Протокол процедуры</a></h3>
						<p class="none"><?=$arResult['app']['protocol']?></p>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['app']['specs'])):?>
					<div class="el-tab">
						<h3><a href="#">Технические характеристики</a></h3>
						<p class="none"><?=$arResult['app']['specs']?></p>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['app']['equipment'])):?>
					<div class="el-tab">
						<h3><a href="#">Комплектация</a></h3>
						<p class="none"><?=$arResult['app']['equipment']?></p>
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
			</div>
		</div>
		<?php if (!empty($arResult['app']['production'])):?>
			<div class="similars products">
				<div class="title">
					<h2>Другие аппараты</h2>
				</div>
				<div class="items">
					<?php foreach ($arResult['app']['production'] as $arValue):?>
						<div class="item product">
							<div class="item-rel">
								<div class="img">
									<div class="img-in">
										<?php if(!empty($arValue["logo_id"])):?>
											<?=$arValue["img"]?>
										<?endif?>
									</div>
								</div>
								<div class="cols">
									<h2><a href="<?=$arValue["link"]?>"><?=$arValue["name"]?></a></h2>
									<ul>
										<li class="country c<?=$arResult['app']['country_id']?>"><?=$arResult['app']['country_name']?></li>
										<li>Производитель: <a href="<?=$arResult['app']['company_link']?>"><?=$arResult['app']['company_name']?></a></li>
									</ul>
									<p><?=$arValue["preview_text"]?></p>
								</div>
							</div>
							<div class="border"></div>
						</div>
					<?php endforeach?>
				</div>
			</div>
		<?php endif?>
	</div>
</div>