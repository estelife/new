<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="inner">
	<ul class="crumb">
		<li><a href="/">Главная</a></li>
		<li><a href="/training-centers/">Учебные центры</a></li>
		<li><b><?=$arResult['company']['name']?></b></li>
	</ul>
	<div class="wrap_item">
		<div class="item detail company center">
			<h1><?=$arResult['company']['name']?></h1>
			<div class="img">
				<div class="img-in">
					<?if (!empty($arResult['company']['img'])):?>
						<?=$arResult['company']['img']?>
					<?php endif?>
				</div>
			</div>
			<div class="cols col1">
				<?php if (!empty($arResult['company']['address'])):?>
					<span><?=$arResult['company']['address']?></span>
				<?php endif?>
				<?php if (!empty($arResult['company']['contacts']['phone'])):?>
					<span><?=$arResult['company']['contacts']['phone']?></span>
				<?php endif?>
				<?php if (!empty($arResult['company']['web'])):?>
					<a href="<?=$arResult['company']['web']?>"><?=$arResult['company']['web_short']?></a>
				<?php endif?>
			</div>
			<div class="tabs-menu menu_tab">
				<ul>
					<li class="active t1"><a href="#">О центре<i></i></a></li>
					<li class="t2"><a href="#">Текущие семинары<i></i></a></li>
					<li class="t3"><a href="#">Контакты<i></i></a></li>
				</ul>
			</div>
			<div class="tabs tab1 ">
				<?php if (!empty($arResult['company']['gallery'])):?>
					<div class="gallery">
						<div class="gallery-in">
							<?php foreach ($arResult['company']['gallery'] as $val):?>
								<div class="item">
									<div class="img">
										<img src="<?=$val['original']?>" alt="<?=$val['description']?>" title="<?=$val['description']?>" />
									</div>
									<div class="desc">
										<?=$val['description']?>
									</div>
								</div>
							<?php endforeach?>
						</div>
						<div class="gallery-desc">
							<?php if (!empty($val['description'])):?>
								<?=$val['description']?>
							<?php endif?>
						</div>
						<a href="#" class="arrow left">Назад<i></i></a>
						<a href="#" class="arrow right">Вперед<i></i></a>
					</div>
				<?php endif?>
				<p><?=$arResult['company']['detail_text']?></p>
			</div>
			<div class="tabs tab2 none">
				<div class="items">
					<?$APPLICATION->IncludeComponent(
						"estelife:trainings.list",
						"centers_list",
						array(
							"MAKER"=>$arResult['company']['id'],
							"MAKER_NAME"=>$arResult['company']['name'],
							"COMPONENT"=> 'centers_list',
						)
					)?>
				</div>
			</div>
			<div class="tab-c tabs tab3 none">
				<ul>
					<?php if (!empty($arResult['company']['address'])):?>
						<li>
							<b>Адрес</b>
							<span><?=$arResult['company']['address']?></span>
						</li>
					<?php endif?>
					<?php if (!empty($arResult['company']['contacts']['phone'])):?>
					<li>
						<b>Телефон</b>
						<span><?=$arResult['company']['contacts']['phone']?></span>
					</li>
					<?php endif?>
					<?php if (!empty($arResult['company']['web'])):?>
					<li>
						<b>Сайт учебного центра</b>
						<a href="<?=$arResult['company']['web']?>"><?=$arResult['company']['web_short']?></a>
					</li>
					<?php endif?>
					<?php if (!empty($arResult['company']['contacts']['email'])):?>
						<li>
							<b>E-mail</b>
							<span><?=$arResult['company']['contacts']['email']?></span>
						</li>
					<?php endif?>

				</ul>

				<div class="map">
					<span class="lat"><?=$arResult['company']['contacts']['lat']?></span>
					<span class="lng"><?=$arResult['company']['contacts']['lng']?></span>
				</div>
			</div>
		</div>
	</div>
</div>