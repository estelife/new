<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?php
$APPLICATION->SetPageProperty("title", $arResult['SEO']['title']);
$APPLICATION->SetPageProperty("description", $arResult['SEO']['description']);
$APPLICATION->SetPageProperty("keywords", $arResult['SEO']['keywords']);
?>
<div class="block" rel="news">
	<div class="block-header blue">
		<h1><?=GetMessage("ESTELIFE_ACTIONS")?></h1>
		<div class="clear"></div>
	</div>
	<div class="shadow"></div>
	<div class="tab-group" rel="articles">
		<div class="panel">
			<div class="el-items">
			<?php if (!empty($arResult['akzii'])):?>
				<?php foreach ($arResult['akzii'] as $arValue):?>
					<div class="articlee el-item">
						<div class="section big">
							<a href="<?=$arValue['link']?>" class="el-get-detail">
								<div class="h"><?=$arValue["name"]?></div>
								<?php if(!empty($arValue["logo"])):?>
									<img class="photo" src="<?=$arValue['logo']?>" alt="<?=$arValue["name"]?>" title="<?=$arValue["name"]?>" />
								<?endif?>
								<span class="new_price"><?=$arValue['new_price']?> руб.</span>
								<span class="old_price"><span></span><?=$arValue['old_price']?> руб.</span>
								<span class="days"><span></span>Осталось <?=$arValue['time']?> <?=$arValue['day']?></span>
								<span class="discount"><?=$arValue["sale"]?> %</span>
							</a>
						</div>
					</div>
				<?php endforeach?>
			<?php endif?>
			</div>

			<div class="el-not-found<?=(!empty($arResult['akzii']) ? ' none' : '')?>"><?=GetMessage("ESTELIFE_ACTION_NOT_FOUND")?></div>

			<div class="clear"></div>
			<?php if (!empty($arResult['nav'])):?>
				<div class='pagination'>
					<?=$arResult['nav']?>
				</div>
			<?php endif?>
		</div>
	</div>
</div>