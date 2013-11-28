<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<div class="block" rel="news">
	<div class="block-header red">
		<span><?=GetMessage("ESTELIFE_CLINIC")?></span>
		<div class="clear"></div>
	</div>
	<div class="shadow"></div>
	<div class="tab-group" rel="articles">
		<div class="el-items">
		<?php if (!empty($arResult['clinics'])):?>
			<?php foreach ($arResult['clinics'] as $arClinic):?>
				<div class="news-item clinic-list el-item">
					<?php if ($arClinic["recomended"] == 1):?><span class="checkit"></span><?php endif?>
					<div class='item-wrap'>
						<h2 class='clinic-title'><a href="<?=$arClinic["link"]?>" class="el-get-detail"><?=$arClinic["name"]?></a></h2>
						<div class='news-picture'>
							<div>
								<a href="<?=$arClinic["link"]?>" class="el-get-detail">
								<?php if(!empty($arClinic["logo"])): ?>
									<?=$arClinic["logo"]?>
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
										<? if(!empty($arClinic["address"])):?>
											<tr>
												<td><i class='icon address'></i></td>
												<td><?=$arClinic["address"]?></td>
											</tr>
										<? endif; ?>
										<? if (!empty($arClinic["metro_name"])):?>
											<tr>
												<td><i class='icon metro<?=$arClinic["city_id"]?>'></i></td>
												<td><?=$arClinic["metro_name"]?></td>
											</tr>
										<? endif; ?>
										<? if (!empty($arClinic["web"])):?>
											<tr>
												<td><i class='icon link'></i></td>
												<td><a target='_blank' href="<?=$arClinic["web"]?>"><?=$arClinic["web_short"]?></a></td>
											</tr>
										<? endif; ?>
										<? if (!empty($arClinic['phone'])) :?><tr>
											<td><i class='icon phone'></i></td>
											<td><b><?=$arClinic['phone']?></b></td>
											</tr>
										<? endif; ?>
									</table>
								</td>
								<td valign="top" class="profs">
									<?=$arClinic['dop_text']?>
								</td>
							</tr>
						</table>
						<div class='clear'></div>
					</div>
				</div>
			<?php endforeach?>
		<?php endif?>
		</div>

		<div class="el-not-found<?=(!empty($arResult['clinics'])) ? ' none' : ''?>"><?=GetMessage("ESTELIFE_CLINIC_NOT_FOUND")?></div>

		<div class="clear"></div>
		<div class='pagination'>
			<?php if (!empty($arResult['nav'])):?>
				<?=$arResult['nav']?>
			<?php endif; ?>
		</div>
	</div>
</div>