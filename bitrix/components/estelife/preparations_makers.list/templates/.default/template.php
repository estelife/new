<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<div class="block" rel="news">
	<div class="block-header blue">
		<h1><?=GetMessage("ESTELIFE_PILLS")?></h1>
		<div class="clear"></div>
	</div>
	<div class="shadow"></div>
	<div class="tab-group" rel="articles">
		<div class="el-items">
		<?php if (!empty($arResult['pills'])):?>
			<?php foreach ($arResult['pills'] as $arPill):?>
				<div class="news-item clinic-list el-item">
					<div class='item-wrap'>
						<h2 class='clinic-title'><a href="<?=$arPill["link"]?>" class="el-get-detail"><?=$arPill["name"]?></a></h2>
						<div class='news-picture'>
							<div>
								<a href="<?=$arPill["link"]?>" class="el-get-detail">
								<?php if(!empty($arPill["logo_id"])):?>
									<?=$arPill['img']?>
								<?endif?>
								</a>
							</div>
						</div>
						<table class='clinic-table float'>
							<tr>
								<td valign="top">
									<table class='data'>
										<? if(!empty($arPill["country_name"])):?>
											<tr>
												<td><i class='icon' style="background:url('/img/countries/c<?=$arPill["country_id"]?>.png')"></i></td>
												<td><?=$arPill["country_name"]?></td>
											</tr>
										<? endif; ?>
										<? if (!empty($arPill["web"])):?>
											<tr>
												<td><i class='icon link'></i></td>
												<td><a target='_blank' href="<?=$arPill["web"]?>"><?=$arPill["web_short"]?></a></td>
											</tr>
										<? endif; ?>
									</table>
								</td>
								<td valign="top" class="about">
									<?=$arPill['preview_text']?>
								</td>
							</tr>
						</table>
						<div class='clear'></div>
					</div>
				</div>
			<?php endforeach?>
			<div class="clear"></div>
		<?php endif;?>
		</div>
		<div class="el-not-found<?=(!empty($arResult['pills']) ? ' none' : '')?>"><?=GetMessage("ESTELIFE_PILLS_NOT_FOUND")?></div>
		<div class='pagination'>
		<?php if (!empty($arResult['nav'])):?>
			<?=$arResult['nav']?>
		<?php endif?>
		</div>
	</div>
</div>