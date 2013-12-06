<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<div class="block" rel="news">
	<div class="block-header blue">
		<h1><?=GetMessage("ESTELIFE_TRAINING")?></h1>
		<div class="clear"></div>
	</div>
	<div class="shadow"></div>
	<div class="tab-group" rel="articles">
		<div class="el-items">
		<?php if (!empty($arResult['training'])):?>
			<?php foreach ($arResult['training'] as $arTraining):?>
				<div class="news-item clinic-list el-item"><div class='item-wrap'>
						<h2 class="clinic-title"><a href="<?=$arTraining["link"]?>" class="el-get-detail"><?=$arTraining["short_name"]?></a></h2>
						<div class='news-picture'>
							<div>
								<a href="<?=$arTraining["link"]?>" class="el-get-detail">
								<?php if(!empty($arTraining["logo"])):?>
									<?=$arTraining['logo']?>
								<?endif?>
								</a>
							</div>
						</div>
						<table class='clinic-table float'>
							<tr>
								<td valign="top">
									<table class='data'>
										<? if(!empty($arTraining["address"])):?>
											<tr>
												<td><i class='icon address'></i></td>
												<td>г. <?=$arTraining["city_name"]?>, <?=$arTraining["address"]?></td>
											</tr>
										<?php endif?>

										<? if (!empty($arTraining["company_name"])):?>
											<tr>
												<td><i class="icon company"></i></td>
												<td><?=$arTraining["company_name"]?></td>
											</tr>
										<? endif; ?>

										<? if(!empty($arTraining["web"])):?>
											<tr>
												<td><i class='icon link'></i></td>
												<td><a target='_blank' href="<?=$arTraining["web"]?>"><?=$arTraining["web_short"]?></a></td>
											</tr>
										<?php endif?>

										<? if(!empty($arTraining["phone"])):?>
											<tr>
												<td><i class='icon phone'></i></td>
												<td><b><?=$arTraining['phone']?></b></td>
											</tr>
										<?php endif?>
									</table>
								</td>
								<td valign="top" class="profs">
									<?php if (!empty($arTraining['calendar'])):?>
										<table class="data">
											<?php foreach ($arTraining['calendar'] as $val):?>
												<tr>
													<td><i class="icon calendar"></i></td>
													<td>
														<?=$val['from']?>
														<?php if(!empty($val['to'])):?>
															-
															<?=$val['to']?>
														<?php endif; ?>
													</td>
												</tr>
											<?php endforeach?>
										</table>
									<?php endif?>
								</td>
							</tr>
							<tr>
								<td colspan="2" class="preview_table">
								</td>
							</tr>
						</table>
						<div class='clear'></div>
					</div>
				</div>
			<?php endforeach?>
		<?php endif?>
		</div>

		<div class="el-not-found<?=(!empty($arResult['training']) ? ' none' : '')?>"><?=GetMessage("ESTELIFE_TRAINING_NOT_FOUND")?></div>

		<div class='clear'></div>
		<?php if (!empty($arResult['nav'])):?>
			<div class='pagination'>
				<?=$arResult['nav']?>
			</div>
		<?php endif?>
	</div>
</div>