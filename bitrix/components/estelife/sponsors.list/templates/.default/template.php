<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<div class="block" rel="news">
	<div class="block-header red">
		<h1><?=GetMessage("ESTELIFE_ORG")?></h1>
		<div class="clear"></div>
	</div>
	<div class="shadow"></div>
	<div class="tab-group" rel="articles">
		<div class="el-items">
		<?php if (!empty($arResult['org'])):?>
			<?php foreach ($arResult['org'] as $arOrg):?>
				<div class="news-item clinic-list el-item">
					<div class='item-wrap'>
						<h2 class='clinic-title'><a href="<?=$arOrg["link"]?>" class="el-get-detail"><?=$arOrg["name"]?></a></h2>
						<div class='news-picture'>
							<div>
							<a href="<?=$arOrg["link"]?>" class="el-get-detail">
							<?php if(!empty($arOrg["logo_id"])):?>
								<?=$arOrg['img']?>
							<?endif?>
							</a>
							</div>
						</div>
						<table class='clinic-table float'>
							<tr>
								<td valign="top">
									<table class='data'>
										<? if(!empty($arOrg["address"])):?>
											<tr>
												<td><i class='icon address'></i></td>
												<td><?=$arOrg["address"]?></td>
											</tr>
										<? endif; ?>
										<? if (!empty($arOrg["web"])):?>
											<tr>
												<td><i class='icon link'></i></td>
												<td><a target='_blank' href="<?=$arOrg["web"]?>"><?=$arOrg["short_web"]?></a></td>
											</tr>
										<? endif; ?>
									</table>
								</td>
								<td valign="top" class="about">
								</td>
							</tr>
						</table>
						<div class='clear'></div>
					</div>
				</div>
			<?php endforeach?>
		<?php endif; ?>
		</div>

		<div class="el-not-found<?=(empty($arResult['org']) ? ' none' : '')?>"><?=GetMessage("ESTELIFE_ORG_NOT_FOUND")?></div>
		<div class='clear'></div>

		<div class='pagination'>
			<?php if (!empty($arResult['nav'])):?>
				<?=$arResult['nav']?>
			<?php endif?>
		</div>
	</div>
</div>