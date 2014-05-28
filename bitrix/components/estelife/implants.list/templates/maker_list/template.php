<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?php if (!empty($arResult['company']['production'])):?>
	<h3>Продукция</h3>
	<div class="items products">
		<?php foreach ($arResult['company']['production'] as $arValue):?>
			<div class="item product">
				<div class="item-rel">
					<div class="img">
						<div class="img-in">
							<?php if(!empty($arValue["img"])): ?>
								<?=$arValue["img"]?>
							<?php else:?>
								<div class="default">Изображение отсутствует</div>
							<?endif?>
						</div>
					</div>
					<div class="cols">
						<h4><a href="<?=$arValue['link']?>"><?=$arValue["name"]?></a></h4>
						<p><?=$arValue["preview_text"]?></p>
					</div>
				</div>
				<div class="border"></div>
			</div>
		<?php endforeach?>
	</div>
<?php endif?>