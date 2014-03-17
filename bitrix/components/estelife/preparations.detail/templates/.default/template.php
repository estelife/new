<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="inner">
	<ul class="crumb">
		<li><a href="/">Главная</a></li>
		<li><a href="<?=$arResult['type_link']?>"><?=$arResult['type']?></a></li>
		<li><b><?=$arResult['pill']['name']?></b></li>
	</ul>
	<div class="wrap_item">
		<div class="item detail product">
			<h1><?=$arResult['pill']['name']?></h1>
			<div class="current">
				<div class="img">
					<div class="img-in">
						<?php if(!empty($arResult['pill']['img'])): ?>
							<?=$arResult['pill']['img']?>
						<?php else: ?>
							<div class="default">Изображение отсутствует</div>
						<?php endif; ?>
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
				<?php if (!empty($arResult['pill']['action'])):?>
					<div class="el-tab">
						<h3><a href="#">Действие</a></h3>
						<p class="none"><?=$arResult['pill']['action']?></p>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['pill']['evidence'])):?>
					<div class="el-tab">
						<h3><a href="#">Показания</a></h3>
						<p class="none"><?=$arResult['pill']['evidence']?></p>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['pill']['contra'])):?>
					<div class="el-tab">
						<h3><a href="#">Противопоказания</a></h3>
						<p class="none"><?=$arResult['pill']['contra']?></p>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['pill']['structure'])):?>
					<div class="el-tab">
						<h3><a href="#">Состав</a></h3>
						<p class="none"><?=$arResult['pill']['structure']?></p>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['pill']['registration'])):?>
					<div class="el-tab">
						<h3><a href="#">Регистрация</a></h3>
						<p class="none"><?=$arResult['pill']['registration']?></p>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['pill']['advantages'])):?>
					<div class="el-tab">
						<h3><a href="#">Преимущества</a></h3>
						<p class="none"><?=$arResult['pill']['advantages']?></p>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['pill']['usage'])):?>
					<div class="el-tab">
						<h3><a href="#">Курс лечения</a></h3>
						<p class="none"><?=$arResult['pill']['usage']?></p>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['pill']['area'])):?>
					<div class="el-tab">
						<h3><a href="#">Зоны применения</a></h3>
						<p class="none"><?=$arResult['pill']['area']?></p>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['pill']['effect'])):?>
					<div class="el-tab">
						<h3><a href="#">Достигаемый эффект</a></h3>
						<p class="none"><?=$arResult['pill']['effect']?></p>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['pill']['undesired'])):?>
					<div class="el-tab">
						<h3><a href="#">Побочные эффекты</a></h3>
						<p class="none"><?=$arResult['pill']['undesired']?></p>
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
				<?php if (!empty($arResult['pill']['specs'])):?>
					<div class="el-tab">
						<h3><a href="#">Технические характеристики</a></h3>
						<p class="none"><?=$arResult['pill']['specs']?></p>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['pill']['protocol'])):?>
					<div class="el-tab">
						<h3><a href="#">Протокол процедуры</a></h3>
						<p class="none"><?=$arResult['pill']['protocol']?></p>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['pill']['form'])):?>
					<div class="el-tab">
						<h3><a href="#">Форма выпуска</a></h3>
						<p class="none"><?=$arResult['pill']['form']?></p>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['pill']['storage'])):?>
					<div class="el-tab">
						<h3><a href="#">Условия хранения</a></h3>
						<p class="none"><?=$arResult['pill']['storage']?></p>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['pill']['specialist'])):?>
					<div class="el-tab">
						<h3><a href="#">Рекомендации специалисту</a></h3>
						<p class="none"><?=$arResult['pill']['specialist']?></p>
					</div>
				<?php endif?>
				<?php if (!empty($arResult['pill']['patient'])):?>
					<div class="el-tab">
						<h3><a href="#">Рекомендации пациенту</a></h3>
						<p class="none"><?=$arResult['pill']['patient']?></p>
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
		<?$APPLICATION->IncludeComponent(
			"estelife:preparations.list",
			"similar_list",
			array(
				"MAKER"=>$arResult['pill']['company_id'],
				"MAKER_LINK"=> $arResult['pill']['company_link'],
				"COMPONENT"=> 'similar_list',
				"PREP_ID" => $arResult['pill']['id'],
			)
		)?>
	</div>
</div>
