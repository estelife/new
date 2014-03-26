<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?php if (!empty($arResult['similar_apps']['production'])):?>
	<div class="similars products">
		<div class="title">
			<h2>Другие аппараты производителя</h2>
			<a href="<?=$arResult['similar_apps']['company_link']?>">Смотреть все</a>
		</div>
		<div class="items products">
			<?php foreach ($arResult['similar_apps']['production'] as $arValue):?>
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
	</div>
<?php endif?>