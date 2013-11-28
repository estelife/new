<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<div class="block" rel="news">
	<div class="block-header red">
		<h1><?=GetMessage("ESTELIFE_APPARATUS")?></h1>
		<div class="clear"></div>
	</div>
	<div class="shadow"></div>
	<div class="tab-group" rel="articles">
		<div class="el-items">
		<?php if (!empty($arResult['apparatus'])):?>
			<?php foreach ($arResult['apparatus'] as $arApp):?>
				<div class="news-item clinic-list el-item">
					<div class='item-wrap'>
						<h2 class='clinic-title'><a href="<?=$arApp["link"]?>" class="el-get-detail"><?=$arApp["name"]?></a></h2>
						<div class='news-picture'>
							<div>
							<a href="<?=$arApp["link"]?>" class="el-get-detail">
							<?php if(!empty($arApp["logo_id"])):?>
								<?=$arApp['img']?>
							<?php else: ?>
								<img src="/img/icon/unlogo.png" />
							<?endif?>
							</a>
							</div>
						</div>
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
										<? if (!empty($arApp["web"])):?>
											<tr>
												<td><i class='icon link'></i></td>
												<td><a target='_blank' href="<?=$arApp["web"]?>"><?=$arApp["web_short"]?></a></td>
											</tr>
										<? endif; ?>
									</table>
								</td>
								<td valign="top" class="about">
									<?=html_entity_decode($arApp['preview_text'],ENT_QUOTES,'UTF-8')?>
								</td>
							</tr>
						</table>
						<div class='clear'></div>
					</div>
				</div>
			<?php endforeach?>
			<div class='clear'></div>
		<?php endif; ?>
		</div>
		<div class="el-not-found<?=(!empty($arResult['apparatus']) ? ' none' : '')?>"><?=GetMessage("ESTELIFE_APPARATUS_NOT_FOUND")?></div>
		<div class='pagination'>
			<?php if (!empty($arResult['nav'])):?>
				<?=$arResult['nav']?>
			<?php endif?>
		</div>
	</div>
</div>