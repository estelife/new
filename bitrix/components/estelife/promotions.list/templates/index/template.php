<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?php if (!empty($arResult['akzii'])):?>
	<div class="promotions announces">
		<div class="title">
			<h2>Акции клиник</h2>
			<a href="<?=$arResult['link']?>">Больше акций</a>
			<a href="#" class="arrow black bottom"><?=$arResult['city']['NAME']?><i></i></a>
			<div class="cities none">
				<div class="content">
					<div class="cities-in">
						<div class="cols col1">
							<h4>Выберите город</h4>
							<ul>
								<li><a href="">Москва</a></li>
								<li class="active"><a href="#">Санкт-Петербург</a></li>
							</ul>
						</div>
						<div class="cols col2">
							<h4>Скоро с нами:</h4>
							<ul>
								<li>Новосибирск</li>
								<li>Екатеринбург</li>
								<li>Нижний Новгород</li>
								<li>Казань</li>
								<li>Самара</li>
								<li>Омск</li>
								<li>Челябинск</li>
							</ul>
							<ul>
								<li>Ростов-на-Дону</li>
								<li>Уфа</li>
								<li>Волгоград</li>
								<li>Красноярск</li>
								<li>Пермь</li>
								<li>Воронеж</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="items">
			<?php foreach ($arResult['akzii'] as $arValue):?>
				<div class="item promotion">
					<span class="perc"><?=$arValue["sale"]?>%</span>
					<a href="<?=$arValue['link']?>">
						<img src="<?=$arValue['img']['SRC']?>" width="227px" height="158px" alt="<?=$arValue['name']?>" title="<?=$arValue['name']?>" />
					</a>
					<h3><?=$arValue['name']?></h3>
					<div class="cols prices">
						<b><?=$arValue['new_price']?> <i></i></b>
						<s><?=$arValue['old_price']?> <i></i></s>
					</div>
					<div class="cols time">
						<?=$arValue['time']?> <?=$arValue['day']?>
						<i></i>
					</div>
				</div>
			<?php endforeach?>
		</div>
	</div>
<?php endif?>