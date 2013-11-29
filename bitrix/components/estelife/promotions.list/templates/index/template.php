<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?php if (!empty($arResult['akzii'])):?>
	<div class="full-content">
		<div class="big-block" id="actions">
			<div class="block-header blue">
				<span><?=GetMessage("ESTELIFE_ACTIONS")?></span>
				<div class="clear"></div>
			</div>
			<div class="shadow"></div>
			<div class="tab-group" rel="actions">
				<div class="panel" rel="cosmetologiya">
						<span class="main-block">
							<?php $i = 0?>
							<?php foreach ($arResult['akzii'] as $arValue):?>
								<div class="section <?php if ($i<3):?>big<?php else:?>small<?php endif?>">
									<a href="<?=$arValue['link']?>">
										<div class="h"><?=$arValue["name"]?></div>
										<?php if ($i<3):?>
											<?php if(!empty($arValue["logo_id"])):?>
												<?php $file = CFile::ResizeImageGet($arValue["logo_id"], array('width'=>303, 'height'=>143), BX_RESIZE_IMAGE_EXACT, true);?>
												<img class="photo" src="<?=$file['src']?>" alt="<?=$arValue["name"]?>" title="<?=$arValue["name"]?>" />
											<?endif?>
										<?php else:?>
											<?php if(!empty($arValue["s_logo_id"])):?>
												<?php $file = CFile::ResizeImageGet($arValue["s_logo_id"], array('width'=>223, 'height'=>105), BX_RESIZE_IMAGE_EXACT, true);?>
												<img class="photo" src="<?=$file['src']?>" alt="<?=$arValue["name"]?>" title="<?=$arValue["name"]?>" />
											<?endif?>
										<?php endif?>
										<span class="new_price"><?=intval($arValue['new_price'])?> руб.</span>
										<span class="old_price"><span></span><?=intval($arValue['old_price'])?> руб.</span>
										<span class="days"><span></span>Осталось <?=$arValue['time']?> <?=$arValue['day']?></span>
										<span class="discount"><?=$arValue["sale"]?> %</span>
									</a>
								</div>
								<?php $i++?>
							<?php endforeach?>
						</span>
					<div class="clear"></div>
					<a href="/promotions/" class="view-all blue">Все акции</a>
					<div class="clear"></div>
				</div>
				<div class="clear"></div>
			</div>
		</div>
	</div>
<?php endif?>