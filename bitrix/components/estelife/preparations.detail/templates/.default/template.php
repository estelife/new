<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="inner">
	<ul class="crumb">
		<li><a href="/">Главная</a></li>
		<li><a href="/preparations/">Препараты</a></li>
		<li><b><?=$arResult['pill']['name']?></b></li>
	</ul>
	<div class="item detail product">
		<h1><?=$arResult['pill']['name']?></h1>
		<div class="current">
			<div class="img">
				<div class="img-in">
					<?=$arResult['pill']['img']?>
				</div>
			</div>
			<ul>
				<?php if (!empty($arResult['pill']['country_name'])):?>
					<li class="country c<?=$arResult['pill']['country_id']?>"><?=$arResult['pill']['country_name']?></li>
				<?php endif?>
				<?php if (!empty($arResult['pill']['company_name'])):?>
					<li>Производитель: <a href="<?=$arResult['pill']['company_link']?>"><?=$arResult['pill']['company_name']?></a></li>
				<?php endif?>
			</ul>
		</div>
		<p><?=$arResult['pill']['detail_text']?></p>

		<div class="properties">
			<?php if (!empty($arResult['pill']['registration'])):?>
				<div class="el-tab">
					<h3><a href="#">Регистрация</a></h3>
					<p class="none"><?=$arResult['pill']['registration']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['pill']['action'])):?>
				<div class="el-tab">
					<h3><a href="#">Действие</a></h3>
					<p class="none"><?=$arResult['pill']['action']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['pill']['undesired'])):?>
				<div class="el-tab">
					<h3><a href="#">Побочные действия</a></h3>
					<p class="none"><?=$arResult['pill']['undesired']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['pill']['evidence'])):?>
				<div class="el-tab">
					<h3><a href="#">Показания</a></h3>
					<p class="none"><?=$arResult['pill']['evidence']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['pill']['structure'])):?>
				<div class="el-tab">
					<h3><a href="#">Состав</a></h3>
					<p class="none"><?=$arResult['pill']['structure']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['pill']['effect'])):?>
				<div class="el-tab">
					<h3><a href="#">Достигаемый эффект</a></h3>
					<p class="none"><?=$arResult['pill']['effect']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['pill']['form'])):?>
				<div class="el-tab">
					<h3><a href="#">Форма выпуска</a></h3>
					<p class="none"><?=$arResult['pill']['form']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['pill']['contra'])):?>
				<div class="el-tab">
					<h3><a href="#">Противопоказания</a></h3>
					<p class="none"><?=$arResult['pill']['contra']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['pill']['usage'])):?>
				<div class="el-tab">
					<h3><a href="#">Способ применения</a></h3>
					<p class="none"><?=$arResult['pill']['usage']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['pill']['storage'])):?>
				<div class="el-tab">
					<h3><a href="#">Условия хранения</a></h3>
					<p class="none"><?=$arResult['pill']['storage']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['pill']['advantages'])):?>
				<div class="el-tab">
					<h3><a href="#">Преимущества</a></h3>
					<p class="none"><?=$arResult['pill']['advantages']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['pill']['area'])):?>
				<div class="el-tab">
					<h3><a href="#">Зоны применения</a></h3>
					<p class="none"><?=$arResult['pill']['area']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['pill']['security'])):?>
				<div class="el-tab">
					<h3><a href="#">Безопасность</a></h3>
					<p class="none"><?=$arResult['pill']['security']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['pill']['mix'])):?>
				<div class="el-tab">
					<h3><a href="#">Сочетание</a></h3>
					<p class="none"><?=$arResult['pill']['mix']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['pill']['protocol'])):?>
				<div class="el-tab">
					<h3><a href="#">Протокол процедуры</a></h3>
					<p class="none"><?=$arResult['pill']['protocol']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['pill']['specs'])):?>
				<div class="el-tab">
					<h3><a href="#">Технические характеристики</a></h3>
					<p class="none"><?=$arResult['pill']['specs']?></p>
				</div>
			<?php endif?>


			<?php if (!empty($arResult['pill']['gallery'])):?>
				<div class="el-tab">
					<h3><a href="#">Фотографии результатов</a></h3>
					<div class="items none">
						<div class="gallery">
							<div class="gallery-in">
								<?php foreach ($arResult['pill']['gallery'] as $val):?>
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
	<?php if (!empty($arResult['pill']['production'])):?>
		<div class="similars products">
			<div class="title">
				<h2>Другие препараты</h2>
			</div>
			<div class="items">
				<?php foreach ($arResult['pill']['production'] as $arValue):?>
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
									<li class="country c<?=$arResult['pill']['country_id']?>"><?=$arResult['pill']['country_name']?></li>
									<li>Производитель: <a href="<?=$arResult['pill']['company_link']?>"><?=$arResult['pill']['company_name']?></a></li>
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
