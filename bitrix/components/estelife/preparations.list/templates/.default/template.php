<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<div class="block" rel="news">
	<div class="block-header blue">
		<span><?=GetMessage("ESTELIFE_PILLS")?></span>
		<div class="clear"></div>
	</div>
	<div class="shadow"></div>
	<div class="tab-group" rel="articles">
		<div class="el-items">
		<?php if (!empty($arResult['pills'])):?>
			<?php foreach ($arResult['pills'] as $arPill):?>
				<div class="news-item clinic-list el-item">
					<div class='item-wrap'>
						<b class='clinic-title'><a href="<?=$arPill["link"]?>" class="el-get-detail"><?=$arPill["name"]?></a></b>
						<b class='news-picture'>
							<a href="<?=$arPill['link']?>" class="el-get-detail">
							<?php if(!empty($arPill["logo"])):?>
								<?=$arPill["logo"]?>
							<?endif?>
							</a>
						</b>
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
										<? if (!empty($arPill["company_name"])):?>
											<tr>
												<td><i class="icon company"></i></td>
												<td><a href="<?=$arPill["company_link"]?>"><?=$arPill["company_name"]?></a></td>
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
		<?php endif?>
		</div>

		<div class="el-not-found<?=(!empty($arResult['pills'])) ? ' none' : ''?>"><?=GetMessage("ESTELIFE_PILLS_NOT_FOUND")?></div>

		<div class="clear"></div>
		<div class='pagination'>
			<?php if (!empty($arResult['nav'])):?>
				<?=$arResult['nav']?>
			<?php endif; ?>
		</div>
	</div>
</div>


