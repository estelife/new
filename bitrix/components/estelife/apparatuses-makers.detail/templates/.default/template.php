<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="inner">
	<ul class="crumb">
		<li><a href="/">Главная</a></li>
		<li><a href="/apparatuses-makers/">Производители</a></li>
		<li><b><?=$arResult['company']['name']?></b></li>
	</ul>
	<div class="wrap_item">
		<div class="item detail producer">
			<h1><?=$arResult['company']['name']?></h1>
			<div class="current">
				<div class="img">
					<div class="img-in">
						<?=$arResult['company']['img']?>
					</div>
				</div>
				<ul>
					<?php if (!empty($arResult['company']['country_name'])):?>
						<li class="country c<?=$arResult['company']['country_id']?>"><?=$arResult['company']['country_name']?></li>
					<?php endif?>
					<?php if (!empty($arResult['company']['web'])):?>
						<li><a href="<?=$arResult['company']['web']?>" target="_blank"><?=$arResult['company']['web_short']?></a></li>
					<?php endif?>
				</ul>
			</div>
			<p><?=$arResult['company']['detail_text']?></p>
			<?php if (!empty($arResult['production'])):?>
				<h3>Продукция</h3>
				<div class="items products">
					<?php foreach ($arResult['production'] as $arValue):?>
						<div class="item product">
							<div class="item-rel">
								<div class="img">
									<div class="img-in">
										<a href="<?=$arValue['link']?>">
											<?php if(!empty($arValue["img"])): ?>
												<?=$arValue["img"]?>
											<?php else: ?>
												<div class="default">Изображение отсутствует</div>
											<?endif?>
										</a>
									</div>
								</div>
								<div class="cols">
									<h4><?=$arValue["name"]?></h4>
									<p><?=$arValue["preview_text"]?></p>
								</div>
							</div>
							<div class="border"></div>
						</div>
					<?php endforeach?>
				</div>
			<?php endif?>
		</div>
	</div>
</div>