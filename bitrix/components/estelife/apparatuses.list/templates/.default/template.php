<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<div class="block" rel="news">
	<div class="block-header red">
		<h1><?=GetMessage("ESTELIFE_APPS")?></h1>
		<div class="clear"></div>
	</div>
	<div class="shadow"></div>
	<div class="tab-group" rel="articles">
		<div class="el-items">
		<?php if (!empty($arResult['apps'])):?>
			<?php foreach ($arResult['apps'] as $arApp):?>
				<div class="news-item clinic-list el-item">
					<div class='item-wrap'>
						<b class='clinic-title'><a href="<?=$arApp["link"]?>" class="el-get-detail"><?=$arApp["name"]?></a></b>
						<b class='news-picture'>
							<a href="<?=$arApp['link']?>" class="el-get-detail">
							<?php if(!empty($arApp["logo"])):?>
								<?=$arApp["logo"]?>
							<?endif?>
							</a>
						</b>
						<table class='clinic-table float'>
							<tr>
								<td valign="top">
									<table class='data'>
										<? if(!empty($arApp["country_name"])):?>
											<tr>
												<td><i class='icon' style="background:url('/img/countries/c<?=$arApp["country_id"]?>.png')"></i></td>
												<td><?=$arApp["country_name"]?></td>
											</tr>
										<? endif; ?>
										<? if (!empty($arApp["company_name"])):?>
											<tr>
												<td><i class="icon company"></i></td>
												<td><a href="<?=$arApp["company_link"]?>"><?=$arApp["company_name"]?></a></td>
											</tr>
										<? endif; ?>
									</table>
								</td>
								<td valign="top" class="about">
									<?=$arApp['preview_text']?>
								</td>
							</tr>
						</table>
						<div class='clear'></div>
					</div>
				</div>
			<?php endforeach?>
		<?php endif?>
		</div>

		<div class="el-not-found<?=(empty($arResult['pills'])) ? ' none' : ''?>"><?=GetMessage("ESTELIFE_APPS_NOT_FOUND")?></div>

		<div class="clear"></div>
		<div class='pagination'>
			<?php if (!empty($arResult['nav'])):?>
				<?=$arResult['nav']?>
			<?php endif; ?>
		</div>
	</div>
</div>